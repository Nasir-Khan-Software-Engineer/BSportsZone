<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Variation;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\Sales_items;
use Illuminate\Support\Facades\DB;
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
            ->where('POSID', $POSID)
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
            ->where('POSID', $POSID)
            ->where(function($query) use ($searchCriteria) {
                $query->where('description', 'like', "%{$searchCriteria}%")
                      ->orWhere('status', 'like', "%{$searchCriteria}%");
            });

        $totalRecord = Variation::where('POSID', $POSID)->where('product_id', $productId)->count();
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
            ->where('POSID', $POSID)
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
                'tagline' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
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

            // Check if there's already an active variation with the same tagline
            $status = $request->status ?? 'active';
            if ($status === 'active') {
                $existingActive = Variation::where('tagline', $request->tagline)
                    ->where('POSID', $POSID)
                    ->where('status', 'active')
                    ->exists();
                
                if ($existingActive) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'An active variation with this tagline already exists.',
                        'errors' => [
                            'tagline' => ['An active variation with this tagline already exists.']
                        ]
                    ]);
                }
            }

            $variation = new Variation();
            $variation->POSID = $POSID;
            $variation->product_id = $request->product_id;
            $variation->tagline = $request->tagline;
            $variation->description = $request->description;
            $variation->selling_price = 0;
            $variation->stock = 0;
            $variation->status = $status;

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
                'tagline' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'selling_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'status' => 'nullable|string|in:active,inactive'
            ]);

            $variation = Variation::with('product')
                ->where('id', $id)
                ->where('POSID', $POSID)
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

            // Check if variation has sales items - if yes, cannot edit (except status)
            $salesItemsCount = Sales_items::where('variation_id', $id)
                ->where('POSID', $POSID)
                ->where('type', 'Product')
                ->count();

            // Get the new status
            $status = $request->status ?? $variation->status;
            $taglineChanged = $request->tagline !== $variation->tagline;
            $statusChanged = $status !== $variation->status;
            
            // If variation has sales items, only allow status change, not other fields
            if ($salesItemsCount > 0) {
                // Check if any field other than status is being changed
                $otherFieldsChanged = $taglineChanged || 
                    ($request->has('description') && $request->description !== $variation->description) ||
                    ($request->has('selling_price') && (float)$request->selling_price !== (float)$variation->selling_price) ||
                    ($request->has('stock') && (int)$request->stock !== (int)$variation->stock);
                
                if ($otherFieldsChanged) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot edit variation. This variation has ' . $salesItemsCount . ' sales item(s) associated with it. Only status can be changed.'
                    ], 422);
                }
            }

            // Check if there's already an active variation with the same tagline (excluding current)
            // This check applies when status is being set to active
            // Tagline must be unique for active variations
            if ($status === 'active') {
                $taglineToCheck = $request->tagline ?? $variation->tagline;
                $existingActive = Variation::where('tagline', $taglineToCheck)
                    ->where('POSID', $POSID)
                    ->where('status', 'active')
                    ->where('id', '!=', $id)
                    ->exists();
                
                if ($existingActive) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'An active variation with this tagline already exists. Tagline must be unique for active variations.',
                        'errors' => [
                            'tagline' => ['An active variation with this tagline already exists. Tagline must be unique for active variations.']
                        ]
                    ], 422);
                }
            }

            $variation->tagline = $request->tagline;
            $variation->description = $request->description;
            $variation->selling_price = (float)$request->selling_price;
            $variation->stock = (int)$request->stock;
            $variation->status = $status;

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
                ->where('POSID', $POSID)
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

            // Check if variation has purchase items (any purchase items, not just unallocated)
            $purchaseItemsCount = PurchaseItem::where('product_variant_id', $id)
                ->count();

            if ($purchaseItemsCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete variation. This variation has ' . $purchaseItemsCount . ' purchase item(s) associated with it.'
                ], 422);
            }

            // Check if variation has sales items
            $salesItemsCount = Sales_items::where('variation_id', $id)
                ->where('POSID', $POSID)
                ->where('type', 'Product')
                ->count();

            if ($salesItemsCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete variation. This variation has ' . $salesItemsCount . ' sales item(s) associated with it.'
                ], 422);
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
                ->where('POSID', $POSID)
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
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate total sold items for this variation
            $soldItemsQty = Sales_items::where('variation_id', $id)
                ->where('POSID', $POSID)
                ->where('type', 'Product')
                ->sum('quantity');

            // Format the data for the frontend - only show sellable items
            $formattedItems = $purchaseItems->filter(function($item) {
                $status = $item->status ?? 'reserved';
                return $status === 'sellable';
            })->map(function($item) use ($variation) {
                $status = $item->status ?? 'reserved';
                $unallocatedQty = $item->unallocated_qty ?? 0;
                $purchasedQty = $item->purchased_qty ?? 0;
                $allocatedQty = $purchasedQty - $unallocatedQty;
                
                return [
                    'id' => $item->id,
                    'purchase_id' => $item->purchase_id,
                    'invoice_number' => $item->purchase->invoice_number ?? 'N/A',
                    'purchase_date' => $item->purchase->purchase_date ? formatDate($item->purchase->purchase_date) : 'N/A',
                    'available_stock' => $unallocatedQty,
                    'purchased_qty' => $purchasedQty,
                    'unallocated_qty' => $unallocatedQty,
                    'allocated_qty' => $allocatedQty,
                    'cost_price' => $item->cost_price,
                    'selling_price' => $variation->selling_price,
                    'status' => match($status) {
                        'sellable' => 'Sellable',
                        'reserved' => 'Reserved',
                        default => ucfirst($status)
                    },
                    'status_raw' => $status,
                ];
            })->values()->toArray();

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
                'current_purchase_item_id' => 'required|exists:purchase_items,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $variation = Variation::with('product')
                ->where('POSID', $POSID)
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

            $purchaseItem = PurchaseItem::findOrFail($request->current_purchase_item_id);

            // Verify purchase item belongs to this variation
            if ($purchaseItem->product_variant_id != $id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid purchase item for this variation.'
                ], 400);
            }

            // Verify purchase item is sellable
            $status = $purchaseItem->status ?? 'reserved';
            if ($status !== 'sellable') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only sellable purchase items can be added to product.'
                ], 400);
            }

            // Verify available quantity
            if ($request->quantity > $purchaseItem->unallocated_qty) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient available stock. Available: ' . $purchaseItem->unallocated_qty
                ], 400);
            }

            DB::transaction(function () use ($variation, $purchaseItem, $request) {
                // Update variation stock
                $variation->stock += $request->quantity;
                $variation->save();

                // Update purchase item unallocated quantity
                $purchaseItem->unallocated_qty -= $request->quantity;
                $purchaseItem->save();
            });

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
                ->where('POSID', $POSID)
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
                ->where('type', 'Product')
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

    public function createFreshVariant($id)
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
                ->where('type', 'Product')
                ->sum('quantity');

            if ($soldItemsQty == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot create fresh variant. This variation has no sales.'
                ], 400);
            }

            // Use the same tagline since the original will be inactive
            // Only one active variation with the same tagline is allowed
            $newTagline = $variation->tagline;

            // Create new variation with same data
            $newVariation = new Variation();
            $newVariation->POSID = $POSID;
            $newVariation->product_id = $variation->product_id;
            $newVariation->tagline = $newTagline;
            $newVariation->description = $variation->description;
            $newVariation->selling_price = $variation->selling_price;
            $newVariation->stock = $variation->stock; // Transfer all stock
            $newVariation->status = 'active';
            $newVariation->save();

            // Mark original variation as inactive
            $variation->status = 'inactive';
            $variation->stock = 0; // Clear stock from original
            $variation->save();

            // Transfer purchase items to new variation
            PurchaseItem::where('product_variant_id', $id)
                ->update(['product_variant_id' => $newVariation->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Fresh variant created successfully. Original variant marked as inactive.',
                'variation' => $newVariation
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function moveStockToPurchase(Request $request, $id)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'current_purchase_item_id' => 'required|exists:purchase_items,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $variation = Variation::with('product')
                ->where('POSID', $POSID)
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

            $purchaseItem = PurchaseItem::findOrFail($request->current_purchase_item_id);

            // Verify purchase item belongs to this variation
            if ($purchaseItem->product_variant_id != $id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid purchase item for this variation.'
                ], 400);
            }

            // Verify purchase item is sellable
            $status = $purchaseItem->status ?? 'reserved';
            if ($status !== 'sellable') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only sellable purchase items can be moved back to purchase.'
                ], 400);
            }

            // Calculate allocated quantity (items already added to product)
            $allocatedQty = $purchaseItem->purchased_qty - $purchaseItem->unallocated_qty;

            // Verify quantity doesn't exceed allocated quantity
            if ($request->quantity > $allocatedQty) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient allocated stock. Maximum available: ' . $allocatedQty
                ], 400);
            }

            // Verify variation has enough stock
            if ($request->quantity > $variation->stock) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient variation stock. Available: ' . $variation->stock
                ], 400);
            }

            DB::transaction(function () use ($variation, $purchaseItem, $request) {
                // Decrease variation stock
                $variation->stock -= $request->quantity;
                $variation->save();

                // Increase purchase item unallocated quantity (move back to purchase)
                $purchaseItem->unallocated_qty += $request->quantity;
                $purchaseItem->save();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Stock moved back to purchase successfully.',
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
}
