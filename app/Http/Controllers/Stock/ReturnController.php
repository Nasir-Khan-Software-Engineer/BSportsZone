<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\ProductReturn;
use App\Models\ReturnItem;
use App\Models\ReturnPayment;
use App\Models\Customer;
use App\Models\Sales;
use App\Models\Sales_items;
use Illuminate\Support\Facades\DB;
use Exception;

class ReturnController extends Controller
{
    public function index()
    {
        return view('stock.return.index');
    }

    public function datatable(Request $request)
    {
        try {
            $POSID = auth()->user()->POSID;
            $search = $request->input('search.value', '');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderColumn = $request->input('order.0.column', 0);
            $orderDir = $request->input('order.0.dir', 'desc');

            $query = ProductReturn::where('POSID', $POSID)
                ->with(['customer', 'sale', 'creator']);

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('reason', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone1', 'like', "%{$search}%");
                      })
                      ->orWhereHas('sale', function($q) use ($search) {
                          $q->where('invoice_code', 'like', "%{$search}%");
                      });
                });
            }

            $totalRecord = ProductReturn::where('POSID', $POSID)->count();
            $filteredRecord = $query->count();

            // Define order columns
            $columns = [
                0 => 'id',
                1 => 'created_at',
                2 => 'customer_id',
                3 => 'sale_id',
                4 => 'status',
                5 => 'total_payable_atm',
            ];

            $orderColumnName = $columns[$orderColumn] ?? 'id';
            
            $returns = $query->orderBy($orderColumnName, $orderDir)
                ->skip($start)
                ->take($length)
                ->get();

            $returns->transform(function($return) {
                $return->formattedDate = formatDate($return->created_at);
                $return->customer_name = $return->customer->name ?? '-';
                $return->customer_id = $return->customer_id;
                $return->customer_phone = $return->customer->phone1 ?? '-';
                $return->sale_invoice = $return->sale->invoice_code ?? '-';
                $return->sale_id = $return->sale_id;
                $return->total_amount_formatted = number_format($return->total_amount ?? 0, 2);
                $return->adjustment_amt_formatted = number_format($return->adjustment_amt ?? 0, 2);
                $return->total_payable_formatted = number_format($return->total_payable_atm, 2);
                
                if ($return->created_by == null) {
                    $return->createdBy = 'CustomData';
                } else {
                    $return->createdBy = $return->creator->name ?? 'N/A';
                }

                return $return;
            });

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecord,
                'recordsFiltered' => $filteredRecord,
                'data' => $returns->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }

    public function create()
    {
        return view('stock.return.create');
    }

    public function store(Request $request)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'sale_id' => 'required|exists:sales,id',
                'reason' => 'nullable|string',
                'note' => 'nullable|string',
                'status' => 'nullable|in:pending,completed,cancelled',
                'total_payable_atm' => 'required|numeric|min:0',
                'adjustment_amt' => 'nullable|numeric',
                'return_items' => 'required|array|min:1',
                'return_items.*.sales_item_id' => 'required|exists:sales_items,id',
                'return_items.*.qty' => 'required|integer|min:1',
                'return_items.*.is_sellable' => 'required|boolean',
                'return_items.*.unit_price' => 'required|numeric|min:0',
            ]);

            // Verify sale belongs to customer
            $sale = Sales::where('id', $request->sale_id)
                ->where('customerId', $request->customer_id)
                ->where('POSID', $POSID)
                ->first();

            if (!$sale) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sale does not belong to the selected customer.',
                ], 422);
            }

            DB::transaction(function () use ($request, $POSID) {
                // Calculate total amount from return items
                $totalAmount = 0;
                foreach ($request->return_items as $item) {
                    $totalAmount += ($item['unit_price'] * $item['qty']);
                }

                $adjustmentAmt = $request->adjustment_amt ?? 0;
                $totalPayableAtm = $totalAmount + $adjustmentAmt;

                $returnData = [
                    'POSID' => $POSID,
                    'customer_id' => $request->customer_id,
                    'sale_id' => $request->sale_id,
                    'reason' => $request->reason,
                    'note' => $request->note,
                    'status' => $request->status ?? 'pending',
                    'total_amount' => $totalAmount,
                    'total_payable_atm' => $totalPayableAtm,
                    'adjustment_amt' => $adjustmentAmt,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ];

                $return = ProductReturn::create($returnData);

                // Create return items
                foreach ($request->return_items as $item) {
                    ReturnItem::create([
                        'return_id' => $return->id,
                        'sales_item_id' => $item['sales_item_id'],
                        'qty' => $item['qty'],
                        'is_sellable' => $item['is_sellable'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Return Created Successfully.',
                'redirect' => route('stock.return.index')
            ]);

        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors()
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $exception->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $POSID = auth()->user()->POSID;
        
        try {
            $return = ProductReturn::where('POSID', $POSID)
                ->with([
                    'customer',
                    'sale.items.product',
                    'sale.items.variation',
                    'sale.customer',
                    'returnItems.salesItem.product',
                    'returnItems.salesItem.variation',
                    'payments.creator',
                    'payments.updater',
                    'creator',
                    'updater'
                ])
                ->findOrFail($id);

            // Format dates
            $return->formattedCreatedDate = formatDateAndTime($return->created_at);
            $return->formattedUpdatedDate = formatDateAndTime($return->updated_at);
            
            // Format amounts
            $return->total_amount_formatted = number_format($return->total_amount ?? 0, 2);
            $return->adjustment_amt_formatted = number_format($return->adjustment_amt ?? 0, 2);
            $return->total_payable_formatted = number_format($return->total_payable_atm, 2);

            // Customer phone masking
            if (!hasAccess('show_phone')) {
                $return->formattedCustomerPhone = maskPhoneNumber($return->customer->phone1 ?? '');
            } else {
                $return->formattedCustomerPhone = $return->customer->phone1 ?? '-';
            }

            return view('stock.return.show', compact('return'));

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $POSID = auth()->user()->POSID;
        $return = ProductReturn::where('POSID', $POSID)
            ->with(['customer', 'sale', 'returnItems.salesItem.product', 'returnItems.salesItem.variation'])
            ->findOrFail($id);

        return view('stock.return.edit', [
            'return' => $return
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'reason' => 'nullable|string',
                'note' => 'nullable|string',
                'status' => 'nullable|in:pending,completed,cancelled',
                'total_payable_atm' => 'required|numeric|min:0',
                'adjustment_amt' => 'nullable|numeric',
                'return_items' => 'required|array|min:1',
                'return_items.*.sales_item_id' => 'required|exists:sales_items,id',
                'return_items.*.qty' => 'required|integer|min:1',
                'return_items.*.is_sellable' => 'required|boolean',
                'return_items.*.unit_price' => 'required|numeric|min:0',
            ]);

            $return = ProductReturn::where('POSID', $POSID)->findOrFail($id);

            DB::transaction(function () use ($request, $return) {
                // Calculate total amount from return items
                $totalAmount = 0;
                foreach ($request->return_items as $item) {
                    $totalAmount += ($item['unit_price'] * $item['qty']);
                }

                $adjustmentAmt = $request->adjustment_amt ?? 0;
                $totalPayableAtm = $totalAmount + $adjustmentAmt;

                $return->reason = $request->reason;
                $return->note = $request->note;
                $return->status = $request->status ?? 'pending';
                $return->total_amount = $totalAmount;
                $return->total_payable_atm = $totalPayableAtm;
                $return->adjustment_amt = $adjustmentAmt;
                $return->updated_by = auth()->user()->id;
                $return->save();

                // Delete existing return items
                $return->returnItems()->delete();

                // Create new return items
                foreach ($request->return_items as $item) {
                    ReturnItem::create([
                        'return_id' => $return->id,
                        'sales_item_id' => $item['sales_item_id'],
                        'qty' => $item['qty'],
                        'is_sellable' => $item['is_sellable'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Return Updated Successfully.',
            ]);

        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors()
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $exception->getMessage(),
            ]);
        }
    }

    public function searchCustomer(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $POSID = auth()->user()->POSID;

            if (strlen($phone) < 3) {
                return response()->json([
                    'status' => 'success',
                    'customers' => []
                ]);
            }

            $customers = Customer::where('POSID', $POSID)
                ->where('phone1', 'like', "%{$phone}%")
                ->select('id', 'name', 'phone1')
                ->limit(10)
                ->get();

            return response()->json([
                'status' => 'success',
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search customer.'
            ]);
        }
    }

    public function getCustomerSales(Request $request)
    {
        try {
            $customerId = $request->input('customer_id');
            $POSID = auth()->user()->POSID;

            $sales = Sales::where('POSID', $POSID)
                ->where('customerId', $customerId)
                ->with(['customer'])
                ->orderBy('created_at', 'desc')
                ->select('id', 'invoice_code', 'total_payable_amount', 'created_at')
                ->get();

            $sales->transform(function($sale) {
                $sale->formattedDate = formatDate($sale->created_at);
                $sale->total_formatted = number_format($sale->total_payable_amount, 2);
                return $sale;
            });

            return response()->json([
                'status' => 'success',
                'sales' => $sales
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load sales.'
            ]);
        }
    }

    public function getSaleItems(Request $request)
    {
        try {
            $saleId = $request->input('sale_id');
            $POSID = auth()->user()->POSID;

            // Verify sale belongs to POSID
            $sale = Sales::where('POSID', $POSID)
                ->where('id', $saleId)
                ->firstOrFail();

            $items = Sales_items::where('sales_id', $saleId)
                ->with(['product', 'variation'])
                ->get();

            $items->transform(function($item) {
                $item->product_name = $item->product->name ?? '-';
                // Show variant tagline - prefer variation tagline, fallback to variant_tagline from sales_items
                $item->variation_name = $item->variation ? ($item->variation->tagline ?? '-') : ($item->variant_tagline ?? '-');
                $item->unit_price = $item->selling_price ?? $item->product_price ?? 0;
                return $item;
            });

            return response()->json([
                'status' => 'success',
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load sale items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storePayment(Request $request, $id)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'payment_method' => 'required|string|max:255',
                'payment_via' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
                'transaction_id' => 'nullable|string|max:255',
                'note' => 'nullable|string',
            ]);

            $return = ProductReturn::where('POSID', $POSID)->findOrFail($id);

            $payment = ReturnPayment::create([
                'POSID' => $POSID,
                'return_id' => $return->id,
                'payment_method' => $request->payment_method,
                'payment_via' => $request->payment_via,
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'note' => $request->note,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment added successfully.',
                'payment' => $payment->load('creator')
            ]);

        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors()
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $exception->getMessage(),
            ]);
        }
    }

    public function updatePayment(Request $request, $id, $paymentId)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'payment_method' => 'required|string|max:255',
                'payment_via' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
                'transaction_id' => 'nullable|string|max:255',
                'note' => 'nullable|string',
            ]);

            $return = ProductReturn::where('POSID', $POSID)->findOrFail($id);
            $payment = ReturnPayment::where('return_id', $return->id)
                ->where('id', $paymentId)
                ->firstOrFail();

            $payment->update([
                'payment_method' => $request->payment_method,
                'payment_via' => $request->payment_via,
                'amount' => $request->amount,
                'transaction_id' => $request->transaction_id,
                'note' => $request->note,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully.',
                'payment' => $payment->load('creator', 'updater')
            ]);

        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors()
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $exception->getMessage(),
            ]);
        }
    }

    public function destroyPayment($id, $paymentId)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $return = ProductReturn::where('POSID', $POSID)->findOrFail($id);
            $payment = ReturnPayment::where('return_id', $return->id)
                ->where('id', $paymentId)
                ->firstOrFail();

            $payment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment deleted successfully.'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $exception->getMessage(),
            ]);
        }
    }

    public function getPayment($id, $paymentId)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $return = ProductReturn::where('POSID', $POSID)->findOrFail($id);
            $payment = ReturnPayment::where('return_id', $return->id)
                ->where('id', $paymentId)
                ->with(['creator', 'updater'])
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'payment' => $payment
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found.',
            ], 404);
        }
    }
}
