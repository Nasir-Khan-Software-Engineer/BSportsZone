@extends('layouts.main-layout')

@section('content')
    <div id="createSupplierModalContainer"></div>
    <div id="SupplierListContainer" class="view-container mb-2">
        <div class="row mb-4">
            <div class="col">
                <input type="text" class="form-control search-mid w-50" id="searchSupplier" placeholder="Search">
            </div>
            <div class="col text-right">
                <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="createSupplierBtn">Create Supplier</button>
            </div>
        </div>
        <div class="bg-white rounded">
            <table class="table table-bordered" id="supplierTable">
                <thead>
                <tr>
                    <th scope="col" class="text-left" style="width: 25%;">SUPPLIER</th>
                    <th scope="col" class="text-center" style="width: 15%;">PHONE</th>
                    <th scope="col" class="text-center" style="width: 30%;">SHORT ADDRESS</th>
                    <th scope="col" class="text-center" style="width: 15%;">ADDED ON</th>
                    <th scope="col" class="text-right" style="width: 15%;">ACTION</th>
                </tr>
                </thead>
                <tbody>
                @foreach($suppliers as $Supplier)
                    <tr>
                        <td>{{$Supplier->name}}</td>
                        <td class="text-center align-middle">{{$Supplier->phone}}</td>
                        <td class="text-center align-middle">
                            @if(strlen($Supplier->address)<=30)
                                {{$Supplier->address}}
                            @else
                                {{substr($Supplier->address, 30)."..."}}
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <div class="text-center align-middle d-inline-block px-2" style="line-height: normal;">
                                {{ Carbon\Carbon::parse($Supplier->created_at)->format('g:i A') }}
                                <br>
                                {{ Carbon\Carbon::parse($Supplier->created_at)->format('d M Y') }}
                            </div>
                        </td>
                        <td class="text-right align-middle">
                            <button data-id="{{$Supplier->id}}" class='btn btn-sm thm-btn-bg thm-btn-text-color edit-supplier'><i class='fa-solid fa-pen-to-square'></i></button>
                            <button data-id="{{$Supplier->id}}" class='btn btn-sm thm-btn-bg thm-btn-text-color delete-supplier'><i class='fa-solid fa-trash'></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
@section('script')
    @vite(['resources/js/setup/supplier-script.js'])
    <script>
        let SupplierUrls = {
            'getSuppliers': "{{ route('setup.supplier.index') }}",
            'saveSupplier': "{{ route('setup.supplier.store') }}",
            'createSupplier': "{{ route('setup.supplier.create') }}",
            'getSupplier': "{{ route('setup.supplier.show', ['supplier' => 'supplierid']) }}",
            'updateSupplier': "{{ route('setup.supplier.update', ['supplier' => 'supplierid']) }}",
            'deleteSupplier': "{{ route('setup.supplier.destroy', ['supplier' => 'supplierid']) }}",
            'editSupplier': "{{ route('setup.supplier.edit', ['supplier' => 'supplierid']) }}"
        }

        $(document).ready(function(){

            WinPos.Datatable.initDataTable('#supplierTable');

            $("#createSupplierBtn").on('click', function(){
                WinPos.Supplier.getCreateSupplierForm("#createSupplierModalContainer", function(){
                    $("#createSupplierModal").modal('show');
                });
            });

            $(document).on('click', '#saveUpdateSupplier', function (event){
                event.preventDefault();
                WinPos.Supplier.saveSupplier(
                    WinPos.Common.getFormData("#createSupplierForm"), 
                    $("#saveUpdateSupplier").attr('data-type'), 
                    function(){
                        $("#createSupplierModal").modal('hide');
                    });
            })

            $(document).on('click', '.edit-supplier', function (){
                WinPos.Datatable.selectRow(this); 
                WinPos.Supplier.getUpdateSupplierForm("#createSupplierModalContainer", $(this).attr('data-id'), function(){
                    $("#createSupplierModal").modal('show');
                });
            });

            $(document).on('click', '.delete-supplier', function (){
                WinPos.Datatable.selectRow(this); 
                if(confirm("Are you sure you want to delete this supplier?\nClick OK to continue or Cancel.")){
                    WinPos.Supplier.deleteSupplier($(this).attr('data-id'));
                }
            });
        });
    </script>
@endsection
