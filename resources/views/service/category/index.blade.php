@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Category List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchCategory" placeholder="Search Category">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="createCategoryBtn" data-toggle="modal"><i class="fa-solid fa-plus"></i> New Category</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="categoryTable">
            <thead>
                <tr>
                    <th scope="col" class="text-center" style="width: 10%;">ID</th>
                    <th scope="col" class="text-center" style="width: 45%;">CATEGORY</th>
                    <th scope="col" class="text-center" style="width: 15%;">CREATED ON</th>
                    <th scope="col" class="text-center" style="width: 15%;">CREATED BY</th>
                    <th scope="col" class="text-center" style="width: 15%;">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td class="text-center align-middle">{{$category->id}}</td>
                    <td class="align-middle text-center">{{$category->name}}</td>
                    <td class="text-center align-middle">
                        <div class="text-center align-middle d-inline-block px-2" style="line-height: normal;">
                            {{ $category->formattedTime }}
                            <br>
                            {{ $category->formattedDate }}
                        </div>
                    </td>
                    <td class="text-center align-middle">{{$category->userName}}</td>
                    <td class="text-center align-middle">
                        <button data-id="{{$category->id}}" data-name="{{$category->name}}" class='btn btn-sm thm-btn-bg thm-btn-text-color edit-category'><i class='fa-solid fa-pen-to-square'></i></button>
                        <button data-id="{{$category->id}}" class='btn btn-sm thm-btn-bg thm-btn-text-color delete-category'><i class='fa-solid fa-trash'></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

<div id="createCategoryModalContainer">
    @include('service/category/create')
</div>

@endsection
@section('script')
@vite(['resources/js/service/category-script.js'])
<script>
let CategoryUrls = {
    'getCategories': "{{ route('service.category.index') }}",
    'saveCategory': "{{ route('service.category.store') }}",
    'createCategory': "{{ route('service.category.create') }}",
    'updateCategory': "{{ route('service.category.update', ['category' => 'categoryid']) }}",
    'deleteCategory': "{{ route('service.category.destroy', ['category' => 'categoryid']) }}",
    'editCategory': "{{ route('service.category.edit', ['category' => 'categoryid']) }}"
}

$(document).ready(function() {
    WinPos.Datatable.initDataTable('#categoryTable', {
        order: [
            [0, 'desc']
        ],
        columns: [{
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
                type: 'date',
                orderable: true
            },
            {
                type: 'string',
                orderable: false
            },
        ]
    });

    $("#searchCategory").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })

    $('#createCategoryModal').on('shown.bs.modal', function() {
        $("#categoryName").focus();
    })

    $("#createCategoryBtn").on('click', function() {
        $("#createCategoryModalLabel").html("Create New Category")
        $("#saveUpdateCategory").attr('data-type', 'create').html('<i class="fa-solid fa-floppy-disk"></i> Create');
        $("#categoryName").val("");
        $("#categoryID").val("");
        $("[name='_method']").val("POST");

        $("#createCategoryModal").modal('toggle');
    });

    $('#categoryTable').on("click", ".edit-category", function() {
        WinPos.Datatable.selectRow(this);

        $("#createCategoryModalLabel").text("Update Category | Category ID: " + $(this).attr('data-id'))
        $("#categoryName").val($(this).attr('data-name'));
        $("#categoryID").val($(this).attr('data-id'));
        $("#saveUpdateCategory").attr('data-type', 'update').html('<i class="fa-solid fa-floppy-disk"></i> Update');

        $("#createCategoryModal").modal('show');
        $("[name='_method']").val("PUT");
    });

    $("#saveUpdateCategory").on('click', function(event) {
        event.preventDefault();

        WinPos.Category.saveCategory(
            WinPos.Common.getFormData("#createCategoryForm"),

            $("#saveUpdateCategory").attr('data-type'),

            function() {
                $('#createCategoryModal').modal('hide');
            });
    });

    $('#categoryTable').on("click", ".delete-category", function() {
        WinPos.Datatable.selectRow(this);
        if (confirm("Are you sure you want to delete this category?\nClick OK to continue or Cancel.")) {
            WinPos.Category.deleteCategory($(this).attr('data-id'));
        }
    });
});
</script>
@endsection