<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Shop;
use App\Models\Sales;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use Illuminate\Support\Facades\DB;
use Exception;


class ServiceController extends Controller
{
    public function index(){
        $POSID = auth()->user()->POSID;
        $brands = Brand::where('POSID', '=', $POSID)->get();
        $categories = Category::where('POSID', '=', $POSID)->get();
        $units = Unit::where('POSID', '=', $POSID)->get();
        $suppliers = Supplier::where('POSID', '=', $POSID)->get();
        $shops = Shop::where('POSID', '=', $POSID)->get();
        // Get staff designation
        $staffDesignation = EmployeeDesignation::where('POSID', $POSID)
            ->where('name', 'Staff')
            ->first();
        
        // Get only staffs (employees with Staff designation)
        $employees = Employee::where('POSID', '=', $POSID)
            ->where('status', 'Active')
            ->when($staffDesignation, function($query) use ($staffDesignation) {
                return $query->where('designation_id', $staffDesignation->id);
            })
            ->orderBy('name')
            ->get();

        return view('service.index', [
            'brands' => $brands,
            'categories' => $categories,
            'units' => $units,
            'suppliers' => $suppliers,
            'shops' => $shops,
            'employees' => $employees
        ]);
    }

    public function datatable(Request $request)
    {
        $POSID = auth()->user()->POSID;
        $searchCriteria = $request->input('search');

        $query = Product::where('products.POSID', $POSID)
            ->with('creator', 'staff')
            ->where('type', 'Service')
            ->where(function($query) use ($searchCriteria) {
                $query->where('code', 'like', "%{$searchCriteria}%")
                      ->orWhere('name', 'like', "%{$searchCriteria}%");
            });

        $totalRecord = Product::where('POSID', $POSID)->where('type', 'Service')->count();
        $filteredRecord = $query->count();

        // Handle sorting
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        // Special handling for each column
        if ($orderColumn == 0) {
            // Order by ID
            $services = (clone $query)->orderBy('id', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 1) {
            // Order by code
            $services = (clone $query)->orderBy('code', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 3) {
            // Order by name
            $services = (clone $query)->orderBy('name', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 4) {
            // Order by price
            $services = (clone $query)->orderBy('price', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } else {
            // Default sorting by ID descending
            $services = (clone $query)->orderBy('id', 'desc')
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        }

        $services->transform(function($service) {
            $service->formattedDate = formatDate($service->created_at);
            $service->formattedTime = formatTime($service->created_at);
            
            if ($service->created_by == null) {
                $service->createdBy = 'CustomData';
            } else {
                $service->createdBy = $service->creator->name ?? 'N/A';
            }

            return $service;
        });

        $result = [];
        $result["draw"] = $request->input('draw');
        $result["recordsTotal"] = $totalRecord;
        $result["recordsFiltered"] = $filteredRecord;
        $result['data'] = $services->toArray();

        return response()->json($result);
    }

    public function edit($id){
        $POSID = auth()->user()->POSID;
        $service = Product::with('categories','salesItemServices', 'staff')->where('POSID', $POSID)
            ->where('id', $id)->where('type', 'Service')
            ->first();
        
        // Check if service has sales
        $hasSales = $service->salesItemServices()->count() > 0;
        
        return response()->json([
            'status'  => 'success',
            'service' => $service,
            'categories' => $service->categories,
            'hasSales' => $hasSales
        ]);
    }

    public function copy($id){
        $POSID = auth()->user()->POSID;
        $service = Product::with('categories')->where('POSID', $POSID)
            ->where('id', $id)->where('type', 'Service')
            ->first();
        
        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found.'
            ], 404);
        }
        
        return response()->json([
            'status'  => 'success',
            'service' => [
                'name' => $service->name,
                'price' => $service->price,
                'description' => $service->description,
            ],
            'categories' => $service->categories
        ]);
    }

    public function show($id){
        $POSID = auth()->user()->POSID;
        $service = Product::with('creator', 'updater', 'brand', 'categories', 'unit', 'salesItemServices', 'staff')
            ->where('POSID', $POSID)->where('type', 'Service')
            ->where('id', $id)
            ->first();

        // Created/Updated By
        $service->createdBy = $service->creator->name ?? 'CustomData';
        $service->updatedBy = $service->updater->name ?? '';

        // Get the latest sale
        $lastSale = $service->salesItemServices()->latest()->first();

        $service->lastSaleAt = $lastSale ? formatDateAndTime($lastSale->created_at) : null;

        // Total number of sales
        $service->totalSalesCount = $service->salesItemServices()->count();

        // Total amount of sales
        $service->totalSalesAmount = $service->salesItemServices->sum(function($item) {
            return ($item->selling_price - $item->discount) * $item->quantity;
        });

        // Fetch related Sales (sales) for this service
        $sales = Sales::where('POSID', $POSID)
            ->whereHas('items', function($query) use ($id) {
                $query->where('product_id', $id);
            })
            ->with(['customer', 'items' => function($query) use ($id) {
                $query->where('product_id', $id);
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($sales) use ($id) {
                // Get the quantity of this service in this sales
                $item = $sales->items->first();
                $sales->product_quantity = $item ? $item->quantity : 0;
                $sales->formatted_date = formatDate($sales->created_at);
                $sales->customer_name = $sales->customer ? $sales->customer->name : 'Walk-in Customer';
                $sales->customer_id = $sales->customerId;
                return $sales;
            });

        return view('service.show', compact('service', 'sales'));
    }


    public function store(Request $request){

        try{
            $POSID = auth()->user()->POSID;
            $request->validate([
                'code' => [
                    'required',
                    'string',
                    'min:3',
                    'max:20',
                    Rule::unique('products', 'code')
                        ->where('POSID', $POSID),
                ],
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:200',
                    Rule::unique('products', 'name')
                        ->where('POSID', $POSID),
                ],
                'category_id' => 'required',
                'price' => 'required|numeric',
                'description' => 'nullable|string|min:3'
            ]);

            $service = new Product();
            $service->POSID         = $POSID;
            $service->code          = (session('accountInfo.serviceCodePrefix') ?? 'AU').'-'.$request->code;
            $service->name          = $request->name;
            $service->price             = (float)$request->price;
            $service->description       = $request->description;
            $service->staff_id     = $request->staff_id ?: null;
            $service->created_by        = auth()->user()->id;

            $POSID = auth()->user()->POSID;
            if ($request->has('image')) {
                $base64Image = $request->input('image');

                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $extension = strtolower($type[1]);

                    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        return response()->json(['error' => 'Invalid image type'], 422);
                    }
                } else {
                    return response()->json(['error' => 'Invalid base64 image format'], 422);
                }

                $base64Image = str_replace(' ', '+', $base64Image);
                $imageData = base64_decode($base64Image);

                if ($imageData === false) {
                    return response()->json(['error' => 'base64_decode failed'], 422);
                }

                $directory = public_path("images/{$POSID}/services");

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $fileName = uniqid() . '.' . $extension;
                $filePath = $directory . '/' . $fileName;
                file_put_contents($filePath, $imageData);

                $service->image = $fileName;
            }

            $service->save();
            $service->categories()->attach($request->category_id);
            
            $service->formattedDate = formatDate($service->created_at);
            $service->formattedTime = formatTime($service->created_at);
            $service->createdBy = auth()->user()->name;
            

            return response()->json([
                'status'    => 'success',
                'message'   => 'Service Created Successfully.',
                'service'  => $service
            ]);
        
            
        }catch(ValidationException $exception){
            return response()->json([
                'status'    => 'error',
                'message'   => '',
                'errors'    => $exception->validator->errors()
            ]);
        }catch(\Exception $exception){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong.',
            ]);
        }
    } // end store

    public function update(Request $request, $id){
        try{
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'code' => [
                    'required',
                    'string',
                    'min:3',
                    'max:20',
                    Rule::unique('products', 'code')
                        ->where('POSID', $POSID)
                        ->ignore($id),   // ignore current service
                ],

                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:200',
                    Rule::unique('products', 'name')
                        ->where('POSID', $POSID)
                        ->ignore($id),   // ignore current service
                ],

                'category' => 'required',
                'description' => 'nullable|string|min:3'
            ]);

            $service = Product::with('salesItemServices')->where('POSID', $POSID)->where('type', 'Service')->where('id', $id)->first();

            // Check if service has sales and prevent price change
            $hasSales = $service->salesItemServices()->count() > 0;
            if ($hasSales && $service->price != (float)$request->price) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This service already has sales, so the price cannot be changed.',
                    'errors' => [
                        'price' => ['This service already has sales, so the price cannot be changed.']
                    ]
                ]);
            }else{
                // we need to validate the price required and numeric
                $request->validate([
                    'price' => 'required|numeric',
                ]);
                
            }

            $service->name          = $request->name;
            $service->price             = (float)$request->price;
            $service->description       = $request->description;
            $service->staff_id     = $request->staff_id ?: null;
            $service->updated_by        = auth()->id();

            $POSID = auth()->user()->POSID;
            if ($request->has('image')) {
                $base64Image = $request->input('image');

                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $extension = strtolower($type[1]);

                    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        return response()->json(['error' => 'Invalid image type'], 422);
                    }
                } else {
                    return response()->json(['error' => 'Invalid base64 image format'], 422);
                }

                $base64Image = str_replace(' ', '+', $base64Image);
                $imageData = base64_decode($base64Image);

                if ($imageData === false) {
                    return response()->json(['error' => 'base64_decode failed'], 422);
                }

                $directory = public_path("images/{$POSID}/services");

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                if ($service->image) {
                    $existingFile = $directory . '/' . $service->image;
                    if (file_exists($existingFile)) {
                        unlink($existingFile);
                    }
                }

                $fileName = uniqid() . '.' . $extension;
                $filePath = $directory . '/' . $fileName;
                file_put_contents($filePath, $imageData);

                $service->image = $fileName;
            }

            $service->save();
          
            if ($request->has('category')) {
                $categories = is_array($request->category)
                    ? $request->category
                    : [$request->category];

                $service->categories()->sync($categories);
            }

            $service->formattedDate = formatDate($service->created_at);
            $service->formattedTime = formatTime($service->created_at);

            if ($service->created_by == null) {
                $service->createdBy = 'CustomData';
            }else{
                $service->createdBy = $service->creator->name;
            }
            
            return response()->json([
                'status'    => 'success',
                'message'   => 'Service Updated Successfully.',
                'service'  => $service
            ]);
            
        }catch(ValidationException $exception){
            return response()->json([
                'status'    => 'error',
                'message'   => '',
                'errors'    => $exception->validator->errors()
            ]);
        }catch(\Exception $exception){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong.',
            ]);
        }
    } // end update

    public function destroy($id){
        try {
            $POSID = auth()->user()->POSID;
            $service = Product::where('POSID', $POSID)
                ->where('id', $id)->where('type', 'Service')
                ->first();

            if($service->salesItemServices()->count() > 0){
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['This service has sales items.']
                    ],
                ]);

            }else{
                // we can introduce a soft delete here
                // but now we can delete a service as it has no dependencies
                $service->delete();

                //after delete we can delete the image
                if($service->image){
                    $filePath = public_path("images/{$POSID}/services/{$service->image}");
                    if(file_exists($filePath)){
                        unlink($filePath);
                    }
                }
            }
            
            return response()->json([
                'status'    => 'success',
                'message'   => 'Service Deleted Successfully.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong.',
            ]);
        }
    }
}