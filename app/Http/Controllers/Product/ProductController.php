<?php

namespace App\Http\Controllers\Product;

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
use App\Models\ProductStock;
use App\Models\Purchases;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use Illuminate\Support\Facades\DB;
use Exception;


class ProductController extends Controller
{
    public function index(){
        $posid = auth()->user()->posid;
        $brands = Brand::where('posid', '=', $posid)->get();
        $categories = Category::where('posid', '=', $posid)->get();
        $units = Unit::where('posid', '=', $posid)->get();
        $suppliers = Supplier::where('posid', '=', $posid)->get();
        $shops = Shop::where('posid', '=', $posid)->get();
        // Get beautician designation
        $beauticianDesignation = EmployeeDesignation::where('posid', $posid)
            ->where('name', 'Beautician')
            ->first();
        
        // Get only beauticians (employees with Beautician designation)
        $employees = Employee::where('posid', '=', $posid)
            ->where('status', 'Active')
            ->when($beauticianDesignation, function($query) use ($beauticianDesignation) {
                return $query->where('designation_id', $beauticianDesignation->id);
            })
            ->orderBy('name')
            ->get();

        return view('product.index', [
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
        $posid = auth()->user()->posid;
        $searchCriteria = $request->input('search');

        $query = Product::where('products.posid', $posid)
            ->with('creator', 'beautician')
            ->where(function($query) use ($searchCriteria) {
                $query->where('code', 'like', "%{$searchCriteria}%")
                      ->orWhere('name', 'like', "%{$searchCriteria}%");
            });

        $totalRecord = Product::where('posid', $posid)->count();
        $filteredRecord = $query->count();

        // Handle sorting
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        // Special handling for each column
        if ($orderColumn == 0) {
            // Order by ID
            $products = (clone $query)->orderBy('id', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 1) {
            // Order by code
            $products = (clone $query)->orderBy('code', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 3) {
            // Order by name
            $products = (clone $query)->orderBy('name', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } elseif ($orderColumn == 4) {
            // Order by price
            $products = (clone $query)->orderBy('price', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } else {
            // Default sorting by ID descending
            $products = (clone $query)->orderBy('id', 'desc')
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        }

        $products->transform(function($product) {
            $product->formattedDate = formatDate($product->created_at);
            $product->formattedTime = formatTime($product->created_at);
            
            if ($product->created_by == null) {
                $product->createdBy = 'CustomData';
            } else {
                $product->createdBy = $product->creator->name ?? 'N/A';
            }

            return $product;
        });

        $result = [];
        $result["draw"] = $request->input('draw');
        $result["recordsTotal"] = $totalRecord;
        $result["recordsFiltered"] = $filteredRecord;
        $result['data'] = $products->toArray();

        return response()->json($result);
    }

    public function edit($id){
        $posid = auth()->user()->posid;
        $product = Product::with('categories','sales_items', 'beautician')->where('posid', $posid)
            ->where('id', $id)
            ->first();
        
        // Check if product has sales
        $hasSales = $product->sales_items()->count() > 0;
        
        return response()->json([
            'status'  => 'success',
            'product' => $product,
            'categories' => $product->categories,
            'hasSales' => $hasSales
        ]);
    }

    public function copy($id){
        $posid = auth()->user()->posid;
        $product = Product::with('categories')->where('posid', $posid)
            ->where('id', $id)
            ->first();
        
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found.'
            ], 404);
        }
        
        return response()->json([
            'status'  => 'success',
            'product' => [
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
            ],
            'categories' => $product->categories
        ]);
    }

    public function show($id){
        $posid = auth()->user()->posid;
        $product = Product::with('creator', 'updater', 'brand', 'categories', 'unit', 'sales_items', 'beautician')
            ->where('posid', $posid)
            ->where('id', $id)
            ->first();

        // Created/Updated By
        $product->createdBy = $product->creator->name ?? 'CustomData';
        $product->updatedBy = $product->updater->name ?? '';

        // Get the latest sale
        $lastSale = $product->sales_items()->latest()->first();

        $product->lastSaleAt = $lastSale ? formatDateAndTime($lastSale->created_at) : null;

        // Total number of sales
        $product->totalSalesCount = $product->sales_items()->count();

        // Total amount of sales
        $product->totalSalesAmount = $product->sales_items->sum(function($item) {
            return ($item->selling_price - $item->discount) * $item->quantity;
        });

        // Fetch related purchases (sales) for this product
        $sales = Purchases::where('posid', $posid)
            ->whereHas('items', function($query) use ($id) {
                $query->where('product_id', $id);
            })
            ->with(['customer', 'items' => function($query) use ($id) {
                $query->where('product_id', $id);
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($purchase) use ($id) {
                // Get the quantity of this product in this purchase
                $item = $purchase->items->first();
                $purchase->product_quantity = $item ? $item->quantity : 0;
                $purchase->formatted_date = formatDate($purchase->created_at);
                $purchase->customer_name = $purchase->customer ? $purchase->customer->name : 'Walk-in Customer';
                $purchase->customer_id = $purchase->customerId;
                return $purchase;
            });

        return view('product.show', compact('product', 'sales'));
    }


    public function store(Request $request){

        try{
            $posid = auth()->user()->posid;
            $request->validate([
                'code' => [
                    'required',
                    'string',
                    'min:3',
                    'max:20',
                    Rule::unique('products', 'code')
                        ->where('posid', $posid),
                ],
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:200',
                    Rule::unique('products', 'name')
                        ->where('posid', $posid),
                ],
                'category_id' => 'required',
                'price' => 'required|numeric',
                'description' => 'nullable|string|min:3'
            ]);

            $product = new Product();
            $product->posid         = $posid;
            $product->code          = (session('accountInfo.productCodePrefix') ?? 'AU').'-'.$request->code;
            $product->name          = $request->name;
            $product->price             = (float)$request->price;
            $product->description       = $request->description;
            $product->beautician_id     = $request->beautician_id ?: null;
            $product->created_by        = auth()->user()->id;

            $posid = auth()->user()->posid;
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

                $directory = public_path("images/{$posid}/products");

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $fileName = uniqid() . '.' . $extension;
                $filePath = $directory . '/' . $fileName;
                file_put_contents($filePath, $imageData);

                $product->image = $fileName;
            }

            $product->save();
            $product->categories()->attach($request->category_id);
            
            $product->formattedDate = formatDate($product->created_at);
            $product->formattedTime = formatTime($product->created_at);
            $product->createdBy = auth()->user()->name;
            

            return response()->json([
                'status'    => 'success',
                'message'   => 'Service Created Successfully.',
                'product'  => $product
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
            $posid = auth()->user()->posid;
            
            $request->validate([
                'code' => [
                    'required',
                    'string',
                    'min:3',
                    'max:20',
                    Rule::unique('products', 'code')
                        ->where('posid', $posid)
                        ->ignore($id),   // ignore current product
                ],

                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:200',
                    Rule::unique('products', 'name')
                        ->where('posid', $posid)
                        ->ignore($id),   // ignore current product
                ],

                'category' => 'required',
                'description' => 'nullable|string|min:3'
            ]);

            $product = Product::with('sales_items')->where('posid', $posid)->where('id', $id)->first();

            // Check if product has sales and prevent price change
            $hasSales = $product->sales_items()->count() > 0;
            if ($hasSales && $product->price != (float)$request->price) {
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

            $product->name          = $request->name;
            $product->price             = (float)$request->price;
            $product->description       = $request->description;
            $product->beautician_id     = $request->beautician_id ?: null;
            $product->updated_by        = auth()->id();

            $posid = auth()->user()->posid;
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

                $directory = public_path("images/{$posid}/products");

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                if ($product->image) {
                    $existingFile = $directory . '/' . $product->image;
                    if (file_exists($existingFile)) {
                        unlink($existingFile);
                    }
                }

                $fileName = uniqid() . '.' . $extension;
                $filePath = $directory . '/' . $fileName;
                file_put_contents($filePath, $imageData);

                $product->image = $fileName;
            }

            $product->save();
          
            if ($request->has('category')) {
                $categories = is_array($request->category)
                    ? $request->category
                    : [$request->category];

                $product->categories()->sync($categories);
            }

            $product->formattedDate = formatDate($product->created_at);
            $product->formattedTime = formatTime($product->created_at);

            if ($product->created_by == null) {
                $product->createdBy = 'CustomData';
            }else{
                $product->createdBy = $product->creator->name;
            }
            
            return response()->json([
                'status'    => 'success',
                'message'   => 'Service Updated Successfully.',
                'product'  => $product
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
            $posid = auth()->user()->posid;
            $product = Product::where('posid', $posid)
                ->where('id', $id)
                ->first();

            if($product->sales_items()->count() > 0){
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['This service has sales items.']
                    ],
                ]);

            }else{
                // we can introduce a soft delete here
                // but now we can delete a product as it has no dependencies
                $product->delete();

                //after delete we can delete the image
                if($product->image){
                    $filePath = public_path("images/{$posid}/products/{$product->image}");
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