@extends('layouts.main-layout')
@section('style')
<style>
input,
select,
textarea {
    border: 1px solid #333 !important;
}
</style>

@vite(['resources/css/product/product-style.css'])

@endsection

@php
$posid = auth()->user()->posid;
$imagePath = "/images/{$posid}/products/";
@endphp

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Service List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchService" placeholder="Search Service">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button type="button" id="createNewProduct" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> New Service</button>

                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="productTable">
                <thead>
                    <tr>
                        <th class="text-center align-middle" style="width: 5%;" scope="col">ID</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Code</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Image</th>
                        <th class="text-center align-middle" style="width: 25%;" scope="col">Name</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Price</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Beautician</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Created At</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Created By</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('product.add')
@include('product.edit')

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
    'copyProduct': "{{ route('product.copy',['product' => 'productID']) }}",
    'productImagePath': "{{ asset($imagePath) }}",
    'defaultProductImagePath': "{{ asset('images/default_product_img.png') }}",
    'datatable': "{{ route('product.datatable') }}"
};

$(document).ready(function() {
    WinPos.Datatable.initDataTable("#productTable", WinPos.Product.datatableConfiguration());


    $("#searchService").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })

    $("#saveProduct").click(async function(event) {
        event.preventDefault();

        let productInfo = WinPos.Common.getFormData('#productAddForm');

        if (productInfo.image instanceof File) {
            productInfo.image = await fileToBase64(productInfo.image);
        }

        WinPos.Product.saveProduct(productInfo);
    });

    $("#createNewProduct").click(function() {
        $("#productAddForm")[0].reset();
        $('#imagePreview').css('background-image', '');
        $("#productBasicInfoTab").click();
        WinPos.Common.showBootstrapModal("productAddModal");
    })

    $("#updateProduct").click(async function(event) {
        event.preventDefault();
        let productInfo = WinPos.Common.getFormData('#productEditForm');
        let productID = $("#hiddenProductID").val();

        if (productInfo.image instanceof File) {
            productInfo.image = await fileToBase64(productInfo.image);
        }

        WinPos.Product.updateProduct(productInfo, productID);
    });

    $("#image").change(function() {
        WinPos.Common.previewImage('#imagePreview', this);
    });

}); // end jquery

$(document).on('change', '#editImage', function() {
    console.log(this);
    WinPos.Common.previewImage('#imagePreviewEdit', this);
})

$(document).on('click', '.edit-product', function() {
    WinPos.Datatable.selectRow(this);
    let productID = $(this).data('productid');
    WinPos.Product.editProduct(productID);
})

$(document).on('click', '.copy-product', function() {
    WinPos.Datatable.selectRow(this);
    let productID = $(this).data('productid');
    WinPos.Product.copyProduct(productID);
})

$(document).on('click', '.delete-product', function(event) {
    WinPos.Datatable.selectRow(this);
    if (confirm("Are you sure you want to delete this user?\nClick OK to continue or Cancel.")) {
        let productID = $(this).data('productid');
        WinPos.Product.deleteProduct(productID);
    }
});

function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}
</script>
@endsection