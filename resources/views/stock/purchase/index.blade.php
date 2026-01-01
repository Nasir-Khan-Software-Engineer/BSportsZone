@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Purchase List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchPurchase" placeholder="Search Purchase">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <a href="{{ route('stock.purchase.create') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> New Purchase</a>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="purchaseTable">
                <thead>
                    <tr>
                        <th class="text-center align-middle" style="width: 5%;" scope="col">ID</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Purchase Date</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Invoice Number</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Purchase Name</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Product Name</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Total Cost</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Total Quantity</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Supplier</th>
                        <th class="text-center align-middle" style="width: 7%;" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('script')
@vite(['resources/js/stock/purchase-script.js'])
<script>
let purchaseUrls = {
    'showPurchase': "{{ route('stock.purchase.show',['purchase' => 'purchaseID']) }}",
    'editPurchase': "{{ route('stock.purchase.edit',['purchase' => 'purchaseID']) }}",
    'datatable': "{{ route('stock.purchase.datatable') }}"
};

$(document).ready(function() {
    WinPos.Datatable.initDataTable("#purchaseTable", WinPos.Purchase.datatableConfiguration());

    $("#searchPurchase").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })
});
</script>
@endsection

