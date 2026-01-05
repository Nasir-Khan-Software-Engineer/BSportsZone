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
use Illuminate\Support\Facades\DB;
use Exception;

class ProductController extends Controller
{
    public function index()
    {
        $POSID = auth()->user()->POSID;
        $brands = Brand::where('POSID', '=', $POSID)->get();
        $categories = Category::where('POSID', '=', $POSID)->get();
        $units = Unit::where('POSID', '=', $POSID)->get();
        $suppliers = Supplier::where('POSID', '=', $POSID)->get();

        return view('product.index', [
            'brands' => $brands,
            'categories' => $categories,
            'units' => $units,
            'suppliers' => $suppliers
        ]);
    }

    public function datatable(Request $request)
    {
        $POSID = auth()->user()->POSID;
        $searchCriteria = $request->input('search');

        $query = Product::where('products.POSID', $POSID)
            ->with('creator', 'variations.purchaseItems')
            ->where('type', 'Product')
            ->where(function($query) use ($searchCriteria) {
                $query->where('code', 'like', "%{$searchCriteria}%")
                      ->orWhere('name', 'like', "%{$searchCriteria}%");
            });

        $totalRecord = Product::where('POSID', $POSID)->where('type', 'Product')->count();
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
        } elseif ($orderColumn == 2) {
            // Order by name
            $products = (clone $query)->orderBy('name', $orderDir)
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        } else {
            // Default sorting by ID descending (other columns are not sortable)
            $products = (clone $query)->orderBy('id', 'desc')
                ->skip($request->input('start'))
                ->take($request->input('length'))
                ->get();
        }

