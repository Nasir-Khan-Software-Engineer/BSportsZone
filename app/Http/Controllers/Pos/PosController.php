<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Purchases;
use App\Services\AccountSetup\IAccountSetupService;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Services\Category\ICategoryService;
use App\Services\Brand\IBrandService;
use App\Services\Pos\IPosService;
use App\Models\Purchase_items;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use App\Models\Attendance;
use Exception;
use App\Models\SalesPayment;
use App\Models\Accountinfo;
use App\Models\LoyaltyHistory;
use App\Jobs\SendSmsJob;
use App\Services\Sms\SmsTemplateBuilder;
use Carbon\Carbon;

class PosController extends Controller
{
    public function __construct(IPosService $iPosService,
                                ICategoryService $iCategoryService,
                                IBrandService $iBrandService,
                                IAccountSetupService $iAccountService){

        $this->posService = $iPosService;
        $this->categoryService = $iCategoryService;
        $this->brandService = $iBrandService;
        $this->accountService = $iAccountService;
    }

    public function index(){
        $posid = auth()->user()->posid;
        $categories = $this->categoryService->getAllCategories(auth()->user()->posid);
        $brands = $this->brandService->getBrands(auth()->user()->posid);

        $topSellingServices = $this->posService->getPosPageServices(auth()->user()->posid);

        return view('pos/index',[
            'recentServices' => $topSellingServices,
            'categories' => $categories,
            'brands' => $brands]);
    }

