@extends('layouts.main-layout')
@section('style')
@endsection

@section('content')
<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Sales List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchSale"
                    placeholder="Search Sales">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <a href="{{route('pos.index')}}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm btn-sm"><i class="fa-solid fa-desktop"></i> POS Terminal</a>

                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="saleTable">
            <thead>
                <tr>
                    <th class="text-center align-middle" style="width: 20%;" scope="col">Invoice No.</th>
                    <th class="text-center align-middle" style="width: 15%;" scope="col">Customer</th>
                    <th class="text-center align-middle" style="width: 10%;" scope="col">Total Amt.</th>
                    <th class="text-center align-middle" style="width: 10%;" scope="col">Payable Amt.</th>
                    <th class="text-center align-middle" style="width: 10%;" scope="col">Paid Amt.</th>
                    <th class="text-center align-middle" style="width: 10%;" scope="col">Date</th>
                    <th class="text-center align-middle" style="width: 15%;" scope="col">Created By</th>
                    <th class="text-center align-middle" style="width: 10%;" scope="col">Action</th>
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
@vite(['resources/js/print-receipt-script.js', 'resources/js/sales/sale-script.js'])
<script>
let saleUrls = {
    'showSale': "{{ route('sales.sale.show',['sale' => 'saleID']) }}",
    'deleteSale': "{{ route('sales.sale.destroy',['sale' => 'saleID']) }}",
    'datatable': "{{route('sales.sale.datatable')}}"
};

// Permission status for phone number masking
let hasShowPhonePermission = {{ hasAccess('show_phone') ? 'true' : 'false' }};

let posUrls = {
    'getAccountInfo': "{{ route('pos.account.get')}}"
};

$(document).ready(function() {
    WinPos.Datatable.initDataTable("#saleTable", WinPos.sale.datatableConfiguration());

    $("#searchSale").on("input search paste cut", function (){
        WinPos.Datatable.filter($(this).val());
    })

}); // end jquery


$(document).on('click', '.delete-sale', function(event) {
    WinPos.Datatable.selectRow(this);
    if (confirm("Deleting a sale will delete the payments history as well.\nClick OK to continue or Cancel.")) {
        let saleID = $(this).data('id');
        console.log(saleID);
        WinPos.sale.deleteSale(saleID);
    }
});

$(document).on('click', '#printSalesBtn', function(){
    WinPos.sale.printReceipt();
});

</script>
@endsection
