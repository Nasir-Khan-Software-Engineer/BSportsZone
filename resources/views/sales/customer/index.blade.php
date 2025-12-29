@extends('layouts.main-layout')

@section('style')
@vite(['resources/css/setup/customer-style.css'])
@endsection

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Customer List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchCustomer" placeholder="Search Customer">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button type="button" id="createNewCustomer" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-user-plus"></i> New Customer</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered datatable" id="customerTable">
                <thead>
                    <tr>
                        @if(isFeatureEnabled('ENABLED_LOYALTY'))
                        <th class="text-center align-middle" style="width: 10%;" scope="col">ID</th>
                        <th class="text-center align-middle" style="width: 20%;" scope="col">Name</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Phone Number</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Total Service</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Created On</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Type</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Action</th>
                        @else
                        <th class="text-center align-middle" style="width: 10%;" scope="col">ID</th>
                        <th class="text-center align-middle" style="width: 25%;" scope="col">Name</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Phone Number</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Total Service</th>
                        <th class="text-center align-middle" style="width: 15%;" scope="col">Created On</th>
                        <th class="text-center align-middle" style="width: 20%;" scope="col">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('sales.customer.addEdit')

@endsection

@section('script')
@vite(['resources/js/setup/customer-script.js'])
<script>
let customerUrls = {
    'saveCustomer': "{{ route('sales.customer.store') }}",
    'editCustomer': "{{ route('sales.customer.edit',['customer' => 'customerID']) }}",
    'updateCustomer': "{{ route('sales.customer.update',['customer' => 'customerID']) }}",
    'deleteCustomer': "{{ route('sales.customer.destroy',['customer' => 'customerID']) }}",
    'detailsCustomer': "{{ route('sales.customer.details',['customer' => 'customerID']) }}",
    'getCustomerInfo': "{{ route('sales.customer.info', ['customer' => 'customerID']) }}",
    'datatable': "{{ route('sales.customer.datatable') }}",
    'loyaltyDetails': "{{ route('sales.customer.loyalty', ['customer' => 'customerID']) }}"
};

// Permission status for phone number masking
let hasShowPhonePermission = {{ hasAccess('show_phone') ? 'true' : 'false' }};

$(document).ready(function() {
    WinPos.Datatable.initDataTable("#customerTable", WinPos.Customer.datatableConfiguration());

    $("#searchCustomer").on("input search paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    });

    $("#customerAddEditForm").submit(function(event) {
        event.preventDefault();
        let data = WinPos.Common.getFormData(this);

        if ($(this).attr('data-formSubmitFor') == "create") {
            WinPos.Customer.saveCustomer(data); // we can validate here before call
        } else if ($(this).attr('data-formSubmitFor') == "update") {
            let customerID = $("#hiddenCustomerID").val();
            WinPos.Customer.updateCustomer(data, customerID); // we can validate here before call
        } else {
            return;
        }
    })

    $("#createNewCustomer").click(function() {
        $("#customerAddEditForm")[0].reset();
        $("#customerID").html('');
        $("#customerBasicInfoTab").click();
        $("#customerAddEditForm").attr('data-formSubmitFor', 'create');
        WinPos.Common.showBootstrapModal("customerAddEditModal");
    })

}); // end jquery

$(document).on('click', '.edit-customer', function() {
    WinPos.Datatable.selectRow(this);
    let customerID = Number($(this).attr("data-customerID"));
    WinPos.Customer.editCustomer(customerID);
});

$(document).on('click', '.delete-customer', function() {
    WinPos.Datatable.selectRow(this);
    var customerID = $(this).attr('data-customerID');
    let confirmation = confirm('Are you sure you want to delete this customer?');
    if (confirmation) {
        WinPos.Customer.deleteCustomer(customerID);
    } else {
        alert('Deletion canceled');
    }
});
</script>
@endsection