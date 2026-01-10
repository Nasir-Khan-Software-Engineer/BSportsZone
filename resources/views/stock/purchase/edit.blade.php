@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Edit Purchase</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('stock.purchase.index') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
            <!-- Purchase Info Form -->
            <div class="card border mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Information</h5>
                </div>
                <div class="card-body">
                    <form id="purchaseEditForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="purchaseId" name="purchase_id" value="{{ $purchase->id ?? '' }}">
                        <div class="row">
                            <div class="col-12 col-lg-4 form-group">
                                <label for="purchaseDate">Purchase Date*</label>
                                <input required type="date" class="form-control rounded" name="purchase_date" id="purchaseDate" value="{{ $purchase->purchase_date ? date('Y-m-d', strtotime($purchase->purchase_date)) : date('Y-m-d') }}">
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="invoiceNumber">Invoice Number</label>
                                <input type="text" class="form-control rounded" name="invoice_number" id="invoiceNumber" value="{{ $purchase->invoice_number ?? '' }}" placeholder="Invoice Number">
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="purchaseName">Purchase Name*</label>
                                <input required type="text" class="form-control rounded" name="name" id="purchaseName" value="{{ $purchase->name ?? '' }}" placeholder="Purchase Name">
                            </div>

                            <div class="col-12 col-lg-6 form-group">
                                <label for="supplierSelect">Supplier*</label>
                                <select required class="form-control rounded" name="supplier_id" id="supplierSelect">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ ($purchase->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="productSelect">Product*</label>
                                <select required class="form-control rounded" name="product_id" id="productSelect">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ ($purchase->product_id ?? '') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-lg-2 form-group">
                                <label for="purchaseStatus">Status</label>
                                <select class="form-control rounded" name="status" id="purchaseStatus">
                                    <option value="draft" {{ ($purchase->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="confirmed" {{ ($purchase->status ?? 'draft') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                </select>
                            </div>

                            <div class="col-12 form-group">
                                <label for="purchaseDescription">Description</label>
                                <textarea name="description" id="purchaseDescription" class="form-control rounded" cols="30" rows="3" placeholder="Purchase Description">{{ $purchase->description ?? '' }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Variant Stock Entry Table -->
            <div class="card border">
                <div class="card-header">
                    <h5 class="mb-0">Variant Stock Entry</h5>
                </div>
                <div class="card-body">
                    <div id="variantsContainer" class="mb-3">
                        <p class="text-muted">Loading variants...</p>
                    </div>

                    <div id="purchaseItemsTableContainer">
                        <table class="table table-bordered" id="purchaseItemsTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Tag Line</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Cost Price</th>
                                    <th class="text-center">Purchased Qty</th>
                                    <th class="text-center">Unallocated Qty</th>
                                    <th class="text-center">Allocated Qty</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseItemsTableBody">
                                @foreach($purchase->purchaseItems as $item)
                                <tr data-item-id="{{ $item->id }}" data-variant-id="{{ $item->product_variant_id }}" data-editable="{{ $item->is_editable ? 'true' : 'false' }}">
                                    <td>{{ $item->variation->tagline ?? '-' }}</td>
                                    <td class="text-center">
                                        <select class="form-control form-control-sm rounded purchase-item-status" data-item-id="{{ $item->id }}" data-original-status="{{ $item->status ?? 'reserved' }}" {{ $item->is_editable ? '' : 'disabled' }}>
                                            <option value="reserved" {{ ($item->status ?? 'reserved') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                            <option value="nextplanned" {{ ($item->status ?? 'reserved') == 'nextplanned' ? 'selected' : '' }}>Next Planned</option>
                                            <option value="inused" {{ ($item->status ?? 'reserved') == 'inused' ? 'selected' : '' }}>In Used</option>
                                        </select>
                                    </td>
                                    <td>
                                        @if($item->is_editable)
                                            <input type="number" step="0.01" class="form-control form-control-sm rounded cost-price-input" value="{{ $item->cost_price }}" data-item-id="{{ $item->id }}">
                                        @else
                                            {{ number_format($item->cost_price, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->is_editable)
                                            <input type="number" class="form-control form-control-sm rounded purchased-qty-input" value="{{ $item->purchased_qty }}" data-item-id="{{ $item->id }}">
                                        @else
                                            {{ $item->purchased_qty }}
                                        @endif
                                    </td>
                                    <td>{{ $item->unallocated_qty }}</td>
                                    <td class="text-center">{{ $item->purchased_qty - $item->unallocated_qty }}</td>
                                    <td class="text-center">
                                        @if($item->is_editable)
                                            <button type="button" class="btn btn-sm btn-success save-purchase-item" data-item-id="{{ $item->id }}" title="Save Item">
                                                <i class="fa-solid fa-save"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger remove-purchase-item" data-item-id="{{ $item->id }}" title="Remove Item">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">Allocated</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right"><strong>Total:</strong></td>
                                    <td class="text-center"><strong id="totalCost">{{ number_format($purchase->total_cost_price ?? 0, 2) }}</strong></td>
                                    <td class="text-center"><strong id="totalQty">{{ $purchase->total_qty ?? 0 }}</strong></td>
                                    <td class="text-center"><strong id="totalUnallocated">{{ $purchase->purchaseItems->sum('unallocated_qty') ?? 0 }}</strong></td>
                                    <td class="text-center"><strong id="totalAllocated">{{ $purchase->purchaseItems->sum(function($item) { return $item->purchased_qty - $item->unallocated_qty; }) ?? 0 }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-right mt-3">
                        <button type="button" id="updatePurchase" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update Purchase</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@vite(['resources/js/stock/purchase-script.js'])
<script>
let purchaseUrls = {
    'updatePurchase': "{{ route('stock.purchase.update', ['purchase' => $purchase->id]) }}",
    'getVariations': "{{ route('stock.purchase.variations.get') }}",
    'updatePurchaseItem': "{{ route('stock.purchase.item.update', ['purchaseItem' => 'ITEM_ID']) }}",
    'removePurchaseItem': "{{ route('stock.purchase.item.remove', ['purchaseItem' => 'ITEM_ID']) }}"
};

let purchaseData = {
    'suppliers': @json($suppliers),
    'products': @json($products),
    'purchase': @json($purchase),
    'purchaseItems': @json($purchase->purchaseItems)
};

let addedVariants = [];
let purchaseItems = [];

$(document).ready(function() {
    // Initialize existing items
    purchaseItems = purchaseData.purchaseItems.map(item => ({
        product_variant_id: item.product_variant_id,
        cost_price: parseFloat(item.cost_price),
        purchased_qty: parseInt(item.purchased_qty),
        is_editable: item.is_editable
    }));

    addedVariants = purchaseData.purchaseItems.map(item => item.product_variant_id);

    // Load variants when product is selected
    $('#productSelect').on('change', function() {
        const productId = $(this).val();
        if (productId) {
            WinPos.Purchase.loadProductVariations(productId);
        }
    });

    // Load variants on page load
    const productId = $('#productSelect').val();
    if (productId) {
        WinPos.Purchase.loadProductVariations(productId);
    }

    // Update purchase
    $('#updatePurchase').on('click', function() {
        WinPos.Purchase.updatePurchase();
    });

    // Save purchase item
    $(document).on('click', '.save-purchase-item', function() {
        let itemId = $(this).data('item-id');
        if (itemId) {
            WinPos.Purchase.updatePurchaseItem(itemId);
        }
    });

    // Remove purchase item (for existing items with item-id)
    $(document).on('click', '.remove-purchase-item[data-item-id]', function() {
        let itemId = $(this).data('item-id');
        if (itemId) {
            WinPos.Purchase.removePurchaseItem(itemId);
        }
    });

    // Remove purchase item (for new items added via variant selection)
    $(document).on('click', '.remove-purchase-item[data-variant-id]', function() {
        let variantId = $(this).data('variant-id');
        if (variantId) {
            WinPos.Purchase.removeItemFromPurchase(variantId);
        }
    });

    // Update item values
    $(document).on('change', '.cost-price-input, .purchased-qty-input', function() {
        WinPos.Purchase.calculateTotals();
    });
});
</script>
@endsection

