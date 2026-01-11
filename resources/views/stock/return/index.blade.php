@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Return List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchReturn" placeholder="Search Return">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <a href="{{ route('stock.return.create') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> New Return</a>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="returnTable">
                <thead>
                    <tr>
                        <th class="text-center align-middle" style="width: 5%;" scope="col">ID</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Date</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Customer Phone</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Sale Invoice</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Status</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Total Amount</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Adjustment</th>
                        <th class="text-center align-middle" style="width: 12%;" scope="col">Total Payable</th>
                        <th class="text-center align-middle" style="width: 14%;" scope="col">Action</th>
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
@vite(['resources/js/stock/return-script.js'])
<script>
let returnUrls = {
    'editReturn': "{{ route('stock.return.edit',['return' => 'returnID']) }}",
    'showReturn': "{{ route('stock.return.show',['return' => 'returnID']) }}",
    'datatable': "{{ route('stock.return.datatable') }}",
    'showSale': "{{ route('sales.sale.show',['sale' => 'saleID']) }}",
    'showCustomer': "{{ route('sales.customer.details',['customer' => 'customerID']) }}"
};

$(document).ready(function() {
    WinPos.Datatable.initDataTable("#returnTable", WinPos.Return.datatableConfiguration());

    $("#searchReturn").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })
});
</script>
@endsection
