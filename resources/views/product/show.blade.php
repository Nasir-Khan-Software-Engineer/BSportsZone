@extends('layouts.main-layout')
@section('style')
<style>

</style>

@vite(['resources/css/product/product-style.css'])
@endsection


@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Service Details</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ url()->previous() }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">


            <div class="card border mb-3" id="productCard">
                <div class="card-body d-flex flex-wrap justify-content-between p-1">
                    <!-- Section 1: Product Image -->
                    <div class="product-image mr-3">
                        @if($product->image)
                        <img src="{{ asset('image/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-width:120px; max-height:120px;">
                        @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width:120px; height:90px;">
                            No Image
                        </div>
                        @endif
                    </div>

                    <!-- Section 2: Basic Info -->
                    <div class="product-info mr-2 flex-grow-1">
                        <p class="mb-1"><strong>Code:</strong> {{ $product->code }}</p>
                        <p class="mb-1"><strong>Name:</strong> {{ $product->name }}</p>
                        <p class="mb-1"><strong>Price:</strong> TK {{ number_format($product->price, 2) }}</p>
                    </div>

                    <!-- Section 2: Basic Info -->
                    <div class="product-info mr-2 flex-grow-1">
                        <p class="mb-1"><strong>Brand:</strong> {{ $product->brand->name ?? '-' }}</p>
                        <p class="mb-1"><strong>Unit:</strong> {{ $product->unit->name ?? '-' }}</p>
                        <p class="mb-1"><strong>Categories:</strong> {{ $product->categories->pluck('name')->join(', ') ?: '-' }}</p>
                        <p class="mb-1"><strong>Default Beautician:</strong> {{ $product->beautician->name ?? '-' }}</p>
                    </div>

                    <!-- Section 4: Sales Info -->
                    <div class="product-sales mr-2 flex-grow-1">
                        <p class="mb-1"><strong>Last Sale At:</strong> {{ $product->lastSaleAt ? $product->lastSaleAt : '-' }}</p>
                        <p class="mb-1"><strong>Total Number of Sales:</strong> {{ $product->totalSalesCount ?? 0 }}</p>
                        <p class="mb-1"><strong>Total Amount of Sales:</strong> TK {{ number_format($product->totalSalesAmount ?? 0, 2) }}</p>
                    </div>


                    <!-- Section 3: Additional Info -->
                    <div class="product-meta">
                        <p class="mb-1"><strong>Created By:</strong> {{ $product->createdBy ?? '-' }}</p>
                        <p class="mb-1"><strong>Updated By:</strong> {{ $product->updatedBy ?? '-' }}</p>
                        <p class="mb-1"><strong>Created At:</strong> {{ $product->created_at->format('d M, Y') }}</p>
                        <p class="mb-1"><strong>Updated At:</strong> {{ $product->updated_at->format('d M, Y') }}</p>
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

            <!-- Sales Details Table -->
            <div class="card border mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sales History</h5>
                    @if($sales->count() > 0)
                    <input type="text" class="form-control data-table-search" id="searchSales" placeholder="Search Sales" style="max-width: 250px;">
                    @endif
                </div>
                <div class="card-body p-1">
                    @if($sales->count() > 0)
                    <table class="table table-bordered" id="salesTable">
                        <thead class="thead-light thm-tbl-header-bg thm-tbl-header-text-color">
                            <tr>
                                <th class="text-center align-middle" style="width: 15%;" scope="col">Date</th>
                                <th class="text-center align-middle" style="width: 20%;" scope="col">Invoice Code</th>
                                <th class="text-center align-middle" style="width: 25%;" scope="col">Customer Name</th>
                                <th class="text-center align-middle" style="width: 10%;" scope="col">QTY</th>
                                <th class="text-center align-middle" style="width: 15%;" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td class="text-center align-middle">{{ $sale->formatted_date }}</td>
                                <td class="text-center align-middle">{{ $sale->invoice_code }}</td>
                                <td class="text-center align-middle">
                                    @if($sale->customer_id)
                                    <a href="{{ route('sales.customer.details', $sale->customer_id) }}" class="text-decoration-none">
                                        {{ $sale->customer_name }}
                                    </a>
                                    @else
                                    {{ $sale->customer_name }}
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $sale->product_quantity }}</td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('sales.sale.show', $sale->id) }}" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                                     <i class="fa-solid fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fa-solid fa-info-circle"></i> No sales found for this service.
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('script')
@vite(['resources/js/product/product-script.js'])
<script>
@if($sales->count() > 0)
$(document).ready(function() {
    WinPos.Datatable.initDataTable('#salesTable', {
        order: [
            [0, 'desc']
        ],
        columns: [
            {
                type: 'date',
                orderable: true
            },
            {
                type: 'string',
                orderable: true
            },
            {
                type: 'string',
                orderable: true
            },
            {
                type: 'num',
                orderable: true
            },
            {
                type: 'string',
                orderable: false
            }
        ]
    });

    $("#searchSales").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    });
});
@endif
</script>
@endsection