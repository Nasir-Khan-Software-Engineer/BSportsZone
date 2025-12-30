<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\Brand\IBrandService;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    public function __construct(IBrandService $iBrandService){

        $this->brandService = $iBrandService;
    }

    public function index()
    {
        $brands = $this->brandService->getBrands(auth()->user()->posid);

        foreach ($brands as $brand){

            $brand->formattedDate = formatDate($brand->created_at);
            $brand->formattedTime = formatTime($brand->created_at);

            if ($brand->created_by == null) {
                $brand->createdBy = "CustomData";
            }else{
                $brand->createdBy = $brand->creator->name;
            }
        }

        return view("service/brand/index", ['brands' => $brands]);
    }

    public function create()
    {
        return view('service/brand/create');
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'brandName' => 'required|string|min:1|max:100',
                'brandDescription' => 'string|nullable',
                'brandNote' => 'string|nullable'
            ]);

            $brand = new Brand;
            $brand->posid = auth()->user()->posid;
            $brand->name = $request->brandName;
            $brand->logo = "";
            $brand->description = "";
            $brand->note ="";
            $brand->created_by = auth()->user()->id;

            $brand->save();

            $brand->createdBy = auth()->user()->name;
            $brand->formattedDate = formatDate($brand->created_at);
            $brand->formattedTime = formatTime($brand->created_at);

            return response()->json(
                [
                    'status'=>'success',
                    'message'=>'Brand created successfully.',
                    'brand' => $brand
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

    public function show(Brand $brand)
    {
        //
    }

    public function edit(Brand $brand)
    {
        return view('service/brand/create',['brand' => $brand]);
    }

    public function update(Request $request, $id)
    {
        try{

            $posid = auth()->user()->posid;
            $brand = Brand::with('creator')->where('id', $id)->where('posid', $posid)->first();

            $request->validate([
                'brandName' => 'required|string|min:1|max:100',
                'brandDescription' => 'string|nullable',
                'brandNote' => 'string|nullable'
            ]);

            $brand->name = $request->brandName;
            $brand->logo = "demo";
            $brand->description = "demo";
            $brand->note = "demo";
            $brand->updated_by = auth()->user()->id;

            $brand->update();

            $brand->createdBy = $brand->creator->name;
            $brand->formattedDate = formatDate($brand->created_at);
            $brand->formattedTime = formatTime($brand->created_at);

            return response()->json(
                [
                    'status'=>'success',
                    'message'=>'Brand updated successfully.',
                    'brand' => $brand
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
                    'message' => 'Something went wrong, please try later. Error: ' . $exception->getMessage(),
                ]
            );
        }
    }

    public function destroy(Brand $brand)
    {
        try{

            if($brand->services()->count() > 0){
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['This brand has service items.']
                    ],
                ]);
            }else{
                if($brand->delete()){
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
            }
            
        }catch (Exception $exception){
            return $exception;
        }
    }
}