    public function searchService(Request $request)
    {
        $serviceName = $request->input('searchCriteria');
        $categoryId  = $request->input('categoryId');
        $brandId     = $request->input('brandId');
        $posId       = auth()->user()->posid;

        $services = Product::select(
                'products.id',
                'products.name',
                'products.posid',
                'code',
                'products.price',
                'products.image',
                'products.beautician_id'
            )
            ->with('TodaysBeautician:id,name')
            ->where('products.posid', $posId)
            ->where('type', 'Service')
            // search by name or code
            ->when($serviceName, function ($query, $serviceName) {
                $query->where(function ($q) use ($serviceName) {
                    $q->where('products.name', 'like', "%{$serviceName}%")
                    ->orWhere('code', 'like', "%{$serviceName}%");
                });
            })

            // filter by brand if set and not 0
            ->when(!empty($brandId) && $brandId != 0, function ($query) use ($brandId) {
                $query->where('products.brand_id', $brandId);
            })

            // filter by category if set and not 0
            ->when(!empty($categoryId) && $categoryId != 0, function ($query) use ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('category.id', $categoryId);
                });
            })

            ->orderBy('products.updated_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json($services);
    }

    public function saveSales(Request $request){
        // validation

        if ($request->adjustmentAmt != 0) {
            $min = session('posSettings.adjustment_min');
            $max = session('posSettings.adjustment_max');

            if ($request->adjustmentAmt < $min || $request->adjustmentAmt > $max) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Adjustment must be between {$min} and {$max}."
                ], 422);
            }
        }

        $customerId = $request->input('customerId');
        if (!is_numeric($customerId) || (int)$customerId < 1) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Please Select or Create a Customer.'
            ],500);
        }

        $serviceIds = array_column($request->services, 'id');
        $posid = auth()->user()->posid;

        $services = Product::select('products.id', 'products.price')
            ->where('products.posid', $posid)
            ->where('type', 'Service')
            ->whereIn('products.id', $serviceIds)
            ->get();

        $totalAmount = $services->sum(function ($item) use($request) {
            $qty = array_filter($request->services, function($prod) use($item){
                return (int) $prod['id'] == $item->id;
            });
            return ($item->price ?? 0)  * reset($qty)['quantity'];
        });


        $discountAmount = $request->discount;
        if($request->discountType != 'fixed'){
            $discountAmount = ($totalAmount * $request->discount)/100;
        }

        // Purchases table purchase
        $purchase = new Purchases;
        $purchase->posid = $posid;
        $purchase->shop_id = 1;
        $purchase->invoice_code = (session('accountInfo.invoiceNumberPrefix') ?? 'AU') . '-'.date('YmdHis');
        $purchase->customerId = ((int) $request->customerId);
        $purchase->total_amount = $totalAmount;
        $purchase->discount_type = $request->discountType;
        $purchase->discount_value = $request->discount;
        $purchase->discount_amount = $discountAmount;
        $purchase->total_payable_amount = $request->payment["paidAmount"]; // paid amount and paybale amount same as there is no due and multiple payment option is not enabled.
        $purchase->payment_date = date('Y-m-d');
        $purchase->created_by = auth()->user()->id;
        $purchase->updated_by = auth()->user()->id;
        $purchase->adjustmentAmt = $request->adjustmentAmt;
        $purchase->save();

        $purchaseItems = [];
        $now = now();
        
        foreach($services as $service){
            $purchaseItemObj = [];

            $qty = array_filter($request->services, function($item) use($service){
                return (int) $item['id'] == $service->id;
            });

            $serviceData = reset($qty);
            $beauticianId = isset($serviceData['beautician_id']) && !empty($serviceData['beautician_id']) 
                ? (int)$serviceData['beautician_id'] 
                : null;

            $purchaseItemObj['posid'] = $posid;
            $purchaseItemObj['purchase_id'] = $purchase->id;
            $purchaseItemObj['product_id'] = $service->id;
            $purchaseItemObj['beautician_id'] = $beauticianId;
            $purchaseItemObj['product_price'] = $service->price;
            $purchaseItemObj['selling_price'] = $service->price;
            $purchaseItemObj['quantity'] = $serviceData['quantity'];
            $purchaseItemObj['created_at'] = $now;
            $purchaseItemObj['updated_at'] = $now;

            array_push($purchaseItems, $purchaseItemObj);
        }

        Purchase_items::insert($purchaseItems);

        // Send SMS after items are inserted (so quantity can be calculated)
        $smsConfig = session('sms_config');
        if($smsConfig && isset($smsConfig['is_active']) && $smsConfig['is_active']){ 
            $sms = $this->sendSmsToCustomer($purchase);
        }

        // add payment info
        $payment_method = $request->payment["paymentMethod"];
        $transaction_id = "";
        $payment_via = "";

        if($payment_method == "card"){
            $payment_via = $request->payment["cardType"];
            $transaction_id = $request->payment["transactionId"];
        }else if($payment_method == "cash"){
            $payment_via = "cash";
        }else if($payment_method == "wallet"){
            $payment_via = $request->payment["mobileBankingType"];
            $transaction_id = $request->payment["transactionId"];
        }else{
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong: Payment Failed.'
            ], 500);
        }

        if($payment_via == ""){
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong: Payment Failed.'
            ], 500);
        }

        try {
            $payment = SalesPayment::create([
                'posid'          => $posid,
                'sales_id'       => $purchase->id,
                'payment_method' => $payment_method,
                'payment_via'    => $payment_via,
                'paid_amount'    => $request->payment["paidAmount"],
                'transaction_id' => $transaction_id,
                'note'           => $request->payment["paymentNote"],
                'created_by'     => auth()->user()->id,
                'updated_by'     => auth()->user()->id
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }

        if(isFeatureEnabled('ENABLED_LOYALTY')){
            if($request->loyaltyCardVerified && $request->loyaltyCardId){
                $storeLoyalty = $this->storeLoyaltyHistory(
                    $posid,
                    $request->loyaltyCardId,
                    $purchase->id,
                    $request->discountType,
                    $request->discount,
                    $discountAmount,
                    'Loyalty Applied',
                    false
                );
            }else if($request->skipLoyalty && $request->loyaltyCardId){
                $storeLoyalty = $this->storeLoyaltyHistory(
                    $posid,
                    $request->loyaltyCardId,
                    $purchase->id,
                    'Fixed',
                    '0',
                    '0',
                    'Customer Skip Loyalty: '.$request->skipLoyaltyReason,
                    true
                );
            }
        }
        return response()->json('', 201);
    }

    public function getAccountInfo(){

        $accountInfo = $this->accountService->getAccountInfo(auth()->user()->posid);

        return response()->json($accountInfo, 200);
    }

    public function getCustomerLastSales($customerId)
    {
        try {
            $posid = auth()->user()->posid;

            // Get the last sale for this customer
            $lastSale = Purchases::where('posid', $posid)
                ->where('customerId', $customerId)
                ->with([
                    'items.service',
                    'payments',
                    'customer',
                    'createdByUser'
                ])
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastSale) {
                return response()->json([
                    'status' => 'success',
                    'sale' => null,
                    'message' => 'No sales history found for this customer.'
                ]);
            }

            // Format the data
            $lastSale->formattedDate = formatDate($lastSale->created_at);
            $lastSale->formattedTime = formatTime($lastSale->created_at);
            $lastSale->formattedCreatedDate = formatDateAndTime($lastSale->created_at);

            // Format payment information
            $paymentMethod = 'N/A';
            if ($lastSale->payments && $lastSale->payments->count() > 0) {
                $payment = $lastSale->payments->first();
                $paymentMethod = ucfirst($payment->payment_method);
                if ($payment->payment_via) {
                    $paymentMethod .= ' (' . $payment->payment_via . ')';
                }
            }

            // Get sales by user name
            $salesBy = 'N/A';
            if ($lastSale->createdByUser) {
                $salesBy = $lastSale->createdByUser->name;
            }

            // Format adjustment amount
            $adjustmentAmount = number_format($lastSale->adjustmentAmt ?? 0, 2);

            return response()->json([
                'status' => 'success',
                'sale' => $lastSale,
                'paymentMethod' => $paymentMethod,
                'totalPaid' => $lastSale->payments ? $lastSale->payments->sum('paid_amount') : $lastSale->total_payable_amount,
                'salesBy' => $salesBy,
                'adjustmentAmount' => $adjustmentAmount
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function storeLoyaltyHistory($posid, $card_id, $sales_id, $discount_type, $discount_value, $discount_amount, $note, $isSkipped) 
    {
        try {
            LoyaltyHistory::create([
                'posid' => $posid,
                'card_id' => $card_id,
                'sales_id' => $sales_id,
                'discount_type' => $discount_type,
                'discount_value' => $discount_value,
                'discount_amount' => $discount_amount,
                'created_by' => auth()->user()->id,
                'note' => $note,
                'isSkipped' => $isSkipped
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    } 



    private function sendSmsToCustomer($sales) {
        try {
            // Load customer relationship if not already loaded
            if (!$sales->relationLoaded('customer')) {
                $sales->load('customer');
            }
            
            // Load items relationship if not already loaded
            if (!$sales->relationLoaded('items')) {
                $sales->load('items');
            }

            // Get customer phone
            $customer = $sales->customer;
            if (!$customer || !$customer->phone1) {
                return false; // No phone number available
            }

            $phone = $customer->phone1;

            // Build system line using template builder
            $templateBuilder = new SmsTemplateBuilder();
            
            // Calculate total quantity from items
            $totalQuantity = $sales->items->sum('quantity');
            
            // Prepare template data
            $templateData = [
                'system_line' => $templateBuilder->buildSystemLineForSale([
                    'invoice_code' => $sales->invoice_code ?? '',
                    'payable_amount' => $sales->total_payable_amount ?? 0,
                    'quantity' => $totalQuantity,
                    'discount_amount' => $sales->discount_amount ?? 0,
                    'adjustment_amount' => $sales->adjustmentAmt ?? 0,
                ])
            ];

            // Dispatch SMS job with from field and template data
            SendSmsJob::dispatch(
                $phone,
                '', // Empty message - will be built from template
                'T', // Transactional
                $sales->posid,
                'POS_SALE', // Source tracking
                $templateData
            );

            return true;
        } catch (Exception $e) {
            \Log::error('SMS_SEND_ERROR', [
                'error' => $e->getMessage(),
                'sales_id' => $sales->id ?? null,
                'posid' => $sales->posid ?? null,
            ]);
            return false;
        }
    }

    public function getBeauticians(Request $request)
    {
        try {
            $posId = auth()->user()->posid;
            $today = Carbon::today()->format('Y-m-d');

            // Get beautician designation
            $beauticianDesignation = EmployeeDesignation::where('posid', $posId)
                ->where('name', 'Beautician')
                ->first();

            if (!$beauticianDesignation) {
                return response()->json([
                    'status' => 'success',
                    'beauticians' => []
                ]);
            }

            // Get all beauticians
            $beauticians = Employee::where('posid', $posId)
                ->where('designation_id', $beauticianDesignation->id)
                ->where('status', 'Active')
                ->orderBy('name')
                ->get();

            // Get today's attendance for beauticians
            $todayAttendances = Attendance::where('posid', $posId)
                ->where('attendance_date', $today)
                ->whereIn('employee_id', $beauticians->pluck('id'))
                ->get()
                ->keyBy('employee_id');

            // Get today's service count for each beautician
            $todayServiceCounts = Purchase_items::where('posid', $posId)
                ->whereDate('created_at', $today)
                ->whereNotNull('beautician_id')
                ->selectRaw('beautician_id, COUNT(*) as service_count')
                ->groupBy('beautician_id')
                ->get()
                ->keyBy('beautician_id');

            $beauticiansData = $beauticians->map(function($beautician) use ($todayAttendances, $todayServiceCounts) {
                $attendance = $todayAttendances->get($beautician->id);
                $isPresent = $attendance && $attendance->status === 'Present';
                $todayServiceCount = $todayServiceCounts->get($beautician->id)->service_count ?? 0;

                return [
                    'id' => $beautician->id,
                    'name' => $beautician->name,
                    'is_present' => $isPresent,
                    'today_service_count' => $todayServiceCount
                ];
            });

            return response()->json([
                'status' => 'success',
                'beauticians' => $beauticiansData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
