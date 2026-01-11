@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between">
            <h3>Return Details #{{ $return->id }}</h3>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color" id="addPaymentBtn" data-return-id="{{ $return->id }}">
                    <i class="fa-solid fa-money-bill-wave"></i> Add Payment
                </button>

                <a href="{{ route('sales.customer.details', $return->customer_id) }}" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                    <i class="fa-solid fa-user"></i> Customer
                </a>

                <a href="{{ route('sales.sale.show', $return->sale_id) }}" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                    <i class="fa-solid fa-receipt"></i> Sale
                </a>

                <a href="{{ route('stock.return.edit', $return->id) }}" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>

                <a href="{{ url()->previous() }}" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body p-1">
            <!-- Collapsible sections (independent) -->
            <!-- Customer Information -->
            <div class="card border mb-2">
                <div class="card-header" id="headingCustomer">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseCustomer" aria-expanded="false" aria-controls="collapseCustomer">
                            <i class="fa-solid fa-user"></i> Customer Information
                        </button>
                    </h5>
                </div>
                <div id="collapseCustomer" class="collapse" aria-labelledby="headingCustomer">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Customer Name:</strong> {{ $return->customer->name ?? '-' }}</p>
                                    <p class="mb-1"><strong>Customer Phone:</strong> {{ $return->formattedCustomerPhone ?? '-' }}</p>
                                    <p class="mb-1"><strong>Customer Age Group:</strong> {{ $return->customer->age_group ?? '-' }}</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Email:</strong> {{ $return->customer->email ?? '-' }}</p>
                                    <p class="mb-1"><strong>Address:</strong> {{ $return->customer->address ?? '-' }}</p>
                                    <p class="mb-1"><strong>Gender:</strong> {{ $return->customer->gender ?? '-' }}</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Customer Type:</strong> {{ $return->customer->type ?? 'General' }}</p>
                                    <p class="mb-1"><strong>Status:</strong> 
                                        <span class="badge {{ $return->customer->isActive ? 'badge-success' : 'badge-danger' }}">
                                            {{ $return->customer->isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Sale Information -->
            <div class="card border mb-2">
                <div class="card-header" id="headingSale">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseSale" aria-expanded="false" aria-controls="collapseSale">
                            <i class="fa-solid fa-receipt"></i> Sale Information
                        </button>
                    </h5>
                </div>
                <div id="collapseSale" class="collapse" aria-labelledby="headingSale">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Invoice Code:</strong> 
                                        <a href="{{ route('sales.sale.show', $return->sale_id) }}" class="text-decoration-none">
                                            {{ $return->sale->invoice_code ?? '-' }}
                                        </a>
                                    </p>
                                    <p class="mb-1"><strong>Sale Date:</strong> {{ formatDate($return->sale->created_at ?? null) }}</p>
                                    <p class="mb-1"><strong>Sale Status:</strong> 
                                        <span class="badge badge-info">{{ ucfirst($return->sale->sale_status ?? '-') }}</span>
                                    </p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Total Amount:</strong> {{ number_format($return->sale->total_amount ?? 0, 2) }} Tk</p>
                                    <p class="mb-1"><strong>Discount Amount:</strong> {{ number_format($return->sale->discount_amount ?? 0, 2) }} Tk</p>
                                    <p class="mb-1"><strong>Total Payable:</strong> {{ number_format($return->sale->total_payable_amount ?? 0, 2) }} Tk</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Payment Status:</strong> 
                                        <span class="badge {{ $return->sale->payment_status == 'paid' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($return->sale->payment_status ?? '-') }}
                                        </span>
                                    </p>
                                    <p class="mb-1"><strong>Sales From:</strong> {{ ucfirst($return->sale->sales_from ?? '-') }}</p>
                                    <p class="mb-1"><strong>Sales Type:</strong> {{ ucfirst($return->sale->sales_type ?? '-') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Return Information -->
            <div class="card border mb-2">
                <div class="card-header" id="headingReturn">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseReturn" aria-expanded="false" aria-controls="collapseReturn">
                            <i class="fa-solid fa-rotate-left"></i> Return Information
                        </button>
                    </h5>
                </div>
                <div id="collapseReturn" class="collapse" aria-labelledby="headingReturn">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Return ID:</strong> #{{ $return->id }}</p>
                                    <p class="mb-1"><strong>Status:</strong> 
                                        <span class="badge 
                                            {{ $return->status == 'completed' ? 'badge-success' : ($return->status == 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </p>
                                    <p class="mb-1"><strong>Created At:</strong> {{ $return->formattedCreatedDate }}</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Total Amount:</strong> {{ $return->total_amount_formatted }} Tk</p>
                                    <p class="mb-1"><strong>Adjustment Amount:</strong> {{ $return->adjustment_amt_formatted }} Tk</p>
                                    <p class="mb-1"><strong>Total Payable:</strong> {{ $return->total_payable_formatted }} Tk</p>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <p class="mb-1"><strong>Created By:</strong> {{ $return->creator->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Updated At:</strong> {{ $return->formattedUpdatedDate }}</p>
                                    <p class="mb-1"><strong>Updated By:</strong> {{ $return->updater->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if(!empty($return->reason))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <p class="mb-1"><strong>Reason:</strong></p>
                                    <div class="p-2 bg-light rounded">
                                        {!! nl2br(e($return->reason)) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(!empty($return->note))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <p class="mb-1"><strong>Note:</strong></p>
                                    <div class="p-2 bg-light rounded">
                                        {!! nl2br(e($return->note)) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            <!-- Return Items -->
            <div class="card border mb-2">
                <div class="card-header" id="headingItems">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseItems" aria-expanded="false" aria-controls="collapseItems">
                            <i class="fa-solid fa-list"></i> Return Items
                        </button>
                    </h5>
                </div>
                <div id="collapseItems" class="collapse" aria-labelledby="headingItems">
                        <div class="card-body p-1">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Product Name</th>
                                            <th>Variant</th>
                                            <th class="text-center">Original Qty</th>
                                            <th class="text-center">Return Qty</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-end">Total Price</th>
                                            <th class="text-center">Is Sellable</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($return->returnItems as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->salesItem->product->name ?? '-' }}</td>
                                            <td>{{ $item->salesItem->variation ? ($item->salesItem->variation->tagline ?? '-') : ($item->salesItem->variant_tagline ?? '-') }}</td>
                                            <td class="text-center">{{ $item->salesItem->quantity ?? 0 }}</td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-end">{{ number_format($item->unit_price, 2) }} Tk</td>
                                            <td class="text-end">{{ number_format($item->unit_price * $item->qty, 2) }} Tk</td>
                                            <td class="text-center">
                                                <span class="badge {{ $item->is_sellable ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $item->is_sellable ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No return items found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="6" class="text-right"><strong>Total:</strong></td>
                                            <td class="text-end"><strong>{{ $return->total_amount_formatted }} Tk</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Payment History -->
            <div class="card border mb-2">
                <div class="card-header" id="headingPayments">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapsePayments" aria-expanded="false" aria-controls="collapsePayments">
                            <i class="fa-solid fa-money-bill-wave"></i> Payment History
                        </button>
                    </h5>
                </div>
                <div id="collapsePayments" class="collapse" aria-labelledby="headingPayments">
                        <div class="card-body p-1">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="paymentHistoryTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Payment Method</th>
                                            <th class="text-center">Payment Via</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Transaction ID</th>
                                            <th class="text-center">Note</th>
                                            <th class="text-center">Created By</th>
                                            <th class="text-center">Created At</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentHistoryTableBody">
                                        @forelse($return->payments as $index => $payment)
                                        <tr data-payment-id="{{ $payment->id }}">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">{{ ucfirst($payment->payment_method) }}</td>
                                            <td class="text-center">{{ ucfirst($payment->payment_via) }}</td>
                                            <td class="text-center">{{ number_format($payment->amount, 2) }} Tk</td>
                                            <td class="text-center">{{ $payment->transaction_id ?? '-' }}</td>
                                            <td class="text-center">{{ $payment->note ?? '-' }}</td>
                                            <td class="text-center">{{ $payment->creator->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ formatDateAndTime($payment->created_at) }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-primary edit-payment-btn" data-payment-id="{{ $payment->id }}" data-toggle="tooltip" title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-payment-btn" data-payment-id="{{ $payment->id }}" data-toggle="tooltip" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No payments found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Total Paid:</strong></td>
                                            <td class="text-center"><strong>{{ number_format($return->payments->sum('amount'), 2) }} Tk</strong></td>
                                            <td colspan="5"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

@endsection

@include('stock.return.payment-modal')

@section('script')
<script>
// Initialize returnUrls for module initialization (required even if not all routes are used)
let returnUrls = {
    'editReturn': "{{ route('stock.return.edit',['return' => 'returnID']) }}",
    'showReturn': "{{ route('stock.return.show',['return' => 'returnID']) }}",
    'datatable': "{{ route('stock.return.datatable') }}",
    'showSale': "{{ route('sales.sale.show',['sale' => 'saleID']) }}",
    'showCustomer': "{{ route('sales.customer.details',['customer' => 'customerID']) }}",
    'searchCustomer': "{{ route('stock.return.customer.search') }}",
    'getCustomerSales': "{{ route('stock.return.customer.sales') }}",
    'getSaleItems': "{{ route('stock.return.sale.items') }}",
    'saveReturn': "{{ route('stock.return.store') }}",
    'updateReturn': "{{ route('stock.return.update',['return' => 'returnID']) }}"
};

let returnPaymentUrls = {
    'storePayment': "{{ route('stock.return.payment.store', ['return' => $return->id]) }}",
    'getPayment': "{{ route('stock.return.payment.get', ['return' => $return->id, 'payment' => 'PAYMENT_ID']) }}",
    'updatePayment': "{{ route('stock.return.payment.update', ['return' => $return->id, 'payment' => 'PAYMENT_ID']) }}",
    'deletePayment': "{{ route('stock.return.payment.destroy', ['return' => $return->id, 'payment' => 'PAYMENT_ID']) }}"
};
</script>
@vite(['resources/js/stock/return-script.js'])
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Add Payment Button
    $('#addPaymentBtn').on('click', function() {
        WinPos.Return.openPaymentModal({{ $return->id }});
    });

    // Edit Payment Button
    $(document).on('click', '.edit-payment-btn', function() {
        const paymentId = $(this).data('payment-id');
        WinPos.Return.editPayment({{ $return->id }}, paymentId, returnPaymentUrls);
    });

    // Delete Payment Button
    $(document).on('click', '.delete-payment-btn', function() {
        const paymentId = $(this).data('payment-id');
        WinPos.Return.deletePayment({{ $return->id }}, paymentId, returnPaymentUrls);
    });

    // Save Payment Button
    $('#savePaymentBtn').on('click', function() {
        WinPos.Return.savePayment({{ $return->id }}, returnPaymentUrls);
    });

    // Payment Method Change
    $('#paymentMethod').on('change', function() {
        WinPos.Return.handlePaymentMethodChange($(this).val());
    });
});
</script>
@endsection
