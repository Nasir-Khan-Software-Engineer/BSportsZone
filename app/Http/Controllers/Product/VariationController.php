<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Variation;
use App\Models\Product;
use Exception;

class VariationController extends Controller
{
    public function index(Request $request)
    {
        $POSID = auth()->user()->POSID;
        $productId = $request->input('product_id');

        if (!$productId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product ID is required.'
            ], 400);
        }

        // Verify product exists and belongs to the user's POSID
        $product = Product::where('POSID', $POSID)
            ->where('id', $productId)
            ->where('type', 'Product')
            ->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.'
            ], 404);
        }

        $variations = Variation::where('product_id', $productId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'variations' => $variations
        ]);
    }

    public function datatable(Request $request)
    {
        $POSID = auth()->user()->POSID;
        $productId = $request->input('product_id');
        $searchCriteria = $request->input('search', '');

        if (!$productId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product ID is required.'
            ], 400);
        }

        // Verify product exists and belongs to the user's POSID
        $product = Product::where('POSID', $POSID)
            ->where('id', $productId)
            ->where('type', 'Product')
            ->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.'
            ], 404);
        }

        $query = Variation::where('product_id', $productId)
            ->where(function($query) use ($searchCriteria) {
                $query->where('description', 'like', "%{$searchCriteria}%")
                      ->orWhere('status', 'like', "%{$searchCriteria}%");
            });

        $totalRecord = Variation::where('product_id', $productId)->count();
        $filteredRecord = $query->count();

        // Handle sorting
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $variations = (clone $query)->orderBy('id', $orderDir)
            ->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        $variations->transform(function($variation) {
            $variation->formattedDate = formatDate($variation->created_at);
            $variation->formattedTime = formatTime($variation->created_at);
            return $variation;
        });

        $result = [];
        $result["draw"] = $request->input('draw');
        $result["recordsTotal"] = $totalRecord;
        $result["recordsFiltered"] = $filteredRecord;
        $result['data'] = $variations->toArray();

        return response()->json($result);
    }

    public function show($id)
    {
        $POSID = auth()->user()->POSID;
        $variation = Variation::with('product')
            ->where('id', $id)
            ->first();

        if (!$variation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Variation not found.'
            ], 404);
        }

        // Verify product belongs to the user's POSID
        if ($variation->product->POSID != $POSID) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access.'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'variation' => $variation
        ]);
    }

    public function store(Request $request)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'description' => 'nullable|string|max:1000',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'status' => 'nullable|string|in:active,inactive'
            ]);

            // Verify product exists and belongs to the user's POSID
            $product = Product::where('POSID', $POSID)
                ->where('id', $request->product_id)
                ->where('type', 'Product')
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found or invalid.'
                ], 404);
            }

            $variation = new Variation();
            $variation->product_id = $request->product_id;
            $variation->description = $request->description;
            $variation->cost_price = (float)$request->cost_price;
            $variation->selling_price = (float)$request->selling_price;
            $variation->stock = (int)$request->stock;
            $variation->status = $request->status ?? 'active';

            $variation->save();

            $variation->formattedDate = formatDate($variation->created_at);
            $variation->formattedTime = formatTime($variation->created_at);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Variation Created Successfully.',
                'variation' => $variation
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
                'description' => 'nullable|string|max:1000',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'status' => 'nullable|string|in:active,inactive'
            ]);

            $variation = Variation::with('product')
                ->where('id', $id)
                ->first();

            if (!$variation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Variation not found.'
                ], 404);
            }

            // Verify product belongs to the user's POSID
            if ($variation->product->POSID != $POSID) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.'
                ], 403);
            }

            $variation->description = $request->description;
            $variation->cost_price = (float)$request->cost_price;
            $variation->selling_price = (float)$request->selling_price;
            $variation->stock = (int)$request->stock;
            $variation->status = $request->status ?? 'active';

            $variation->save();

            $variation->formattedDate = formatDate($variation->created_at);
            $variation->formattedTime = formatTime($variation->created_at);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Variation Updated Successfully.',
                'variation' => $variation
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
            $variation = Variation::with('product')
                ->where('id', $id)
                ->first();

            if (!$variation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Variation not found.'
                ], 404);
            }

            // Verify product belongs to the user's POSID
            if ($variation->product->POSID != $POSID) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.'
                ], 403);
            }

            $variation->delete();
            
            return response()->json([
                'status'    => 'success',
                'message'   => 'Variation Deleted Successfully.'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Something went wrong.',
            ]);
        }
    }
}
