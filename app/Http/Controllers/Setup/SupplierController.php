<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        abort(404);
    }

    public function index()
    {
        $suppliers = Supplier::where('POSID', auth()->user()->POSID)->get();

        foreach($suppliers as $supplier){
            $supplier->formattedDate = formatDate($supplier->created_at);
            $supplier->formattedTime = formatTime($supplier->created_at);
        }

        return view("setup/supplier/index", ['suppliers' => $suppliers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("setup/supplier/create");
    }

    /**
     * Store a newly created resource in storage.
     */
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
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->note = $request->note;
            $supplier->created_by = auth()->user()->id;

            $supplier->save();

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

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('setup/supplier/create',['supplier' => $supplier]);
    }

    /**
     * Update the specified resource in storage.
     */
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
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->note = $request->note;
            $supplier->updated_by = auth()->user()->id;

            $supplier->update();

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try{
            if($supplier->POSID == auth()->user()->POSID
                && $supplier->delete()){

                return response()->json(
                    [
                        'status'=>'success',
                        'message' => 'Brand deleted successfully.',
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
            return $exception;
        }
    }
}
