<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\Purchase\IPurchaseService;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Variation;

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
                'status' => 'nullable|in:draft,confirmed',
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
}