        $products->transform(function($product) {
            $product->formattedDate = formatDate($product->created_at);
            $product->formattedTime = formatTime($product->created_at);
            $product->variations_count = $product->variations->count();
            
            // Calculate salable stocks (sum of all active variations' stock)
            $product->salable_stocks = $product->variations->where('status', 'active')->sum('stock');
            
            // Calculate warehouse stocks (sum of all purchase items' unallocated_qty for all variations)
            $product->warehouse_stocks = 0;
            $costPrices = [];
            foreach ($product->variations as $variation) {
                foreach ($variation->purchaseItems as $purchaseItem) {
                    $product->warehouse_stocks += $purchaseItem->unallocated_qty;
                    if ($purchaseItem->cost_price > 0) {
                        $costPrices[] = $purchaseItem->cost_price;
                    }
                }
            }
            
            // Calculate cost price range
            if (!empty($costPrices)) {
                $minCost = min($costPrices);
                $maxCost = max($costPrices);
                $product->cost_price_range = $minCost == $maxCost 
                    ? number_format($minCost, 2) 
                    : number_format($minCost, 2) . ' - ' . number_format($maxCost, 2);
            } else {
                $product->cost_price_range = '-';
            }
            
            // Calculate selling price range (from variations)
            $sellingPrices = $product->variations->where('status', 'active')->pluck('selling_price')->filter(function($price) {
                return $price > 0;
            })->toArray();
            
            if (!empty($sellingPrices)) {
                $minSelling = min($sellingPrices);
                $maxSelling = max($sellingPrices);
                $product->selling_price_range = $minSelling == $maxSelling 
                    ? number_format($minSelling, 2) 
                    : number_format($minSelling, 2) . ' - ' . number_format($maxSelling, 2);
            } else {
                $product->selling_price_range = '-';
            }
            
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

    public function edit($id)
    {
        $POSID = auth()->user()->POSID;
        $product = Product::with('categories', 'salesItemProducts', 'variations', 'brand', 'unit', 'supplier')->where('POSID', $POSID)
            ->where('id', $id)->where('type', 'Product')
            ->first();
        
        if (!$product) {
            abort(404, 'Product not found.');
        }
        
        $brands = Brand::where('POSID', '=', $POSID)->get();
        $categories = Category::where('POSID', '=', $POSID)->get();
        $units = Unit::where('POSID', '=', $POSID)->get();
        $suppliers = Supplier::where('POSID', '=', $POSID)->get();
        
        return view('product.edit', [
            'product' => $product,
            'brands' => $brands,
            'categories' => $categories,
            'units' => $units,
            'suppliers' => $suppliers
        ]);
    }

    public function show($id)
    {
        $POSID = auth()->user()->POSID;
        $product = Product::with('creator', 'updater', 'brand', 'categories', 'unit', 'supplier', 'salesItemProducts', 'variations')
            ->where('POSID', $POSID)->where('type', 'Product')
            ->where('id', $id)
            ->first();

        if (!$product) {
            abort(404, 'Product not found.');
        }

        // Created/Updated By
        $product->createdBy = $product->creator->name ?? 'CustomData';
        $product->updatedBy = $product->updater->name ?? '';

        // Get the latest sale
        $lastSale = $product->salesItemProducts()->latest()->first();

        $product->lastSaleAt = $lastSale ? formatDateAndTime($lastSale->created_at) : null;

        // Total number of sales
        $product->totalSalesCount = $product->salesItemProducts()->count();

        // Total amount of sales
        $product->totalSalesAmount = $product->salesItemProducts->sum(function($item) {
            return ($item->selling_price - $item->discount) * $item->quantity;
        });

        return view('product.show', [
            'product' => $product
        ]);
    }

    public function store(Request $request)
    {
        try {
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
                'description' => 'nullable|string|min:3'
            ]);

            $product = new Product();
            $product->POSID = $POSID;
            $product->code = (session('accountInfo.productCodePrefix') ?? 'PR').'-'.$request->code;
            $product->name = $request->name;
            $product->type = 'Product';
            $product->price = 0; // Products don't have price, variations do
            $product->description = $request->description;
            $product->unit_id = $request->unit_id ?: null;
            $product->brand_id = $request->brand_id ?: null;
            $product->supplier_id = $request->supplier_id ?: null;
            $product->created_by = auth()->user()->id;

            $product->save();
            $product->categories()->attach($request->category_id);
            
            $product->formattedDate = formatDate($product->created_at);
            $product->formattedTime = formatTime($product->created_at);
            $product->createdBy = auth()->user()->name;

            return response()->json([
                'status'    => 'success',
                'message'   => 'Product Created Successfully.',
                'product'  => $product,
                'redirect' => route('product.edit', $product->id)
            ]);
        
        } catch(ValidationException $exception) {
            return response()->json([
                'status'    => 'error',
                'message'   => '',
                'errors'    => $exception->validator->errors()
            ]);
        } catch(\Exception $exception) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong.',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'code' => [
                    'required',
                    'string',
                    'min:3',
                    'max:20',
                    Rule::unique('products', 'code')
                        ->where('POSID', $POSID)
                        ->ignore($id),
                ],
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:200',
                    Rule::unique('products', 'name')
                        ->where('POSID', $POSID)
                        ->ignore($id),
                ],
                'category' => 'required',
                'description' => 'nullable|string|min:3'
            ]);

            $product = Product::with('salesItemProducts')->where('POSID', $POSID)->where('type', 'Product')->where('id', $id)->first();

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found.'
                ], 404);
            }

            // Products don't have price, variations do
            $product->name = $request->name;
            $product->description = $request->description;
            $product->unit_id = $request->unit_id ?: null;
            $product->brand_id = $request->brand_id ?: null;
            $product->supplier_id = $request->supplier_id ?: null;
            $product->updated_by = auth()->id();

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
            } else {
                $product->createdBy = $product->creator->name;
            }
            
            return response()->json([
                'status'    => 'success',
                'message'   => 'Product Updated Successfully.',
                'product'  => $product
            ]);
            
        } catch(ValidationException $exception) {
            return response()->json([
                'status'    => 'error',
                'message'   => '',
                'errors'    => $exception->validator->errors()
            ]);
        } catch(\Exception $exception) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong.',
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $POSID = auth()->user()->POSID;
            $product = Product::where('POSID', $POSID)
                ->where('id', $id)->where('type', 'Product')
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found.'
                ], 404);
            }

            if($product->salesItemProducts()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['This product has sales items.']
                    ],
                ]);
            } else {
                // Delete variations first
                $product->variations()->delete();
                
                // Delete the product
                $product->delete();
            }
            
            return response()->json([
                'status'    => 'success',
                'message'   => 'Product Deleted Successfully.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong.',
            ]);
        }
    }
}
