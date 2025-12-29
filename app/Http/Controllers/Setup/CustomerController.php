<?php

namespace App\Http\Controllers\Setup;

use App\Services\Setup\Customer\ICustomerService;
use App\Services\Loyalty\ILoyaltyService;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use Exception;

class CustomerController extends Controller
{
    public function __construct(ICustomerService $customerService, ILoyaltyService $loyaltyService)
    {
        // $this->middleware('auth');
        $this->customerService = $customerService;
        $this->loyaltyService = $loyaltyService;
    }

    public function index()
    {
        return view('sales/customer/index');
    }

    public function datatable(Request $request)
    {
        $posid = auth()->user()->posid;
        $search = $request->input('search', '');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        // Base query
        if(isFeatureEnabled('ENABLED_LOYALTY')){
            $query = Customer::where('posid', $posid)
                    ->withCount('purchases')
                    ->when($search, function($q) use ($search) {
                        $q->where(function($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('phone1', 'like', "%{$search}%")
                                ->orWhere('type', 'like', "%{$search}%");
                        });
            });
        }else{
            $query = Customer::where('posid', $posid)
                    ->withCount('purchases')
                    ->when($search, function($q) use ($search) {
                        $q->where(function($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('phone1', 'like', "%{$search}%");
                        });
                    });
        }

        // Get total and filtered counts
        $totalRecords = Customer::where('posid', $posid)->count();
        $filteredRecords = $query->count();

        // Handle ordering dynamically
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');


        if(isFeatureEnabled('ENABLED_LOYALTY')) {
            $columns = [
                0 => 'id',
                1 => 'name',
                2 => 'phone1',
                3 => 'purchases_count',
                5 => 'type'
            ];
        }else{
            $columns = [
                0 => 'id',
                1 => 'name',
                2 => 'phone1',
                3 => 'purchases_count'
            ];
        }

        

        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        // Fetch paginated rows
        $customers = $query->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        // Transform for display
        $customers->transform(function($customer) {
            $customer->formattedDate = formatDate($customer->created_at);
            $customer->formattedTime = formatTime($customer->created_at);
            $customer->createdBy = $customer->creator->name ?? 'N/A';

            if (!hasAccess('show_phone')) {
                $customer->phone1 = maskPhoneNumber($customer->phone1);
            }

            // Optionally, you can add loyalty status here if stored in customers table
            // $customer->loyalty_status = $customer->loyalty_status;

            return $customer;
        });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $customers->toArray(),
        ]);
    }


    public function edit($id)
    {
        $customer = $this->customerService->edit($id);

        return response()->json([
            'customer'   => $customer,
            'status'     => 'success'
        ]);
    }

    public function show($id){
        $customer = $this->customerService->show($id);

        $customer->formattedCreatedDate = formatDateAndTime($customer->created_at);
        $customer->formattedUpdatedDate = formatDateAndTime($customer->updated_at);

        return response()->json(['customer' => $customer,'status' => 'success']);
    }

    public function getCustomerInfo($id){
        try {
            $customer = $this->customerService->show($id);

            // Format customer data for modal display
            $customer->formattedCreatedOn = formatDateAndTime($customer->created_at);
            $customer->createdBy = $customer->creator->name ?? '-';

            return response()->json([
                'customer' => $customer,
                'status' => 'success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found or access denied'
            ], 404);
        }
    }

    public function details($id){
        $customer = $this->customerService->show($id);

        // Format date and time for each purchase
        if ($customer->purchases->count()) {
            $customer->purchases->transform(function ($purchase) {
                $purchase->formattedDateTime = formatDateAndTime($purchase->created_at);
                return $purchase;
            });
        }

        // Get service summary data - aggregate services by product name
        $serviceSummary = [];
        if ($customer->purchases->count()) {
            $serviceData = [];
            
            // Collect all service items from all purchases
            foreach ($customer->purchases as $purchase) {
                foreach ($purchase->items as $item) {
                    if ($item->product) {
                        $productName = $item->product->name;
                        if (!isset($serviceData[$productName])) {
                            $serviceData[$productName] = 0;
                        }
                        $serviceData[$productName] += $item->quantity;
                    }
                }
            }
            
            // Convert to array and sort by count (descending)
            $serviceSummary = collect($serviceData)->map(function ($count, $name) {
                return [
                    'service_name' => $name,
                    'total_taken_count' => $count
                ];
            })->sortByDesc('total_taken_count')->values()->toArray();
        }

        $customerRibbonData = [
            'name' => $customer->name,
            'phone' => $customer->phone1,
            'age_group' => $customer->age_group,
            'total_sales' => $customer->purchases->count(),
            'total_service' => $customer->purchases->sum(function($purchase) {
                return $purchase->items->count();
            }),
            'total_paid' => $customer->purchases->sum(function($purchase) {
                return $purchase->payments->sum('paid_amount');
            }),
        ];

        if (isFeatureEnabled('ENABLED_LOYALTY')) {
            $posid = auth()->user()->posid;
            $loyaltyStatus = $this->loyaltyService->getCustomerLoayltyStatus($posid, $id);
            $customerRibbonData = array_merge($customerRibbonData, $loyaltyStatus);
        }

        return view('sales/customer/detailes', ['customer' => $customer, 'customerRibbonData' => $customerRibbonData, 'serviceSummary' => $serviceSummary]);
    }

    public function store(Request $request)
    {
        try{
            $posid = auth()->user()->posid;

            $customer = Customer::where('posid', $posid)->where('phone1', $request->phone1)->first();

            if ($customer) {
                return response()->json([
                    'status' => 'exists',
                    'message' => 'Customer already exists with same phone number',
                    'customer'  => $customer
                ]);
            }

            $request->validate([
                'name'   => 'required|string|min:3|max:100',
                'gender' => 'required',
                'phone1' => [
                    'required',
                    'string',
                    'min:11',
                    'max:20',
                    Rule::unique('customers')->where(function ($query) use ($posid) {
                        return $query->where('posid', $posid)->whereNull('deleted_at');
                    })
                ],
                'phone2' => [
                    'nullable'
                ],
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('customers')->where(function ($query) use ($posid) {
                        return $query->where('posid', $posid)->whereNull('deleted_at');
                    })
                ],
                'address' => 'nullable|string|min:3|max:200',
                'note' => 'nullable|string|min:3|max:1000',
                'age_group' => 'nullable'
            ],
            [

            ],
            [
                'phone1' => 'Phone Number 1',
                'phone2' => 'Phone Number 2',
                'email' => 'Email',
                'address' => 'Address',
                'note' => 'Note',
                'gender' => 'Gender',
                'name' => 'Name',
                'age_group' => 'Age Group'
            ]);

            $customer = $this->customerService->store($request);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Customer Created Successfully.',
                'customer'  => $customer
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'status'    => 'error',
                'message'   => '',
                'errors'    => $exception->validator->errors()
            ]);
        }catch(Exception $exception){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong, please try later.',
            ]);
        }
    } // end create

    public function update(Request $request, $id){
        try{
            $posid = auth()->user()->posid;
            $request->validate([
                'name'   => 'required|string|min:3|max:100',
                'gender' => 'required',
                'age_group' => 'nullable',
                'phone1' => [
                    'required',
                    'string',
                    'min:11',
                    'max:20',
                    Rule::unique('customers')->where(function ($query) use ($posid) {
                        return $query->where('posid', $posid)->whereNull('deleted_at');
                    })->ignore($id, 'id')
                ],
                'phone2' => [
                    'nullable'
                ],
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('customers')->where(function ($query) use ($posid) {
                        return $query->where('posid', $posid)->whereNull('deleted_at');
                    })->ignore($id, 'id')
                ],
                'address' => 'nullable|string|min:3|max:200',
                'note' => 'nullable|string|min:3|max:1000'
            ],
            [

            ],
            [
                'phone1' => 'Phone Number 1',
                'phone2' => 'Phone Number 2',
                'email' => 'Email',
                'address' => 'Address',
                'note' => 'Note',
                'gender' => 'Gender',
                'name' => 'Name',
                'age_group' => 'Age Group'
            ]);

            $customer = $this->customerService->update($request, $id);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Customer Updated Successfully.',
                'customer'  => $customer
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'status'    => 'error',
                'message'   => '',
                'errors'    => $exception->validator->errors()
            ]);
        }catch(Exception $exception){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong, please try later.',
            ]);
        }
    } // end update

    public function search(Request $request)
    {
        $search = $request->term;

        $customers = Customer::with('purchases')->where('posid', auth()->user()->posid)
            ->where('phone1', 'like', "%{$search}%")
            ->select('id', 'name', 'phone1', 'age_group')
            ->limit(5)
            ->get();
        // we need to select total sales and last sale date

        foreach ($customers as $customer) {
            $customer->totalSales = $customer->purchases()->count();

            $lastPurchase = $customer->purchases()->latest()->first();
            $customer->lastVisit = $lastPurchase 
                ? formatDateAndTime($lastPurchase->created_at) 
                : "-"; // or 'Never' if you want a string
        }


        return response()->json($customers, 200);
    }

    public function destroy(Customer $customer){
        if($customer->purchases()->count() > 0){
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'Dependent' => ['This customer has sales items.']
                ],
            ]);
        }else{
            $customer->delete();
            return response()->json([
                'status'    => 'success',
                'message'   => "Customer Deleted Successfully."
            ]);
        }
    }
}
