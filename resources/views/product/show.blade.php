@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Product Details</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('product.index') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
            <div class="card border mb-3" id="productCard">
                <div class="card-body d-flex flex-wrap justify-content-between p-1">
                    <!-- Section 1: Basic Info -->
                    <div class="product-info mr-2 flex-grow-1">
                        <p class="mb-1"><strong>Code:</strong> {{ $product->code }}</p>
                        <p class="mb-1"><strong>Name:</strong> {{ $product->name }}</p>
                        <p class="mb-1"><strong>Unit:</strong> {{ $product->unit->name ?? '-' }}</p>
                    </div>

                    <!-- Section 2: Brand & Supplier -->
                    <div class="product-info mr-2 flex-grow-1">
                        <p class="mb-1"><strong>Brand:</strong> {{ $product->brand->name ?? '-' }}</p>
                        <p class="mb-1"><strong>Supplier:</strong> {{ $product->supplier->name ?? '-' }}</p>
                        <p class="mb-1"><strong>Categories:</strong> {{ $product->categories->pluck('name')->join(', ') ?: '-' }}</p>
                    </div>

                    <!-- Section 3: Sales Info -->
                    <div class="product-sales mr-2 flex-grow-1">
                        <p class="mb-1"><strong>Total Variations:</strong> {{ $product->variations->count() ?? 0 }}</p>
                        <p class="mb-1"><strong>Total Sales:</strong> {{ $product->totalSalesCount ?? 0 }}</p>
                        <p class="mb-1"><strong>Total Sales Amount:</strong> TK {{ number_format($product->totalSalesAmount ?? 0, 2) }}</p>
                    </div>

                    <!-- Section 4: Meta Info -->
                    <div class="product-meta">
                        <p class="mb-1"><strong>Created By:</strong> {{ $product->createdBy ?? '-' }}</p>
                        <p class="mb-1"><strong>Updated By:</strong> {{ $product->updatedBy ?? '-' }}</p>
                        <p class="mb-1"><strong>Created At:</strong> {{ $product->created_at->format('d M, Y') }}</p>
                    </div>
                </div>
            </div>

            @if(!empty($product->description))
            <div class="accordion mb-3" id="productDescriptionAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingDescription">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescription" aria-expanded="false" aria-controls="collapseDescription">
                            View Description
                        </button>
                    </h2>
                    <div id="collapseDescription" class="accordion-collapse collapse" aria-labelledby="headingDescription" data-bs-parent="#productDescriptionAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Variations Table -->
            @if($product->variations->count() > 0)
            <div class="card border mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Product Variations</h5>
                </div>
                <div class="card-body p-1">
                    <table class="table table-bordered">
                        <thead class="thead-light thm-tbl-header-bg thm-tbl-header-text-color">
                            <tr>
                                <th class="text-center">Tagline</th>
                                <th class="text-center">Description</th>
                                <th class="text-center">Selling Price</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Available Stock in Warehouse</th>
                                <th class="text-center">Sales</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variations as $variation)
                            <tr>
                                <td class="text-center">{{ $variation->tagline }}</td>
                                <td class="text-center">{{ $variation->description ?? '-' }}</td>
                                <td class="text-center">TK {{ number_format($variation->selling_price, 2) }}</td>
                                <td class="text-center">{{ $variation->stock }}</td>
                                <td class="text-center">
                                    {{ $variation->available_stock_in_warehouse ?? 0 }}
                                </td>
                                <td class="text-center">{{ $variation->total_sales_qty ?? 0 }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $variation->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($variation->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Purchases Table -->
            <div class="card border mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Product Purchases</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="text" class="form-control form-control-sm" id="searchProductPurchases" placeholder="Search Purchases" style="width: 250px;">
                    </div>
                </div>
                <div class="card-body p-1">
                    <table class="table table-bordered" id="productPurchasesTable">
                        <thead class="thead-light thm-tbl-header-bg thm-tbl-header-text-color">
                            <tr>
                                <th class="text-center align-middle" style="width: 5%;">ID</th>
                                <th class="text-center align-middle" style="width: 12%;">Purchase Date</th>
                                <th class="text-center align-middle" style="width: 12%;">Invoice Number</th>
                                <th class="text-center align-middle" style="width: 15%;">Purchase Name</th>
                                <th class="text-center align-middle" style="width: 12%;">Total Cost</th>
                                <th class="text-center align-middle" style="width: 10%;">Total Quantity</th>
                                <th class="text-center align-middle" style="width: 10%;">Total Variations</th>
                                <th class="text-center align-middle" style="width: 12%;">Supplier</th>
                                <th class="text-center align-middle" style="width: 10%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@vite(['resources/js/product/product-script.js'])
<script>
let productUrls = {
    'getProductPurchases': "{{ route('product.get-purchases', ['product' => $product->id]) }}",
    'showPurchase': "{{ route('stock.purchase.show', ['purchase' => 'purchaseID']) }}"
};

$(document).ready(function() {
    // Initialize product purchases table with client-side pagination
    WinPos.Product.initProductPurchasesTable();
});
</script>
@endsection

