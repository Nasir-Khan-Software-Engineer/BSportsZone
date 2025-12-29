@extends('layouts.main-layout')


@section('title', 'Customer details')

@section('style')
<style>
</style>
@endsection

@section('content')
<div class="view-container mb-2">

    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Customer details</h3>
            <div class="d-flex gap-2">

                @if(isFeatureEnabled('ENABLED_LOYALTY'))
                <a href="{{ route('sales.customer.loyalty', ['customer' => $customer->id]) }}" data-toggle="tooltip" data-placement="top" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" title="Loyalty Card Details">
                    <i class="fa-solid fa-id-card"></i> Loyalty
                </a>
                @endif

                <!-- Customer Info Button -->
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-toggle="tooltip" data-placement="top" title="Customer Information" id="showCustomerInfoBtn">
                    <i class="fa-solid fa-people-carry-box"></i> Incormation
                </button>
                <!-- // back button -->
                <a href="{{route('sales.customer.index')}}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
            <x-customer-ribbon :customer="$customerRibbonData" />


            <div class="row">
                {{-- Sales Information Table (Left Side) --}}
                <div class="col-lg-6">
                    <div class="card border">
                        <div class="card-header">
                            <h5 class="mb-0">Sales Information</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="customerSalesTable" class="table table-bordered table-hover mb-0 border-0">
                                    <thead class="thead-light thm-tbl-header-bg thm-tbl-header-text-color">
                                        <tr>
                                            <th class="text-center">Invoice No</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Total Service</th>
                                            <th class="text-center">Paid Amount</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-0">
                                        @foreach($customer->purchases as $index => $purchase)
                                        <tr>
                                            <td class="text-center">{{ $purchase->invoice_code }}</td>
                                            <td class="text-center">{{ $purchase->formattedDateTime ?? $purchase->created_at->format('Y-m-d H:i') }}</td>
                                            <td class="text-center">{{ $purchase->items->count() }}</td>
                                            <td class="text-center">Tk. {{ number_format($purchase->payments->sum('paid_amount'), 2) }}</td>
                                            <td class="text-center">
                                                <button data-id="{{ $purchase->id }}" class="btn thm-btn-bg thm-btn-text-color show-sale-modal btn-sm" data-toggle="tooltip" title="View Sale Details">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Service Summary Table (Right Side) --}}
                <div class="col-lg-6">
                    <div class="card border">
                        <div class="card-header">
                            <h5 class="mb-0">Service Summary</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="serviceSummaryTable" class="table table-bordered table-hover mb-0 border-0">
                                    <thead class="thead-light thm-tbl-header-bg thm-tbl-header-text-color">
                                        <tr>
                                            <th class="text-center">Service Name</th>
                                            <th class="text-center">Total Taken Count</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-0">
                                        @if(count($serviceSummary) > 0)
                                            @foreach($serviceSummary as $service)
                                            <tr>
                                                <td class="text-center">{{ $service['service_name'] }}</td>
                                                <td class="text-center">{{ $service['total_taken_count'] }}</td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@include('sales.sale.modal')
@include('components.customer-info-modal')
@endsection


@section('script')
@vite(['resources/js/print-receipt-script.js', 'resources/js/sales/sale-script.js', 'resources/js/setup/customer-script.js'])

<script>
let saleUrls = {
    'showSaleModal': "{{ route('sales.sale.modal',['sale' => 'saleID']) }}"
};

let customerUrls = {
    'saveCustomer': "{{ route('sales.customer.store') }}",
    'editCustomer': "{{ route('sales.customer.edit',['customer' => 'customerID']) }}",
    'updateCustomer': "{{ route('sales.customer.update',['customer' => 'customerID']) }}",
    'deleteCustomer': "{{ route('sales.customer.destroy',['customer' => 'customerID']) }}",
    'detailsCustomer': "{{ route('sales.customer.details',['customer' => 'customerID']) }}",
    'getCustomerInfo': "{{ route('sales.customer.info', ['customer' => 'customerID']) }}"
};

// Permission status for phone number masking
let hasShowPhonePermission = {{ hasAccess('show_phone') ? 'true' : 'false' }};

let posUrls = {
    'getAccountInfo': "{{ route('pos.account.get')}}"
};

$(document).ready(function() {
    WinPos.Datatable.initDataTable('#customerSalesTable', {
        pageLength: 10,
        order: [[1, 'desc']],
        serverSide: false
    });

    // Initialize Service Summary table
    WinPos.Datatable.initDataTable('#serviceSummaryTable', {
        pageLength: 10,
        order: [[1, 'desc']], // Sort by count descending
        serverSide: false,
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip({
        html: true,
        placement: "auto",
        boundary: "window"
    });
});

// Handle show sale modal
$(document).on('click', '.show-sale-modal', function() {
    let saleID = $(this).data('id');
    WinPos.sale.showSaleModal(saleID);
});

// Handle Customer Info button click
$(document).on('click', '#showCustomerInfoBtn', function() {
    let customerId = {{ $customer->id }};
    WinPos.Customer.showCustomerInfo(customerId);
});

// Handle print button in modal
$(document).on('click', '#printSalesBtn', function(){
    WinPos.sale.printReceipt();
});
</script>
@endsection