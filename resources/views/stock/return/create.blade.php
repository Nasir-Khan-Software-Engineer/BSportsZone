@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Create New Return</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('stock.return.index') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
            <!-- Customer & Sale Selection -->
            <div class="card border mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Customer & Sale Selection</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-4 form-group">
                            <label for="customerPhone">Customer Phone Number*</label>
                            <input type="text" class="form-control rounded" name="customer_phone" id="customerPhone" placeholder="Search by phone number" autocomplete="off">
                            <input type="hidden" id="customerId" name="customer_id">
                            <div id="customerResults" class="list-group mt-2" style="display: none; position: absolute; z-index: 1000; max-height: 200px; overflow-y: auto; width: 100%;"></div>
                        </div>
                        <div class="col-12 col-lg-4 form-group">
                            <label for="customerName">Customer Name</label>
                            <input type="text" class="form-control rounded" id="customerName" readonly placeholder="Customer name will appear here">
                        </div>
                        <div class="col-12 col-lg-4 form-group" id="saleSelectionGroup" style="display: none;">
                            <label for="saleSelect">Select Sale*</label>
                            <select class="form-control rounded" name="sale_id" id="saleSelect">
                                <option value="">Select a sale</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Return Items -->
            <div class="card border mb-3" id="returnItemsCard" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Return Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="returnItemsTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Variant</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Original Qty</th>
                                    <th class="text-center">Return Qty</th>
                                    <th class="text-center">Is Sellable</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="returnItemsTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Return Information -->
            <div class="card border mb-3" id="returnInfoCard" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Return Information</h5>
                </div>
                <div class="card-body">
                    <form id="returnCreateForm">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-lg-4 form-group">
                                <label for="returnStatus">Status</label>
                                <select class="form-control rounded" name="status" id="returnStatus">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-4 form-group">
                                <label for="totalPayableAtm">Total Payable Amount*</label>
                                <input type="number" step="0.01" class="form-control rounded" name="total_payable_atm" id="totalPayableAtm" value="0.00" readonly>
                            </div>
                            <div class="col-12 col-lg-4 form-group">
                                <label for="adjustmentAmt">Adjustment Amount</label>
                                <input type="number" step="0.01" class="form-control rounded" name="adjustment_amt" id="adjustmentAmt" value="0.00" oninput="WinPos.Return.calculateTotal()">
                            </div>
                            <div class="col-12 form-group">
                                <label for="returnReason">Reason</label>
                                <textarea name="reason" id="returnReason" class="form-control rounded" rows="3" placeholder="Return reason"></textarea>
                            </div>
                            <div class="col-12 form-group">
                                <label for="returnNote">Note</label>
                                <textarea name="note" id="returnNote" class="form-control rounded" rows="3" placeholder="Additional notes"></textarea>
                            </div>
                        </div>
                        <div class="text-right mt-3">
                            <button type="button" id="saveReturn" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Create Return</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@vite(['resources/js/stock/return-script.js'])
<script>
let returnUrls = {
    'saveReturn': "{{ route('stock.return.store') }}",
    'searchCustomer': "{{ route('stock.return.customer.search') }}",
    'getCustomerSales': "{{ route('stock.return.customer.sales') }}",
    'getSaleItems': "{{ route('stock.return.sale.items') }}"
};

let selectedCustomerId = null;
let selectedSaleId = null;
let saleItems = [];
let returnItems = [];

$(document).ready(function() {
    // Customer phone search
    let typingTimer;
    
    $('#customerPhone').on('input', function() {
        // Don't search if we're programmatically setting the customer
        if (WinPos.Return.getIsSettingCustomer()) {
            return;
        }
        
        const phone = $(this).val().trim();
        clearTimeout(typingTimer);

        // Don't search if customer is already selected
        if (selectedCustomerId) {
            return;
        }

        if (phone.length >= 3) {
            typingTimer = setTimeout(() => {
                WinPos.Return.searchCustomer(phone);
            }, 300);
        } else {
            $('#customerResults').hide();
            if (phone.length === 0) {
                selectedCustomerId = null;
                WinPos.Return.resetForm();
            }
        }
    });

    // Sale selection
    $('#saleSelect').on('change', function() {
        selectedSaleId = $(this).val();
        if (selectedSaleId) {
            WinPos.Return.loadSaleItems(selectedSaleId);
        } else {
            $('#returnItemsCard').hide();
            $('#returnInfoCard').hide();
            returnItems = [];
        }
    });

    // Save return
    $('#saveReturn').on('click', function() {
        WinPos.Return.saveReturn();
    });
});
</script>
@endsection
