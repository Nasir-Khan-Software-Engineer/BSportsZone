<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\Request;
use Helper;
use Number;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sales.sale.index');
    }

    public function datatable(Request $request){
        $POSID = auth()->user()->POSID;
        $searchCriteria = $request->input('search');

        // here orWhereHas is subquery - we can improve this
        $query = Sales::with('customer', 'createdByUser')
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

                    return [
                        'type'          => 'Service',
                        'code'          => $service->code ?? null,
                        'name'          => $service->name,
                        'staff_name'    => $item->staff?->name ?? 'N/A',
                        'selling_price' => $item->selling_price,
                        'quantity'      => $item->quantity,
                        'total_price'   => $item->selling_price * $item->quantity,
                    ];
                })
                ->values();
        return $serviceList;
    }

    private function getSalesProductList($sale){
        $productList = $sale->items
                ->filter(fn ($item) => !is_null($item->product))
                ->map(function ($item) {

                    $product = $item->product;

                    return [
                        'type'          => 'Product',
                        'code'          => $product->code,
                        'name'          => $product->name,
                        'quantity'      => $item->quantity,
                        'tagline'       => $item->variant_tagline,
                        'selling_price' => $item->selling_price,
                        'total_price'   => $item->quantity * $item->selling_price
                    ];
                })
                ->values(); // reindex
        return $productList;
    }
}
