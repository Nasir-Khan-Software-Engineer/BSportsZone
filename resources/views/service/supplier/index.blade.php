@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Supplier List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchSupplier" placeholder="Search Supplier">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="createSupplierBtn" data-toggle="modal"><i class="fa-solid fa-plus"></i> New Supplier</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="supplierTable">
            <thead>
                <tr>
                    <th scope="col" class="text-center" style="width: 10%;">ID</th>
                    <th scope="col" class="text-center" style="width: 25%;">SUPPLIER</th>
                    <th scope="col" class="text-center" style="width: 15%;">PHONE</th>
                    <th scope="col" class="text-center" style="width: 25%;">ADDRESS</th>
                    <th scope="col" class="text-center" style="width: 10%;">PRODUCTS</th>
                    <th scope="col" class="text-center" style="width: 15%;">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $supplier)
                <tr>
                    <td class="text-center align-middle">{{$supplier->id}}</td>
                    <td class="align-middle text-center">{{$supplier->name}}</td>
                    <td class="text-center align-middle">{{$supplier->phone_1}}</td>
                    <td class="text-center align-middle">
                        @if($supplier->address && strlen($supplier->address) > 30)
                            {{ substr($supplier->address, 0, 30) }}...
                        @else
                            {{ $supplier->address ?? '-' }}
                        @endif
                    </td>
                    <td class="text-center align-middle">{{$supplier->products_count ?? 0}}</td>
                    <td class="text-center align-middle">
                        <a href="{{ route('service.supplier.show', $supplier->id) }}" class='btn btn-sm thm-btn-bg thm-btn-text-color' data-toggle="tooltip" title="View Details"><i class='fa-solid fa-eye'></i></a>
                        <button data-id="{{$supplier->id}}" data-name="{{$supplier->name}}" class='btn btn-sm thm-btn-bg thm-btn-text-color edit-supplier'><i class='fa-solid fa-pen-to-square'></i></button>
                        <button data-id="{{$supplier->id}}" class='btn btn-sm thm-btn-bg thm-btn-text-color delete-supplier'><i class='fa-solid fa-trash'></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

<div id="createSupplierModalContainer">
    @include('service/supplier/create')
</div>

@endsection
@section('script')
@vite(['resources/js/setup/supplier-script.js'])
<script>
let SupplierUrls = {
    'getSuppliers': "{{ route('service.supplier.index') }}",
    'saveSupplier': "{{ route('service.supplier.store') }}",
    'createSupplier': "{{ route('service.supplier.create') }}",
    'showSupplier': "{{ route('service.supplier.show', ['supplier' => 'supplierid']) }}",
    'updateSupplier': "{{ route('service.supplier.update', ['supplier' => 'supplierid']) }}",
    'deleteSupplier': "{{ route('service.supplier.destroy', ['supplier' => 'supplierid']) }}",
    'editSupplier': "{{ route('service.supplier.edit', ['supplier' => 'supplierid']) }}"
}

$(document).ready(function() {
    WinPos.Datatable.initDataTable('#supplierTable', {
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
            },
        ]
    });

    $("#searchSupplier").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })

    $('#createSupplierModal').on('shown.bs.modal', function() {
        $("#supplierName").focus();
    })

    $("#createSupplierBtn").on('click', function() {
        $("#createSupplierModalLabel").html("Create New Supplier")
        $("#saveUpdateSupplier").attr('data-type', 'create').html('<i class="fa-solid fa-floppy-disk"></i> Create');
        $("#supplierName").val("");
        $("#supplierId").val("");
        $("#supplierPhone").val("");
        $("#supplierEmail").val("");
        $("#shortAddress").val("");
        $("#supplierNote").val("");
        $("[name='_method']").val("POST");

        $("#createSupplierModal").modal('toggle');
    });

    $('#supplierTable').on("click", ".edit-supplier", function() {
        WinPos.Datatable.selectRow(this);

        $("#createSupplierModalLabel").text("Update Supplier | Supplier ID: " + $(this).attr('data-id'))
        $("#supplierName").val($(this).attr('data-name'));
        $("#supplierId").val($(this).attr('data-id'));
        $("#saveUpdateSupplier").attr('data-type', 'update').html('<i class="fa-solid fa-floppy-disk"></i> Update');

        WinPos.Supplier.getUpdateSupplierForm("#createSupplierModalContainer", $(this).attr('data-id'), function(){
            $("#createSupplierModal").modal('show');
        });
        $("[name='_method']").val("PUT");
    });

    $("#saveUpdateSupplier").on('click', function(event) {
        event.preventDefault();

        WinPos.Supplier.saveSupplier(
            WinPos.Common.getFormData("#createSupplierForm"),
            $("#saveUpdateSupplier").attr('data-type'),
            function() {
                $('#createSupplierModal').modal('hide');
            });
    });

    $('#supplierTable').on("click", ".delete-supplier", function() {
        WinPos.Datatable.selectRow(this);
        if (confirm("Are you sure you want to delete this supplier?\nClick OK to continue or Cancel.")) {
            WinPos.Supplier.deleteSupplier($(this).attr('data-id'));
        }
    });
});
</script>
@endsection

