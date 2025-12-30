<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('products')
            ->where('POSID', auth()->user()->POSID)
            ->get();

        foreach($suppliers as $supplier){
            $supplier->formattedDate = formatDate($supplier->created_at);
            $supplier->formattedTime = formatTime($supplier->created_at);
            $supplier->products_count = $supplier->products->count();
        }

        return view("service/supplier/index", ['suppliers' => $suppliers]);
    }

    public function create()
    {
        return view("service/supplier/create");
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|min:1|max:100',
                'phone' => 'required',
                'email' => 'required|email',
                'address' => 'required|string',
                'note' => 'string|nullable'
            ]);

            $supplier = new Supplier;
            $supplier->POSID = auth()->user()->POSID;
            $supplier->name = $request->name;
            $supplier->phone_1 = $request->phone;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->note = $request->note ?? '';
            $supplier->created_by = auth()->user()->id;

            $supplier->save();

            $supplier->products_count = 0;
            $supplier->phone = $supplier->phone_1; // For compatibility with frontend
            $supplier->formattedDate = formatDate($supplier->created_at);
            $supplier->formattedTime = formatTime($supplier->created_at);

            return response()->json(
                [
                    'status'=>'success',
                    'message'=>'Supplier created successfully.',
                    'supplier' => $supplier
                ]);
        }catch(ValidationException $exception){
            return response()->json(
                [
                    'status'=>'error',
                    'message' => '',
                    'errors' => $exception->validator->errors()
                ]
            );
        }catch(\Exception $exception){
            return response()->json(
                [
                    'status'=>'error',
                    'message' => 'Something went wrong, please try later.',
                ]
            );
        }
    }

    public function show(Supplier $supplier)
    {
        $POSID = auth()->user()->POSID;
        
        // Ensure supplier belongs to current POS
        if($supplier->POSID != $POSID){
            abort(404);
        }

        // Load products for this supplier
        $products = Product::where('POSID', $POSID)
            ->where('supplier_id', $supplier->id)
            ->with(['brand', 'unit', 'categories'])
            ->get();

        // Prepare ribbon data
        $supplierRibbonData = [
            'name' => $supplier->name,
            'phone' => $supplier->phone_1,
            'email' => $supplier->email ?? '-',
            'address' => $supplier->address ?? '-',
            'city' => $supplier->city ?? '-',
            'country' => $supplier->country ?? '-',
        ];

        return view('service/supplier/show', [
            'supplier' => $supplier,
            'products' => $products,
            'supplierRibbonData' => $supplierRibbonData
        ]);
    }

    public function edit(Supplier $supplier)
    {
        return view('service/supplier/create',['supplier' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        try{
            $request->validate([
                'name' => 'required|string|min:1|max:100',
                'phone' => 'required',
                'email' => 'required|email',
                'address' => 'required|string',
                'note' => 'string|nullable'
            ]);

            $supplier->name = $request->name;
            $supplier->phone_1 = $request->phone;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->note = $request->note ?? '';
            $supplier->updated_by = auth()->user()->id;

            $supplier->update();

            $supplier->products_count = $supplier->products()->count();
            $supplier->phone = $supplier->phone_1; // For compatibility with frontend
            $supplier->formattedDate = formatDate($supplier->created_at);
            $supplier->formattedTime = formatTime($supplier->created_at);

            return response()->json(
                [
                    'status'=>'success',
                    'message'=>'Supplier updated successfully.',
                    'supplier' => $supplier
                ]);
        }catch(ValidationException $exception){
            return response()->json(
                [
                    'status'=>'error',
                    'message' => '',
                    'errors' => $exception->validator->errors()
                ]
            );
        }catch(\Exception $exception){
            return response()->json(
                [
                    'status'=>'error',
                    'message' => 'Something went wrong, please try later.',
                ]
            );
        }
    }

    public function destroy(Supplier $supplier)
    {
        try{
            if($supplier->POSID != auth()->user()->POSID){
                return response()->json(
                    [
                        'status'=>'error',
                        'message' => 'Unauthorized access.',
                    ]
                );
            }

            if($supplier->products()->count() > 0){
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['This supplier has products associated with it.']
                    ],
                ]);
            }

            if($supplier->delete()){
                return response()->json(
                    [
                        'status'=>'success',
                        'message' => 'Supplier deleted successfully.',
                    ]
                );
            }else{
                return response()->json(
                    [
                        'status'=>'error',
                        'message' => 'Something went wrong, please try later.',
                    ]
                );
            }
        }catch (Exception $exception){
            return response()->json(
                [
                    'status'=>'error',
                    'message' => 'Something went wrong, please try later.',
                ]
            );
        }
    }
}
