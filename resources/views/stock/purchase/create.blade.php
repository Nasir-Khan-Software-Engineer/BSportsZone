@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Create New Purchase</h3>
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
                    <form id="purchaseCreateForm">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-lg-4 form-group">
                                <label for="purchaseDate">Purchase Date*</label>
                                <input required type="date" class="form-control rounded" name="purchase_date" id="purchaseDate" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="invoiceNumber">Invoice Number</label>
                                <input type="text" class="form-control rounded" name="invoice_number" id="invoiceNumber" placeholder="Invoice Number">
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="purchaseName">Purchase Name*</label>
                                <input required type="text" class="form-control rounded" name="name" id="purchaseName" placeholder="Purchase Name">
                            </div>

                            <div class="col-12 col-lg-6 form-group">
                                <label for="supplierSelect">Supplier*</label>
                                <select required class="form-control rounded" name="supplier_id" id="supplierSelect">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-lg-4 form-group">
                                <label for="productSelect">Product*</label>
                                <select required class="form-control rounded" name="product_id" id="productSelect">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-lg-2 form-group">
                                <label for="purchaseStatus">Status</label>
                                <select class="form-control rounded" name="status" id="purchaseStatus">
                                    <option value="draft">Draft</option>
                                    <option value="confirmed">Confirmed</option>
                                </select>
                            </div>

                            <div class="col-12 form-group">
                                <label for="purchaseDescription">Description</label>
                                <textarea name="description" id="purchaseDescription" class="form-control rounded" cols="30" rows="3" placeholder="Purchase Description"></textarea>
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
                        <p class="text-muted">Please select a product to load variants.</p>
                    </div>

                    <div id="purchaseItemsTableContainer" style="display: none;">
                        <table class="table table-bordered" id="purchaseItemsTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Tag Line</th>
                                    <th class="text-center">Cost Price</th>
                                    <th class="text-center">New Stock Qty</th>
                                    <th class="text-center">Sellable Stock</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseItemsTableBody">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right"><strong>Total:</strong></td>
                                    <td class="text-center"><strong id="totalCost">0.00</strong></td>
                                    <td class="text-center"><strong id="totalQty">0</strong></td>
                                    <td class="text-center"><strong id="totalSellable">0</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-right mt-3">
                        <button type="button" id="savePurchase" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Save Purchase</button>
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
    'savePurchase': "{{ route('stock.purchase.store') }}",
    'getVariations': "{{ route('stock.purchase.variations.get') }}"
};

let purchaseData = {
    'suppliers': @json($suppliers),
    'products': @json($products)
};

let addedVariants = [];
let purchaseItems = [];

$(document).ready(function() {
    // Load variants when product is selected
    $('#productSelect').on('change', function() {
        const productId = $(this).val();
        if (productId) {
            WinPos.Purchase.loadProductVariations(productId);
        } else {
            $('#variantsContainer').html('<p class="text-muted">Please select a product to load variants.</p>');
            $('#purchaseItemsTableContainer').hide();
            purchaseItems = [];
            addedVariants = [];
        }
    });

    // Save purchase
    $('#savePurchase').on('click', function() {
        WinPos.Purchase.savePurchase();
    });
});
</script>
@endsection

