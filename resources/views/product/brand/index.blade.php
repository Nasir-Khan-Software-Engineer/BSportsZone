@extends('layouts.main-layout')

@section('content')
<div id="createBrandModalContainer"></div>

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Brand List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchBrand" placeholder="Search Brand">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="createBrandBtn"><i class="fa-solid fa-plus"></i> New Brand</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered datatable" id="brandTable">
                <thead>
                    <tr>
                        <th scope="col" class="text-center" style="width: 15%;">BRAND</th>
                        <th scope="col" class="text-center" style="width: 40%;">BRAND</th>
                        <th scope="col" class="text-center" style="width: 15%;">CREATED ON</th>
                        <th scope="col" class="text-center" style="width: 15%;">CREATED BY</th>
                        <th scope="col" class="text-center" style="width: 15%;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $brand)
                    <tr>
                        <td class="align-middle text-center">{{$brand->id}}</td>
                        <td class="align-middle text-center">{{$brand->name}}</td>
                        <td class="text-center align-middle">
                            <div class="text-center d-inline-block px-2" style="line-height: normal;">
                                {{ $brand->formattedTime }}
                                <br>
                                {{ $brand->formattedDate }}
                            </div>
                        </td>
                        <td class="text-center align-middle">{{$brand->createdBy}}</td>
                        <td class="text-center align-middle">
                            <button data-id="{{$brand->id}}" data-name="{{$brand->name}}" class='btn btn-sm thm-btn-bg thm-btn-text-color edit-brand'><i class='fa-solid fa-pen-to-square'></i></button>
                            <button data-id="{{$brand->id}}" class='btn btn-sm thm-btn-bg thm-btn-text-color delete-brand'><i class='fa-solid fa-trash'></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@section('script')
@vite(['resources/js/product/brand-script.js'])
<script>
let BrandUrls = {
    'getBrands': "{{ route('product.brand.index') }}",
    'saveBrand': "{{ route('product.brand.store') }}",
    'createBrand': "{{ route('product.brand.create') }}",
    'updateBrand': "{{ route('product.brand.update', ['brand' => 'brandid']) }}",
    'deleteBrand': "{{ route('product.brand.destroy', ['brand' => 'brandid']) }}",
    'editBrand': "{{ route('product.brand.edit', ['brand' => 'brandid']) }}"
}

$(document).ready(function() {

    WinPos.Datatable.initDataTable("#brandTable", {
        order: [
            [0, 'desc']
        ]
    });

    $("#searchBrand").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })

    $("#createBrandBtn").on('click', function() {
        WinPos.Brand.getCreateBrandForm("#createBrandModalContainer", function() {
            $("#createBrandModal").modal('show');
        });
    });

    $(document).on('click', '#saveUpdateBrand', function(event) {
        event.preventDefault();
        WinPos.Brand.saveBrand(WinPos.Common.getFormData("#createBrandForm"), $("#saveUpdateBrand").attr('data-type'));
    })

    $(document).on("click", ".edit-brand", function() {
        WinPos.Datatable.selectRow(this);
        WinPos.Brand.getUpdateBrandForm("#createBrandModalContainer", $(this).attr('data-id'), function() {
            $("#createBrandModal").modal('show');
        });
    });

    $(document).on("click", ".delete-brand", function() {
        if (confirm("Are you sure you want to delete this brand?\nClick OK to continue or Cancel.")) {
            WinPos.Datatable.selectRow(this);
            WinPos.Brand.deleteBrand($(this).attr('data-id'));
        }
    });
});
</script>
@endsection