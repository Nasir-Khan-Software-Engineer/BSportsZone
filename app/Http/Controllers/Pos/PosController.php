<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use App\Services\AccountSetup\IAccountSetupService;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Services\Category\ICategoryService;
use App\Services\Brand\IBrandService;
use App\Services\Pos\IPosService;
use App\Models\Sales_items;
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
use App\Services\Product\IProductService;
use App\Services\Service\IServiceService;

class PosController extends Controller
{
    public function __construct(IPosService $iPosService,
                                ICategoryService $iCategoryService,
                                IBrandService $iBrandService,
                                IAccountSetupService $iAccountService,
                                IProductService $iProductService,
                                IServiceService $iServiceService) {

        $this->posService = $iPosService;
        $this->categoryService = $iCategoryService;
        $this->brandService = $iBrandService;
        $this->accountService = $iAccountService;
        $this->productService = $iProductService;
        $this->serviceService = $iServiceService;
    }

    public function index(){
        $POSID = auth()->user()->POSID;
        $categories = $this->categoryService->getAllCategories(auth()->user()->POSID);
        $brands = $this->brandService->getBrands(auth()->user()->POSID);

        $defaultType = "Product";

        $topServiceOrProduct = $this->posService->getPosPageItems($POSID, $defaultType);

        return view('pos/index',[
            'recentServices' => $topServiceOrProduct,
            'categories' => $categories,
            'brands' => $brands]);
    }

    public function searchService(Request $request)
    {
        $serviceName = $request->input('searchCriteria');
        $categoryId  = $request->input('categoryId');
        $productType = $request->input('productOrService');
        $posId       = auth()->user()->POSID;
        $searchResult;

        if($productType == "Service"){
            $searchResult = $this->serviceService->searchService($posId, $serviceName, $categoryId);
        }else{
            $searchResult = $this->productService->searchProduct($posId, $serviceName, $categoryId);
        }

        return response()->json($searchResult);
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
        $POSID = auth()->user()->POSID;

        $services = Product::select('products.id', 'products.price', 'products.type')
            ->where('products.POSID', $POSID)
            //->where('type', 'Service')
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

        // Sales table sales
        $sales = new Sales;
        $sales->POSID = $POSID;
        $sales->invoice_code = (session('accountInfo.invoiceNumberPrefix') ?? 'AU') . '-'.date('YmdHis');
        $sales->customerId = ((int) $request->customerId);
        $sales->total_amount = $totalAmount;
        $sales->discount_type = $request->discountType;
        $sales->discount_value = $request->discount;
        $sales->discount_amount = $discountAmount;
        $sales->total_payable_amount = $request->payment["paidAmount"]; // paid amount and paybale amount same as there is no due and multiple payment option is not enabled.
        $sales->created_by = auth()->user()->id;
        $sales->updated_by = auth()->user()->id;
        $sales->adjustmentAmt = $request->adjustmentAmt;
        $sales->save();

        $salesItems = [];
        $now = now();
        
        foreach($services as $service){
            $salesItemObj = [];

            $qty = array_filter($request->services, function($item) use($service){
                return (int) $item['id'] == $service->id;
            });

            $serviceData = reset($qty);
            $staffId = isset($serviceData['staff_id']) && !empty($serviceData['staff_id']) 
                ? (int)$serviceData['staff_id'] 
                : null;

            $salesItemObj['POSID'] = $POSID;
            $salesItemObj['sales_id'] = $sales->id;
            $salesItemObj['product_id'] = $service->id;
            $salesItemObj['staff_id'] = $staffId;
            $salesItemObj['product_price'] = $service->price;
            $salesItemObj['selling_price'] = $service->price;
            $salesItemObj['quantity'] = $serviceData['quantity'];
            $salesItemObj['type'] = $service->type;
            $salesItemObj['created_at'] = $now;
            $salesItemObj['updated_at'] = $now;

            array_push($salesItems, $salesItemObj);

            // if service type is Product we need to reduce the product variation stock quantity
            // from fronend we need to get product id, product variation id and quantity
            // we need to show the product variation in product front end 
            // we need to design the ui


        }

        Sales_items::insert($salesItems);

        // Send SMS after items are inserted (so quantity can be calculated)
        $smsConfig = session('sms_config');
        if($smsConfig && isset($smsConfig['is_active']) && $smsConfig['is_active']){ 
            $sms = $this->sendSmsToCustomer($sales);
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
                'POSID'          => $POSID,
                'sales_id'       => $sales->id,
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
                    $POSID,
                    $request->loyaltyCardId,
                    $sales->id,
                    $request->discountType,
                    $request->discount,
                    $discountAmount,
                    'Loyalty Applied',
                    false
                );
            }else if($request->skipLoyalty && $request->loyaltyCardId){
                $storeLoyalty = $this->storeLoyaltyHistory(
                    $POSID,
                    $request->loyaltyCardId,
                    $sales->id,
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

        $accountInfo = $this->accountService->getAccountInfo(auth()->user()->POSID);

        return response()->json($accountInfo, 200);
    }

    public function getCustomerLastSales($customerId)
    {
        try {
            $POSID = auth()->user()->POSID;

            // Get the last sale for this customer
            $lastSale = Sales::where('POSID', $POSID)
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


    public function storeLoyaltyHistory($POSID, $card_id, $sales_id, $discount_type, $discount_value, $discount_amount, $note, $isSkipped) 
    {
        try {
            LoyaltyHistory::create([
                'POSID' => $POSID,
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
                $sales->POSID,
                'POS_SALE', // Source tracking
                $templateData
            );

            return true;
        } catch (Exception $e) {
            \Log::error('SMS_SEND_ERROR', [
                'error' => $e->getMessage(),
                'sales_id' => $sales->id ?? null,
                'POSID' => $sales->POSID ?? null,
            ]);
            return false;
        }
    }

    public function getStaffs(Request $request)
    {
        try {
            $posId = auth()->user()->POSID;
            $today = Carbon::today()->format('Y-m-d');

            // Get staff designation
            $staffDesignation = EmployeeDesignation::where('POSID', $posId)
                ->where('name', 'Staff')
                ->first();

            if (!$staffDesignation) {
                return response()->json([
                    'status' => 'success',
                    'staffs' => []
                ]);
            }

            // Get all staffs
            $staffs = Employee::where('POSID', $posId)
                ->where('designation_id', $staffDesignation->id)
                ->where('status', 'Active')
                ->orderBy('name')
                ->get();

            // Get today's attendance for staffs
            $todayAttendances = Attendance::where('POSID', $posId)
                ->where('attendance_date', $today)
                ->whereIn('employee_id', $staffs->pluck('id'))
                ->get()
                ->keyBy('employee_id');

            // Get today's service count for each staff
            $todayServiceCounts = Sales_items::where('POSID', $posId)
                ->where('type', 'Service')
                ->whereDate('created_at', $today)
                ->whereNotNull('staff_id')
                ->selectRaw('staff_id, COUNT(*) as service_count')
                ->groupBy('staff_id')
                ->get()
                ->keyBy('staff_id');

            $staffsData = $staffs->map(function($staff) use ($todayAttendances, $todayServiceCounts) {
                $attendance = $todayAttendances->get($staff->id);
                $isPresent = $attendance && $attendance->status === 'Present';
                $todayServiceCount = $todayServiceCounts->get($staff->id)->service_count ?? 0;

                return [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'is_present' => $isPresent,
                    'today_service_count' => $todayServiceCount
                ];
            });

            return response()->json([
                'status' => 'success',
                'staffs' => $staffsData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
