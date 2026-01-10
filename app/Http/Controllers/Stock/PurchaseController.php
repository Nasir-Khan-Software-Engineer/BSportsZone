<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\Purchase\IPurchaseService;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Variation;
use App\Models\PurchaseItem;
use App\Models\Sales_items;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct(IPurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index()
    {
        $POSID = auth()->user()->POSID;
        $suppliers = Supplier::where('POSID', $POSID)->get();
        $products = Product::where('POSID', $POSID)->where('type', 'Product')->get();

        return view('stock.purchase.index', [
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function datatable(Request $request)
    {
        try {
            $POSID = auth()->user()->POSID;
            $search = $request->input('search.value', '');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orderColumn = $request->input('order.0.column', 0);
            $orderDir = $request->input('order.0.dir', 'desc');

            $purchases = $this->purchaseService->getAllPurchases($POSID, $search, $start, $length, $orderColumn, $orderDir);
            $totalRecord = $this->purchaseService->getTotalPurchases($POSID);
            $filteredRecord = $this->purchaseService->getTotalPurchases($POSID, $search);

            $purchases->transform(function($purchase) {
                $purchase->formattedDate = formatDate($purchase->purchase_date);
                $purchase->product_name = $purchase->product->name ?? '-';
                $purchase->supplier_name = $purchase->supplier->name ?? '-';
                $purchase->total_cost_formatted = number_format($purchase->total_cost_price, 2);
                
                if ($purchase->created_by == null) {
                    $purchase->createdBy = 'CustomData';
                } else {
                    $purchase->createdBy = $purchase->creator->name ?? 'N/A';
                }

                return $purchase;
            });

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecord,
                'recordsFiltered' => $filteredRecord,
                'data' => $purchases->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }

    public function create()
    {
        $POSID = auth()->user()->POSID;
        $suppliers = Supplier::where('POSID', $POSID)->get();
        $products = Product::where('POSID', $POSID)->where('type', 'Product')->get();

        return view('stock.purchase.create', [
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'purchase_date' => 'required|date',
                'invoice_number' => 'nullable|string|max:255',
                'name' => 'required|string|max:255',
                'supplier_id' => 'required|exists:suppliers,id',
                'product_id' => 'required|exists:products,id',
                'description' => 'nullable|string',
                'status' => 'nullable|in:reserved,nextplanned,inused,completed',
                'purchase_items' => 'required|array|min:1',
                'purchase_items.*.product_variant_id' => 'required|exists:variations,id',
                'purchase_items.*.cost_price' => 'required|numeric|min:0',
                'purchase_items.*.purchased_qty' => 'required|integer|min:1',
            ]);

            // Check for duplicate variants in the same purchase
            $variantIds = array_column($request->purchase_items, 'product_variant_id');
            if (count($variantIds) !== count(array_unique($variantIds))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Same variant cannot be added twice in one purchase.',
                    'errors' => ['purchase_items' => ['Same variant cannot be added twice in one purchase.']]
                ]);
            }

            $purchaseData = [
                'pos_id' => $POSID,
                'purchase_date' => $request->purchase_date,
                'invoice_number' => $request->invoice_number,
                'name' => $request->name,
                'supplier_id' => $request->supplier_id,
                'product_id' => $request->product_id,
                'description' => $request->description,
                'status' => $request->status ?? 'draft',
                'created_by' => auth()->user()->id,
            ];

            $purchase = $this->purchaseService->savePurchase($purchaseData, $request->purchase_items);

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase Created Successfully.',
                'purchase' => $purchase,
                'redirect' => route('stock.purchase.edit', $purchase->id)
            ]);

        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors()
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ]);
        }
    }

    public function edit($id)
    {
        $POSID = auth()->user()->POSID;
        $purchase = $this->purchaseService->getPurchaseWithItems($POSID, $id);

        if (!$purchase) {
            abort(404, 'Purchase not found.');
        }

        $suppliers = Supplier::where('POSID', $POSID)->get();
        $products = Product::where('POSID', $POSID)->where('type', 'Product')->get();

        // Mark items as editable or not
        foreach ($purchase->purchaseItems as $item) {
            $item->is_editable = $item->isEditable();
        }

        return view('stock.purchase.edit', [
            'purchase' => $purchase,
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'purchase_date' => 'required|date',
                'invoice_number' => 'nullable|string|max:255',
                'name' => 'required|string|max:255',
                'supplier_id' => 'required|exists:suppliers,id',
                'product_id' => 'required|exists:products,id',
                'description' => 'nullable|string',
                'status' => 'nullable|in:draft,confirmed',
                'purchase_items' => 'required|array|min:1',
                'purchase_items.*.product_variant_id' => 'required|exists:variations,id',
                'purchase_items.*.cost_price' => 'required|numeric|min:0',
                'purchase_items.*.purchased_qty' => 'required|integer|min:1',
                'purchase_items.*.status' => 'nullable|in:reserved,nextplanned,inused',
            ]);

            // Check for duplicate variants in the same purchase
            $variantIds = array_column($request->purchase_items, 'product_variant_id');
            if (count($variantIds) !== count(array_unique($variantIds))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Same variant cannot be added twice in one purchase.',
                    'errors' => ['purchase_items' => ['Same variant cannot be added twice in one purchase.']]
                ]);
            }

            $purchase = $this->purchaseService->getPurchaseById($POSID, $id);
            if (!$purchase) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Purchase not found.'
                ], 404);
            }

            $purchaseData = [
                'id' => $id,
                'pos_id' => $POSID,
                'purchase_date' => $request->purchase_date,
                'invoice_number' => $request->invoice_number,
                'name' => $request->name,
                'supplier_id' => $request->supplier_id,
                'product_id' => $request->product_id,
                'description' => $request->description,
                'status' => $request->status ?? 'draft',
            ];

            $purchase = $this->purchaseService->updatePurchase($purchaseData, $request->purchase_items);

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase Updated Successfully.',
                'purchase' => $purchase
            ]);

        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors()
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ]);
        }
    }

    public function show($id)
    {
        $POSID = auth()->user()->POSID;
        $purchase = $this->purchaseService->getPurchaseWithItems($POSID, $id);

        if (!$purchase) {
            abort(404, 'Purchase not found.');
        }

        $purchase->formattedDate = formatDate($purchase->purchase_date);
        $purchase->formattedTime = formatTime($purchase->created_at);
        $purchase->createdBy = $purchase->creator->name ?? 'N/A';

        // Calculate sold quantities for each purchase item
        $variationIds = $purchase->purchaseItems->pluck('product_variant_id')->toArray();
        $soldQuantities = [];
        if (!empty($variationIds)) {
            $soldQuantities = Sales_items::whereIn('variation_id', $variationIds)
                ->where('POSID', $POSID)
                ->where('type', 'Product')
                ->selectRaw('variation_id, SUM(quantity) as total_sold_qty')
                ->groupBy('variation_id')
                ->pluck('total_sold_qty', 'variation_id')
                ->toArray();
        }

        // Attach sold quantity to each purchase item
        foreach ($purchase->purchaseItems as $item) {
            $item->sold_qty = $soldQuantities[$item->product_variant_id] ?? 0;
        }

        return view('stock.purchase.show', [
            'purchase' => $purchase
        ]);
    }

    public function getProductVariations(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $variations = Variation::where('product_id', $productId)
                ->where('status', 'active')
                ->get();

            return response()->json([
                'status' => 'success',
                'variations' => $variations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load variations.'
            ]);
        }
    }

    public function updatePurchaseItem(Request $request, $id)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $request->validate([
                'cost_price' => 'required|numeric|min:0',
                'purchased_qty' => 'required|integer|min:1',
                'status' => 'nullable|in:reserved,nextplanned,inused',
            ]);

            $purchaseItem = PurchaseItem::with('purchase')->findOrFail($id);

            // Verify purchase belongs to the user's POSID
            if ($purchaseItem->purchase->pos_id != $POSID) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.'
                ], 403);
            }

            // Check if item has allocated qty (cannot update if allocated)
            if (!$purchaseItem->isEditable()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot update purchase item. This item has allocated quantity.'
                ], 422);
            }

            DB::transaction(function () use ($purchaseItem, $request) {
                $variationId = $purchaseItem->product_variant_id;
                $newStatus = $request->has('status') ? $request->status : $purchaseItem->status;
                
                // Enforce only one "inused" and one "nextplanned" per variation
                if ($newStatus === 'inused') {
                    // Set all other "inused" items for this variation to "reserved"
                    PurchaseItem::where('product_variant_id', $variationId)
                        ->where('id', '!=', $purchaseItem->id)
                        ->where('status', 'inused')
                        ->update(['status' => 'reserved']);
                } elseif ($newStatus === 'nextplanned') {
                    // Set all other "nextplanned" items for this variation to "reserved"
                    PurchaseItem::where('product_variant_id', $variationId)
                        ->where('id', '!=', $purchaseItem->id)
                        ->where('status', 'nextplanned')
                        ->update(['status' => 'reserved']);
                }
                
                // Update purchase item
                $purchaseItem->cost_price = (float)$request->cost_price;
                $purchaseItem->purchased_qty = (int)$request->purchased_qty;
                $purchaseItem->unallocated_qty = (int)$request->purchased_qty; // Reset unallocated to match purchased
                if ($request->has('status')) {
                    $purchaseItem->status = $request->status;
                }
                $purchaseItem->save();

                // Recalculate purchase totals
                $purchase = $purchaseItem->purchase;
                $totalQty = $purchase->purchaseItems->sum('purchased_qty');
                $totalCostPrice = $purchase->purchaseItems->sum(function($item) {
                    return $item->cost_price * $item->purchased_qty;
                });

                $purchase->total_qty = $totalQty;
                $purchase->total_cost_price = $totalCostPrice;
                $purchase->save();
            });

            $purchaseItem->load('variation');

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase item updated successfully.',
                'item' => $purchaseItem
            ]);

        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors()
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function removePurchaseItem($id)
    {
        try {
            $POSID = auth()->user()->POSID;
            
            $purchaseItem = PurchaseItem::with('purchase')->findOrFail($id);

            // Verify purchase belongs to the user's POSID
            if ($purchaseItem->purchase->pos_id != $POSID) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.'
                ], 403);
            }

            // Check if item has allocated qty (cannot remove if allocated)
            if (!$purchaseItem->isEditable()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot remove purchase item. This item has allocated quantity.'
                ], 422);
            }

            DB::transaction(function () use ($purchaseItem) {
                $purchase = $purchaseItem->purchase;
                
                // Delete the purchase item
                $purchaseItem->delete();

                // Refresh purchase to get updated items
                $purchase->refresh();
                
                // Recalculate purchase totals
                $totalQty = $purchase->purchaseItems->sum('purchased_qty');
                $totalCostPrice = $purchase->purchaseItems->sum(function($item) {
                    return $item->cost_price * $item->purchased_qty;
                });

                $purchase->total_qty = $totalQty;
                $purchase->total_cost_price = $totalCostPrice;
                $purchase->save();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase item removed successfully.'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
