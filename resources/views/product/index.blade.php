@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Product List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchProduct" placeholder="Search Product">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button type="button" id="createNewProduct" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> New Product</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="productTable">
                <thead>
                    <tr>
                        <th class="text-center align-middle" style="width: 5%;" scope="col">ID</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Code</th>
                        <th class="text-center align-middle" style="width: 20%;" scope="col">Name</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Salable Stocks</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Warehouse Stocks</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Cost Price</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Selling Price</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Variations</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('product.create')

@endsection

@section('script')
@vite(['resources/js/product/product-script.js'])
<script>
let productUrls = {
    'saveProduct': "{{ route('product.store') }}",
    'showProduct': "{{ route('product.show',['product' => 'productID']) }}",
    'editProduct': "{{ route('product.edit',['product' => 'productID']) }}",
    'updateProduct': "{{ route('product.update',['product' => 'productID']) }}",
    'deleteProduct': "{{ route('product.destroy',['product' => 'productID']) }}",
    'datatable': "{{ route('product.datatable') }}"
};

let productData = {
    'brands': @json($brands),
    'categories': @json($categories),
    'units': @json($units),
    'suppliers': @json($suppliers)
};

$(document).ready(function() {
    WinPos.Datatable.initDataTable("#productTable", WinPos.Product.datatableConfiguration());

    $("#searchProduct").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })

    $("#createNewProduct").click(function() {
        $("#productCreateForm")[0].reset();
        WinPos.Product.populateCreateForm();
        WinPos.Common.showBootstrapModal("productCreateModal");
    })
});
</script>
@endsection

