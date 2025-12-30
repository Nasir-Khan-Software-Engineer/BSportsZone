@extends('layouts.main-layout')

@section('style')

@endsection

@section('content')

<div class="view-container mb-2">

    <div class="card full-height-card">

        <!-- Header -->
        <div class="card-header d-flex justify-content-between">
            <h3>Sales Details</h3>

            <div class="d-flex gap-2">

                <a href="{{ route('sales.customer.details', $sale->customerId) }}" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                    <i class="fa-solid fa-user"></i> Customer
                </a>

                <button id="printSalesBtn" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                    <i class="fa-solid fa-print"></i> Print Invoice
                </button>

                <a href="{{ url()->previous() }}" class="btn btn-sm thm-btn-bg thm-btn-text-color">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body p-1">

            <div class="card border mb-3">
                <div class="card-body d-flex flex-wrap justify-content-between p-2">
                    <!-- Section 1 -->
                    <div class="flex-grow-1 mr-3">
                        <p class="mb-1"><strong>Customer Name:</strong> {{ $sale->customer->name ?? '-' }}</p>
                        <p class="mb-1"><strong>Customer Phone:</strong> {{ $sale->customer->phone1 ?? '-' }}</p>
                        <p class="mb-1"><strong>Invoice Number:</strong> {{ $sale->invoice_code ?? '-' }}</p>
                    </div>

                    <!-- Section 2 -->
                    <div class="flex-grow-1 mr-3">
                        <p class="mb-1"><strong>Total Service Qty:</strong> {{ $sale->items->sum('quantity') ?? 0 }}</p>
                        <p class="mb-1"><strong>Created At:</strong> {{ $sale->formattedCreatedDate }}</p>
                        <p class="mb-1"><strong>Created By:</strong> {{ $sale->createdByUser->name ?? '-' }}</p>
                    </div>

                    <!-- Section 3 -->
                    <div class="flex-grow-1 mr-3">
                        <p class="mb-1"><strong>Customer Name:</strong> {{ $sale->customer->name ?? '-' }}</p>
                        <p class="mb-1"><strong>Customer Phone:</strong> {{ $sale->formatedCustomerPhone ?? '-' }}</p>
                        <p class="mb-1"><strong>Customer Age Group:</strong> {{ $sale->customer->age_group ?? '-' }}</p>
                    </div>

                    <!-- Section 4 -->
                    <div class="flex-grow-1">
                        <p class="mb-1"><strong>Total Amount:</strong> {{ number_format($sale->total_amount, 2) }} Tk</p>
                        <p class="mb-1"><strong>Discount:</strong> {{ $sale->discountText }}</p>
                        <p class="mb-1"><strong>Adjustment:</strong> {{ $sale->adjustmentText }}</p>
                    </div>
                </div>
            </div>


            <!-- NOTE -->
            @if(!empty($sale->note))
            <div class="card border mb-3">
                <div class="card-body p-1">
                    <h6>Note</h6>
                    <div class="p-2 bg-light rounded">
                        {!! nl2br(e($sale->note)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- SERVICE LIST -->
            <div class="card border mb-3">
                <div class="card-body p-1">
                    <h5 class="mb-2">Service List</h5>

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Service Code</th>
                                <th>Service Name</th>
                                <th class="text-center">Staff</th>
                                <th class="text-center">QTY</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->items as $item)
                            <tr>
                                <td>{{ $item->service->code }}</td>
                                <td>{{ $item->service->name }}</td>
                                <td class="text-center">{{ $item->staff->name ?? 'None' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->selling_price,2) }} Tk</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

            <!-- PAYMENT LIST -->
            <div class="card border mb-3">
                <div class="card-body p-1">
                    <h5 class="mb-2">Payments</h5>

                    <table class="table table-bordered">
                        <colgroup>
                            <col style="width:10%;">   <!-- P. Method -->
                            <col style="width:10%;">   <!-- P. Via -->
                            <col style="width:10%;">   <!-- Paid Amount -->
                            <col style="width:10%;">   <!-- Transaction ID -->
                            <col style="width:40%;">   <!-- Note (40%) -->
                            <col style="width:10%;">   <!-- Received By -->
                            <col style="width:10%;">   <!-- Payment Date -->
                        </colgroup>

                        <thead class="table-light">
                            <tr>
                                <th>P. Method</th>
                                <th>P. Via</th>
                                <th class="text-end">Paid Amount</th>
                                <th>Transaction ID</th>
                                <th>Note</th>
                                <th>Received By</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($sale->payments as $p)
                            <tr>
                                <td>{{ $p->payment_method }}</td>
                                <td>{{ $p->payment_via }}</td>
                                <td class="text-end">{{ number_format($p->paid_amount,2) }}</td>
                                <td>{{ $p->transaction_id ?? '-' }}</td>
                                <td>{{ $p->note ?? '-' }}</td>
                                <td>{{ $p->receivedBy }}</td>
                                <td>{{ $p->formattedDate }} {{ $p->formattedTime }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

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

$(document).ready(function() {
// we need to pass the $sale as json

    WinPos.sale.setCurrentSalesDetails({
        status: "success",
        sale: @json($sale),
        loyaltyHistories: @json($sale->loyaltyHistories),
        created_by_user: @json($sale->createdByUser),
        updated_by_user: @json($sale->updatedByUser),
        payments: @json($sale->payments)
    });

 $("#printSalesBtn").on('click', function(){
    debugger;
    WinPos.sale.printReceipt();
 });

});


// $(document).on('click', '#printSalesBtn', function(){
//     alert("Print Sales");
//     debugger;
//    // WinPos.sale.printReceipt();
// });

</script>
@endsection