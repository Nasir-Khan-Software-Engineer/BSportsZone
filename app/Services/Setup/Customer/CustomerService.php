<?php
namespace App\Services\Setup\Customer;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerService implements ICustomerService{

    public function index(){
        $posid = auth()->user()->posid;
        $customers = Customer::where('posid', $posid)
                            ->with('sales', 'creator')
                            ->orderBy('id', 'DESC')
                            ->get();

        // Apply phone number masking based on permissions
        foreach ($customers as $customer) {
            if (!hasAccess('show_phone')) {
                $customer->phone1 = maskPhoneNumber($customer->phone1);
            }
        }

        return $customers;
    }


    public function show($id){
        $posid = auth()->user()->posid;
        $customer = Customer::with('sales', 'creator', 'updater')
                ->where('posid', $posid)
                ->where('id', $id)
                ->firstOrFail();

        if($customer->created_by){
            $customer->createdBy = $customer->creator->name;
        }else{
            $customer->createdBy = 'CustomData';
        }
        
        if($customer->updated_by){
            $customer->updatedBy = $customer->updater->name;
        }else{
            $customer->updatedBy = '';
        }

        return $customer;
    }

    public function store(Request $request){
        $customer = new Customer();
        $customer->posid        = auth()->user()->posid;
        $customer->name         = ucwords($request->name);
        $customer->gender       = $request->gender;
        $customer->email        = $request->email;
        $customer->phone1       = $request->phone1;
        $customer->phone2       = $request->phone2;
        $customer->address      = $request->address;
        $customer->note         = $request->note;
        $customer->age_group    = $request->age_group;
        $customer->created_by   = auth()->user()->id;
        $customer->isActive     = 1;
        $customer->save();

        $customer->formattedDate = formatDate($customer->created_at);
        $customer->formattedTime = formatTime($customer->created_at);
        $customer->createdBy = auth()->user()->name;
        $customer->SalesCount = 0;

        return $customer;
    }

    public function edit($id)
    {
        $posid = auth()->user()->posid;
        $customer = Customer::where('posid', $posid)
            ->where('id', $id)
            ->first();

        return $customer;
    }


    public function update(Request $request, $id){

        $posid = auth()->user()->posid;
        $customer = Customer::where('posid', $posid)
            ->with('creator')
            ->where('id', $id)
            ->first();

        $customer->name         = ucwords($request->name);
        $customer->gender       = $request->gender;
        $customer->email        = $request->email;
        $customer->phone1       = $request->phone1;
        $customer->phone2       = "demo";
        $customer->address      = $request->address;
        $customer->isActive     = 1;
        $customer->note         = $request->note;
        $customer->age_group    = $request->age_group;
        $customer->updated_by   = auth()->user()->id;
        $customer->save();

        $customer->formattedDate = formatDate($customer->created_at);
        $customer->formattedTime = formatTime($customer->created_at);
        $customer->SalesCount = $customer->sales()->count();
        $customer->createdBy = $customer->creator->name;

        return $customer;
    }

    public function destroy($id){
        $posid = auth()->user()->posid;
        $customer = Customer::where('posid', $posid)
            ->where('id', $id)
            ->first();
        $customer->delete();
        return $customer;
    }
}
