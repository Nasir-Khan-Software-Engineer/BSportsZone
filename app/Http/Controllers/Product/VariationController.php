<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Variation;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\Sales_items;
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
                'tagline' => 'required|string|max:255|unique:variations,tagline',
                'description' => 'nullable|string|max:1000',
                'selling_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:1',
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
            $variation->tagline = $request->tagline;
            $variation->description = $request->description;
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
                'tagline' => 'required|string|max:255|unique:variations,tagline,' . $id,
                'description' => 'nullable|string|max:1000',
                'selling_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:1',
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

            $variation->tagline = $request->tagline;
            $variation->description = $request->description;
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

    public function getPurchaseItems($id)
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

            // Get all purchase items for this variation with purchase details
            $purchaseItems = PurchaseItem::with(['purchase'])
                ->where('product_variant_id', $id)
                ->where('unallocated_qty', '>', 0) // Only items with available stock
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate total sold items for this variation
            $soldItemsQty = Sales_items::where('variation_id', $id)
                ->where('POSID', $POSID)
                ->sum('quantity');

            // Format the data for the frontend
            $formattedItems = $purchaseItems->map(function($item) use ($variation) {
                return [
                    'id' => $item->id,
                    'purchase_id' => $item->purchase_id,
                    'invoice_number' => $item->purchase->invoice_number ?? 'N/A',
                    'purchase_date' => $item->purchase->purchase_date ? formatDate($item->purchase->purchase_date) : 'N/A',
                    'available_stock' => $item->unallocated_qty,
                    'cost_price' => $item->cost_price,
                    'selling_price' => $variation->selling_price,
                    'sold_items' => 0, // Placeholder as per PRD - will be updated later
                ];
            });

            return response()->json([
                'status' => 'success',
                'variation' => [
                    'id' => $variation->id,
                    'tagline' => $variation->tagline,
                    'selling_price' => $variation->selling_price,
                    'current_stock' => $variation->stock,
                    'sold_items_qty' => $soldItemsQty ?? 0,
                ],
                'purchase_items' => $formattedItems
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function addStockFromPurchaseItem(Request $request, $id)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'purchase_item_id' => 'required|exists:purchase_items,id',
                'quantity' => 'required|integer|min:1',
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

            $purchaseItem = PurchaseItem::findOrFail($request->purchase_item_id);

            // Verify purchase item belongs to this variation
            if ($purchaseItem->product_variant_id != $id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid purchase item for this variation.'
                ], 400);
            }

            // Verify available quantity
            if ($request->quantity > $purchaseItem->unallocated_qty) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient available stock. Available: ' . $purchaseItem->unallocated_qty
                ], 400);
            }

            // Update variation stock
            $variation->stock += $request->quantity;
            $variation->save();

            // Update purchase item unallocated quantity
            $purchaseItem->unallocated_qty -= $request->quantity;
            $purchaseItem->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Stock added successfully.',
                'variation' => [
                    'id' => $variation->id,
                    'stock' => $variation->stock,
                ]
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors()
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function getPriceUpdateInfo($id)
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

            // Check if there are any sales for this variation
            $soldItemsQty = Sales_items::where('variation_id', $id)
                ->where('POSID', $POSID)
                ->sum('quantity');

            $hasSales = $soldItemsQty > 0;

            // Get cost price from purchase items (weighted average or latest)
            $purchaseItems = PurchaseItem::where('product_variant_id', $id)
                ->where('purchased_qty', '>', 0)
                ->orderBy('created_at', 'desc')
                ->get();

            $costPrice = null;
            if ($purchaseItems->count() > 0) {
                // Calculate weighted average cost price
                $totalCost = 0;
                $totalQty = 0;
                foreach ($purchaseItems as $item) {
                    $totalCost += $item->cost_price * $item->purchased_qty;
                    $totalQty += $item->purchased_qty;
                }
                if ($totalQty > 0) {
                    $costPrice = $totalCost / $totalQty;
                }
            }

            return response()->json([
                'status' => 'success',
                'variation' => [
                    'id' => $variation->id,
                    'tagline' => $variation->tagline,
                    'selling_price' => $variation->selling_price,
                    'current_stock' => $variation->stock,
                ],
                'has_sales' => $hasSales,
                'sold_items_qty' => $soldItemsQty ?? 0,
                'cost_price' => $costPrice
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
