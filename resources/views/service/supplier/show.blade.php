@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Supplier Details</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('service.supplier.index') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
            <x-supplier-ribbon :supplier="$supplierRibbonData" />

            <!-- Products Details Table -->
            <div class="card border mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Products from this Supplier</h5>
                    @if($products->count() > 0)
                    <input type="text" class="form-control data-table-search" id="searchProducts" placeholder="Search Products" style="max-width: 250px;">
                    @endif
                </div>
                <div class="card-body p-1">
                    @if($products->count() > 0)
                    <table class="table table-bordered" id="productsTable">
                        <thead class="thead-light thm-tbl-header-bg thm-tbl-header-text-color">
                            <tr>
                                <th class="text-center align-middle" style="width: 10%;" scope="col">ID</th>
                                <th class="text-center align-middle" style="width: 20%;" scope="col">Code</th>
                                <th class="text-center align-middle" style="width: 25%;" scope="col">Name</th>
                                <th class="text-center align-middle" style="width: 15%;" scope="col">Type</th>
                                <th class="text-center align-middle" style="width: 15%;" scope="col">Price</th>
                                <th class="text-center align-middle" style="width: 15%;" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td class="text-center align-middle">{{ $product->id }}</td>
                                <td class="text-center align-middle">{{ $product->code }}</td>
                                <td class="text-center align-middle">{{ $product->name }}</td>
                                <td class="text-center align-middle">{{ $product->type }}</td>
                                <td class="text-center align-middle">TK {{ number_format($product->price, 2) }}</td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fa-solid fa-info-circle"></i> No products found for this supplier.
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('script')
<script>
@if($products->count() > 0)
$(document).ready(function() {
    WinPos.Datatable.initDataTable('#productsTable', {
        order: [
            [0, 'desc']
        ],
        columns: [
            {
                type: 'num',
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

    $("#searchProducts").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    });
});
@endif
</script>
@endsection

