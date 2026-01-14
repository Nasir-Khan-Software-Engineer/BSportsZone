<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\Request;
use Helper;
use Number;
use Illuminate\Support\Facades\Validator;
use App\Models\SalesPayment;
class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sales.sale.index');
    }

    public function getPendingCount()
    {
        try {
            $POSID = auth()->user()->POSID;
            
            // Count sales with pending status (online orders)
            $count = Sales::where('POSID', $POSID)
                ->where('sale_status', 'pending')
                ->where('sales_from', 'online')
                ->count();
            
            return response()->json([
                'status' => 'success',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'count' => 0,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function datatable(Request $request){
        $POSID = auth()->user()->POSID;
        $searchCriteria = $request->input('search');

        // here orWhereHas is subquery - we can improve this
        $query = Sales::with('customer','payments')
                            ->where('sales.POSID', $POSID)
                            ->where(function($query) use ($searchCriteria){
                                $query->where('invoice_code', 'like', "%{$searchCriteria}%")
                                      ->orWhereHas('customer',function($query) use($searchCriteria){
                                          $query->where('name', 'like', "%{$searchCriteria}%")
                                                ->orWhere('phone1', 'like', "%{$searchCriteria}%");
                                      });
                            });

        $totalRecord = Sales::where('POSID', $POSID)->count();
        $filteredRecord = $query->count();

        $sales = (clone $query)->orderBy('created_at', 'desc')
                        ->skip($request->input('start'))
                        ->take($request->input('length'))
                        ->get();
        
        $sales->transform(function($sale){
            $sale->formattedDate = formatDate($sale->created_at);
            $sale->formattedTime = formatTime($sale->created_at);

            $sale->total_payable_amount = str_replace('BDT', 'Tk.', Number::currency($sale->total_payable_amount, 'BDT'));
            $sale->paidAmount = str_replace('BDT', 'Tk.', Number::currency($sale->payments->sum('paid_amount'), 'BDT'));
            $sale->discount_amount = str_replace('BDT', 'Tk.', Number::currency($sale->discount_amount, 'BDT'));
            $sale->created_by = $sale->createdByUser ? $sale->createdByUser->name : 'N/A';
            
            // Include payment_status and sale_status
            $sale->payment_status = $sale->payment_status ?? 'pending';
            $sale->sale_status = $sale->sale_status ?? 'pending';

            return $sale;
        });

        $result = [];

        $result["draw"] = $request->input('draw');
        $result["recordsTotal"] = $totalRecord;
        $result["recordsFiltered"] = $filteredRecord;

        $result['data'] = $sales->toArray();

        return response()->json($result);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function modal($id)
    {
        $POSID = auth()->user()->POSID;

        try {
            $sale = Sales::where('POSID', $POSID)
            ->with([
                'items.service',
                'items.product.variations',
                'items.staff',
                'createdByUser',
                'updatedByUser',
                'payments',
                'customer',
                'loyaltyHistories',
            ])
            ->findOrFail($id);
            

            $productList = $this->getSalesProductList($sale);
            $serviceList = $this->getSalesServiceList($sale);

            $sale->formattedCreatedDate = formatDateAndTime($sale->created_at);
            $sale->formattedUpdatedDate = formatDateAndTime($sale->updated_at);

            $sale->payments->each(function ($payment) {
                $payment->formattedTime = formatTime($payment->created_at);
                $payment->formattedDate = formatDate($payment->created_at);
                $payment->receivedBy = $payment->createdByUser->name ?? 'N/A';
            });

            return response()->json([
                'status' => 'success',
                'sale' => $sale,
                'loyaltyHistories' => $sale->loyaltyHistories,
                'created_by_user' => $sale->createdByUser,
                'updated_by_user' => $sale->updatedByUser,
                'payments' => $sale->payments,
                'productList' => $productList,
                'serviceList' => $serviceList
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

    }

    public function show($id)
    {
        $POSID = auth()->user()->POSID;

        try {
            $sale = Sales::where('POSID', $POSID)
                ->with([
                    'items.service',
                    'items.product.variations',
                    'items.staff',
                    'createdByUser',
                    'updatedByUser',
                    'payments.createdByUser',
                    'customer',
                    'loyaltyHistories',
                ])
                ->findOrFail($id);

            $productList = $this->getSalesProductList($sale);
            $serviceList = $this->getSalesServiceList($sale);
                            
            // format dates
            $sale->formattedCreatedDate = formatDateAndTime($sale->created_at);
            $sale->formattedUpdatedDate = formatDateAndTime($sale->updated_at);

            // format payments
            $sale->payments->each(function ($payment) {
                $payment->formattedTime = formatTime($payment->created_at);
                $payment->formattedDate = formatDate($payment->created_at);
                $payment->receivedBy = $payment->createdByUser->name ?? 'N/A';
            });

            $sale->discountText = number_format($sale->discount_amount, 2) . ' Tk';

            if ($sale->discount_type === 'fixed') {
                $sale->discountText .= ' (Fixed)';
            } elseif ($sale->discount_type === 'percentage') {
                $sale->discountText .= ' (' . ($sale->discount_value ?? 0) . '%)';
            }

            $sale->adjustmentText = number_format($sale->adjustmentAmt ?? 0, 2) . ' Tk';

            if(!hasAccess('show_phone')){
                $sale->formatedCustomerPhone = maskPhoneNumber($sale->customer->phone1);
            }else{
                $sale->formatedCustomerPhone = $sale->customer->phone1;
            }

            // return with compact 
            return view('sales.sale.show', compact('sale', 'productList', 'serviceList'));

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $sales = Sales::findOrFail($id);

            $sales->delete(); // soft delete added on model

            return response()->json([
                'status'    => 'success',
                'message'   => 'Sales Deleted Successfully.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong.',
            ]);
        }
    }


    private function getSalesServiceList($sale){
        $serviceList = $sale->items
                ->filter(fn ($item) => !is_null($item->service))
                ->map(function ($item) {

                    $service = $item->service;
                    
                    // Calculate discount amount
                    $discountAmount = 0;
                    if ($item->discount_type && $item->discount_value) {
                        if ($item->discount_type == 'percentage') {
                            $discountAmount = ($item->selling_price * $item->discount_value) / 100;
                        } else {
                            $discountAmount = $item->discount_value;
                        }
                    }
                    
                    // Calculate price after discount per unit
                    $priceAfterDiscount = $item->selling_price - $discountAmount;
                    
                    // Calculate total price after discount
                    $totalPrice = $priceAfterDiscount * $item->quantity;

                    return [
                        'type'          => 'Service',
                        'code'          => $service->code ?? null,
                        'name'          => $service->name,
                        'staff_name'    => $item->staff?->name ?? 'N/A',
                        'selling_price' => $item->selling_price,
                        'quantity'      => $item->quantity,
                        'discount_type' => $item->discount_type,
                        'discount_value' => $item->discount_value,
                        'discount_amount' => $discountAmount,
                        'total_price'   => $totalPrice,
                    ];
                })
                ->values();
        return $serviceList;
    }

    public function storePayment(Request $request, $saleId)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            // Set payment_via for cash if not provided
            if ($request->payment_method === 'cash' && !$request->payment_via) {
                $request->merge(['payment_via' => 'cash']);
            }
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required|in:cash,card,wallet',
                'payment_via' => 'required|string',
                'paid_amount' => 'required|numeric|min:0.01',
                'transaction_id' => 'nullable|string|max:255',
                'note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find the sale
            $sale = Sales::where('POSID', $POSID)->findOrFail($saleId);

            // Create payment
            $payment = new SalesPayment();
            $payment->POSID = $POSID;
            $payment->sales_id = $sale->id;
            $payment->payment_method = $request->payment_method;
            $payment->payment_via = $request->payment_via;
            $payment->paid_amount = $request->paid_amount;
            $payment->transaction_id = $request->transaction_id;
            $payment->note = $request->note;
            $payment->created_by = auth()->user()->id;
            $payment->updated_by = auth()->user()->id;
            $payment->save();

            // Reload payments to get the latest total
            $sale->refresh();
            $totalPaid = $sale->payments()->sum('paid_amount');
            
            // Auto-update payment status if total paid >= total payable
            if ($totalPaid >= $sale->total_payable_amount) {
                $sale->payment_status = 'paid';
            } else if ($totalPaid > 0) {
                $sale->payment_status = 'partial';
            } else {
                $sale->payment_status = 'pending';
            }
            $sale->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment added successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getSalesProductList($sale){
        $productList = $sale->items
                ->filter(fn ($item) => !is_null($item->product))
                ->map(function ($item) {

                    $product = $item->product;
                    
                    // Calculate discount amount
                    $discountAmount = 0;
                    if ($item->discount_type && $item->discount_value) {
                        if ($item->discount_type == 'percentage') {
                            $discountAmount = ($item->selling_price * $item->discount_value) / 100;
                        } else {
                            $discountAmount = $item->discount_value;
                        }
                    }
                    
                    // Calculate price after discount per unit
                    $priceAfterDiscount = $item->selling_price - $discountAmount;
                    
                    // Calculate total price after discount
                    $totalPrice = $priceAfterDiscount * $item->quantity;

                    return [
                        'type'          => 'Product',
                        'code'          => $product->code,
                        'name'          => $product->name,
                        'quantity'      => $item->quantity,
                        'tagline'       => $item->variant_tagline,
                        'selling_price' => $item->selling_price,
                        'discount_type' => $item->discount_type,
                        'discount_value' => $item->discount_value,
                        'discount_amount' => $discountAmount,
                        'total_price'   => $totalPrice
                    ];
                })
                ->values(); // reindex
        return $productList;
    }
}
