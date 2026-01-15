@extends('layouts.main-layout')


@section('content')

<div class="view-container mb-2">

    <div class="card full-height-card">

        <!-- Header -->
        <div class="card-header d-flex justify-content-between">
            <h3>Sales Details | 

            <span class="badge text-dark bg-{{ $sale->sale_status === 'completed' ? 'success' : 'info' }} text-capitalize">
                {{ $sale->sale_status ?? 'pending' }}
            </span>
            </h3>

            <div class="d-flex gap-2 align-items-center">
                @php
                    $latestLifecycle = $sale->latestLifecycle;
                    $currentStatus = $latestLifecycle ? $latestLifecycle->status : 'Pending';
                @endphp
                
                @if($currentStatus === 'Pending')
                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color update-lifecycle-btn" data-status="Confirmed">
                        <i class="fa-solid fa-check"></i> Mark as Confirmed
                    </button>
                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color update-lifecycle-btn" data-status="Cancelled">
                        <i class="fa-solid fa-times"></i> Mark as Cancelled
                    </button>
                @elseif($currentStatus === 'Confirmed')
                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color update-lifecycle-btn" data-status="Delivered to Courier">
                        <i class="fa-solid fa-truck"></i> Delivered to Courier
                    </button>
                @elseif($currentStatus === 'Delivered to Courier')
                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color update-lifecycle-btn" data-status="Received">
                        <i class="fa-solid fa-check-circle"></i> Received
                    </button>
                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color update-lifecycle-btn" data-status="Customer Returned">
                        <i class="fa-solid fa-undo"></i> Customer Returned
                    </button>
                @endif

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
                        <p class="mb-1"><strong>Customer Phone:</strong> {{ $sale->formatedCustomerPhone ?? '-' }}</p>
                        <p class="mb-1"><strong>Order ID:</strong> {{ $sale->invoice_code ?? '-' }}</p>
                        <p class="mb-1"><strong>Order Source:</strong> 
                            <span class="badge bg-{{ $sale->sales_from === 'online' ? 'success' : 'secondary' }}">
                                {{ ucfirst($sale->sales_from ?? 'offline') }}
                            </span>
                        </p>
                    </div>

                    <!-- Section 2 -->
                    <div class="flex-grow-1 mr-3">
                        
                        <p class="mb-1" style="max-width: 350px;"><strong>Shipping Address:</strong> {{ $sale->shipping_address ?? '-' }}</p>
                        <p class="mb-1"><strong>Delivery Area:</strong> {{ $sale->delivery_area ? ucfirst($sale->delivery_area) : '-' }}</p>
                        <p class="mb-1"><strong>Total Item Qty:</strong> {{ $sale->items->sum('quantity') ?? 0 }}</p>
                    </div>

                    <!-- Section 3 -->
                    <div class="flex-grow-1 mr-3">
                        <p class="mb-1"><strong>Order Date:</strong> {{ $sale->formattedCreatedDate }}</p>
                        <p class="mb-1"><strong>Discount:</strong> {{ $sale->discountText }}</p>
                        <p class="mb-1"><strong>Adjustment:</strong> {{ $sale->adjustmentText }}</p>
                    </div>

                    <!-- Section 4 -->
                    <div class="flex-grow-1">
                        <p class="mb-1"><strong>Total Amount:</strong> 
                            <span class="badge bg-primary fs-6">{{ number_format($sale->total_amount, 2) }} Tk</span>
                        </p>
                        <p class="mb-1"><strong>Delivery Charge:</strong> 
                            <span class="badge bg-primary fs-6">{{ number_format($sale->delivery_cost, 2) }} Tk</span>
                        </p>
                        <p class="mb-1"><strong>Total Payable:</strong> 
                            <span class="badge bg-success fs-6">{{ number_format($sale->total_payable_amount, 2) }} Tk</span>
                        </p>
                        <p class="mb-1"><strong>Payment Status:</strong> 
                            <span class="badge bg-{{ $sale->payment_status === 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($sale->payment_status ?? 'pending') }}
                            </span>
                        </p>
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
            @if(count($serviceList) > 0)
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
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Total Price</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($serviceList as $item)
                                <tr>
                                    <td>{{ $item['code'] ?? '-' }}</td>
                                    <td>{{ $item['name'] ?? '-' }}</td>
                                    <td class="text-center">{{ $item['staff_name'] }}</td>
                                    <td class="text-center">{{ $item['quantity'] }}</td>
                                    <td class="text-end">{{ number_format($item['selling_price'], 2) }} Tk</td>
                                    <td class="text-end">
                                        @if($item['discount_type'] && $item['discount_value'])
                                            @if($item['discount_type'] == 'percentage')
                                                {{ number_format($item['discount_value'], 2) }}%
                                            @else
                                                {{ number_format($item['discount_amount'], 2) }} Tk
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($item['total_price'], 2) }} Tk</td>
                                </tr>
                            @endforeach
                            <tr class="table-info fw-bold">
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-center"><strong>{{ collect($serviceList)->sum('quantity') }}</strong></td>
                                <td colspan="2"></td>
                                <td class="text-end"><strong>{{ number_format(collect($serviceList)->sum('total_price'), 2) }} Tk</strong></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            @endif

            <!-- PRODUCT LIST -->
            <div class="card border mb-3">
                <div class="card-body p-1">
                    <h5 class="mb-2">Product List</h5>

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th class="text-center">QTY</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Discount</th>
                                <th class="text-end">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productList as $item)
                                <tr>
                                    <td>{{ $item['code'] ?? '-' }}</td>
                                    <td>{{ $item['name'] ?? '-' }} {{($item['tagline'])}}</td>
                                    <td class="text-center">{{ $item['quantity'] }}</td>
                                    <td class="text-end">
                                        {{ number_format($item['selling_price'], 2) }} Tk
                                    </td>
                                    <td class="text-end">
                                        @if($item['discount_type'] && $item['discount_value'])
                                            @if($item['discount_type'] == 'percentage')
                                                {{ number_format($item['discount_value'], 2) }}%
                                            @else
                                                {{ number_format($item['discount_amount'], 2) }} Tk
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($item['total_price'], 2) }} Tk
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No product items found
                                    </td>
                                </tr>
                            @endforelse
                            @if(count($productList) > 0)
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-center"><strong>{{ collect($productList)->sum('quantity') }}</strong></td>
                                    <td colspan="2"></td>
                                    <td class="text-end"><strong>{{ number_format(collect($productList)->sum('total_price'), 2) }} Tk</strong></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- PAYMENT LIST -->
            <div class="card border mb-3">
                <div class="card-body p-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Payments</h5>
                        <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color" id="addPaymentBtn" 
                                @if($sale->payment_status === 'paid') disabled @endif>
                            <i class="fa-solid fa-plus"></i> Add Payment
                        </button>
                    </div>

                    <table class="table table-bordered">
                        <colgroup>
                            <col style="width:12%;">   <!-- P. Method -->
                            <col style="width:12%;">   <!-- P. Via -->
                            <col style="width:13%;">   <!-- Paid Amount -->
                            <col style="width:18%;">   <!-- Transaction ID -->
                            <col style="width:15%;">   <!-- Received By -->
                            <col style="width:15%;">   <!-- Payment Date -->
                            <col style="width:15%;">   <!-- Action -->
                        </colgroup>

                        <thead class="table-light">
                            <tr>
                                <th>P. Method</th>
                                <th>P. Via</th>
                                <th class="text-end">Paid Amount</th>
                                <th>Transaction ID</th>
                                <th>Received By</th>
                                <th>Payment Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($sale->payments as $p)
                            <tr>
                                <td>{{ $p->payment_method }}</td>
                                <td>{{ $p->payment_via }}</td>
                                <td class="text-end">{{ number_format($p->paid_amount,2) }}</td>
                                <td>{{ $p->transaction_id ?? '-' }}</td>
                                <td>{{ $p->receivedBy }}</td>
                                <td>{{ $p->formattedDate }} {{ $p->formattedTime }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info view-payment-btn" 
                                            data-payment-id="{{ $p->id }}"
                                            data-payment-method="{{ $p->payment_method }}"
                                            data-payment-via="{{ $p->payment_via }}"
                                            data-paid-amount="{{ number_format($p->paid_amount,2) }}"
                                            data-transaction-id="{{ $p->transaction_id ?? '-' }}"
                                            data-note="{{ $p->note ?? '-' }}"
                                            data-received-by="{{ $p->receivedBy }}"
                                            data-payment-date="{{ $p->formattedDate }} {{ $p->formattedTime }}"
                                            title="View Payment">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning edit-payment-btn" 
                                            data-payment-id="{{ $p->id }}"
                                            title="Edit Payment" disabled>
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-payment-btn" 
                                            data-payment-id="{{ $p->id }}"
                                            title="Delete Payment" disabled>
                                        <i class="fa-solid fa-trash"></i>
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

</div>

<!-- Order Lifecycle Update Modal -->
<div class="modal fade" id="updateLifecycleModal" tabindex="-1" role="dialog" aria-labelledby="updateLifecycleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="updateLifecycleModalLabel">Update Order Status</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateLifecycleForm">
                    @csrf
                    <input type="hidden" id="lifecycleSaleId" name="sale_id" value="{{ $sale->id }}">
                    <input type="hidden" id="lifecycleStatus" name="status">
                    <input type="hidden" id="lifecycleCreatedBy" name="created_by" value="{{ auth()->user()->name }}">
                    
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="lifecycleNote">Note <small class="text-muted">(Optional)</small></label>
                            <textarea name="note" id="lifecycleNote" class="form-control rounded" rows="3" placeholder="Enter any additional notes"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Close
                </button>
                <button type="button" class="btn thm-btn-bg thm-btn-text-color btn-sm" id="saveLifecycleBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Update Status
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
@include('sales.sale.add-payment-modal')
@vite(['resources/js/print-receipt-script.js', 'resources/js/sales/sale-script.js'])
<script>
let saleUrls = {
    'showSale': "{{ route('sales.sale.show',['sale' => 'saleID']) }}",
    'deleteSale': "{{ route('sales.sale.destroy',['sale' => 'saleID']) }}",
    'datatable': "{{route('sales.sale.datatable')}}",
    'addPayment': "{{ route('sales.sale.payment.store', ['sale' => $sale->id]) }}",
    'updateLifecycle': "{{ route('sales.sale.lifecycle.update', ['sale' => $sale->id]) }}"
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
    WinPos.sale.printReceipt();
 });

 // Set sales ID in add payment form
 $('#salesId').val({{ $sale->id }});

 // Handle payment method change
 $('#paymentMethod').on('change', function() {
     const method = $(this).val();
     const viaGroup = $('#paymentViaGroup');
     const transactionGroup = $('#transactionIdGroup');
     const viaSelect = $('#paymentVia');
     
     viaGroup.hide();
     transactionGroup.hide();
     viaSelect.html('<option value="">Select Option</option>');
     viaSelect.prop('required', false);
     $('#transactionId').prop('required', false);
     
     if (method === 'card') {
         viaGroup.show();
         viaSelect.prop('required', true);
         transactionGroup.show();
         $('#transactionId').prop('required', true);
         viaSelect.append('<option value="visa">Visa</option>');
         viaSelect.append('<option value="mastercard">MasterCard</option>');
         viaSelect.append('<option value="amex">American Express</option>');
     } else if (method === 'wallet') {
         viaGroup.show();
         viaSelect.prop('required', true);
         transactionGroup.show();
         $('#transactionId').prop('required', true);
         viaSelect.append('<option value="bkash">bKash</option>');
         viaSelect.append('<option value="nagad">Nagad</option>');
         viaSelect.append('<option value="rocket">Rocket</option>');
     } else if (method === 'cash') {
         viaSelect.append('<option value="cash">Cash</option>');
         viaSelect.val('cash');
         // Set payment_via to cash for cash payments
         $('#paymentVia').val('cash');
     }
 });

 // Open add payment modal
 $('#addPaymentBtn').on('click', function() {
     $('#addSalesPaymentForm')[0].reset();
     $('#paymentViaGroup').hide();
     $('#transactionIdGroup').hide();
     $('#paymentVia').html('<option value="">Select Option</option>');
     $('#salesId').val({{ $sale->id }});
     WinPos.Common.showBootstrapModal('addSalesPaymentModal');
 });

 // Save payment
 $('#saveSalesPaymentBtn').on('click', function() {
     const form = $('#addSalesPaymentForm');
     if (form[0].checkValidity()) {
         const formData = WinPos.Common.getFormData('#addSalesPaymentForm');
         
         // Set payment_via for cash if not set
         if (formData.payment_method === 'cash' && !formData.payment_via) {
             formData.payment_via = 'cash';
         }
         
         // Validate payment_via is set for card and wallet
         if ((formData.payment_method === 'card' || formData.payment_method === 'wallet') && !formData.payment_via) {
             toastr.error('Please select payment via.');
             return;
         }
         
         // Validate amount
         if (!formData.paid_amount || parseFloat(formData.paid_amount) <= 0) {
             toastr.error('Please enter a valid amount.');
             return;
         }
         
         WinPos.Common.postAjaxCall(saleUrls.addPayment, JSON.stringify(formData), function(response) {
             if (response.status === 'success') {
                 toastr.success(response.message || 'Payment added successfully');
                 WinPos.Common.hideBootstrapModal('addSalesPaymentModal');
                 // Reload page to show updated payment status
                 location.reload();
             } else {
                 if (response.message) {
                     toastr.error(response.message);
                 }
                 if (response.errors) {
                     WinPos.Common.showValidationErrors(response.errors);
                 }
             }
         }, function(xhr) {
             var response = xhr.responseJSON || {};
             if (response.message) {
                 toastr.error(response.message);
             } else {
                 toastr.error('An error occurred while adding payment');
             }
             if (response.errors && !response.message) {
                 WinPos.Common.showValidationErrors(response.errors);
             }
         });
     } else {
         form[0].reportValidity();
     }
 });

 // View payment details
 $(document).on('click', '.view-payment-btn, .view-payment-note', function(e) {
     e.preventDefault();
     $('#viewPaymentMethod').text($(this).data('payment-method') || '-');
     $('#viewPaymentVia').text($(this).data('payment-via') || '-');
     $('#viewPaidAmount').text($(this).data('paid-amount') || '-');
     $('#viewTransactionId').text($(this).data('transaction-id') || '-');
     $('#viewPaymentNote').text($(this).data('note') || '-');
     $('#viewReceivedBy').text($(this).data('received-by') || '-');
     $('#viewPaymentDate').text($(this).data('payment-date') || '-');
     WinPos.Common.showBootstrapModal('viewSalesPaymentModal');
 });

 // Order Lifecycle Update
 $(document).on('click', '.update-lifecycle-btn', function() {
     const status = $(this).data('status');
     $('#updateLifecycleModalLabel').text('Update Order Status - ' + status);
     $('#updateLifecycleForm')[0].reset();
     $('#lifecycleSaleId').val({{ $sale->id }});
     $('#lifecycleStatus').val(status);
     $('#lifecycleCreatedBy').val('{{ auth()->user()->name }}');
     WinPos.Common.showBootstrapModal('updateLifecycleModal');
 });

 // Save lifecycle update
 $('#saveLifecycleBtn').on('click', function() {
     const form = $('#updateLifecycleForm');
     if (form[0].checkValidity()) {
         const formData = WinPos.Common.getFormData('#updateLifecycleForm');
         
         WinPos.Common.postAjaxCall(saleUrls.updateLifecycle, JSON.stringify(formData), function(response) {
             if (response.status === 'success') {
                 toastr.success(response.message || 'Order status updated successfully');
                 WinPos.Common.hideBootstrapModal('updateLifecycleModal');
                 // Reload page to show updated status
                 location.reload();
             } else {
                 if (response.message) {
                     toastr.error(response.message);
                 }
                 if (response.errors) {
                     WinPos.Common.showValidationErrors(response.errors);
                 }
             }
         }, function(xhr) {
             var response = xhr.responseJSON || {};
             if (response.message) {
                 toastr.error(response.message);
             } else {
                 toastr.error('An error occurred while updating order status');
             }
             if (response.errors && !response.message) {
                 WinPos.Common.showValidationErrors(response.errors);
             }
         });
     } else {
         form[0].reportValidity();
     }
 });

});


// $(document).on('click', '#printSalesBtn', function(){
//     alert("Print Sales");
//    // WinPos.sale.printReceipt();
// });

</script>
@endsection