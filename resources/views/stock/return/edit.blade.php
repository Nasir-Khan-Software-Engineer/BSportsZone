@extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Edit Return #{{ $return->id }}</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('stock.return.index') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
            <!-- Customer Information (Read-only) -->
            <div class="card border mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <p><strong>Customer:</strong> {{ $return->customer->name ?? '-' }}</p>
                            <p><strong>Phone:</strong> {{ $return->customer->phone1 ?? '-' }}</p>
                        </div>
                        <div class="col-12 col-lg-6">
                            <p><strong>Sale Invoice:</strong> {{ $return->sale->invoice_code ?? '-' }}</p>
                            <p><strong>Sale Date:</strong> {{ formatDate($return->sale->created_at ?? null) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Return Items -->
            <div class="card border mb-3">
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
                                </tr>
                            </thead>
                            <tbody id="returnItemsTableBody">
                                @foreach($return->returnItems as $item)
                                <tr data-sales-item-id="{{ $item->sales_item_id }}">
                                    <td class="text-center">{{ $item->salesItem->product->name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->salesItem->variation ? ($item->salesItem->variation->tagline ?? '-') : ($item->salesItem->variant_tagline ?? '-') }}</td>
                                    <td class="text-center">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-center">{{ $item->salesItem->quantity ?? 0 }}</td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm rounded return-qty" 
                                               value="{{ $item->qty }}" 
                                               data-sales-item-id="{{ $item->sales_item_id }}"
                                               data-unit-price="{{ $item->unit_price }}"
                                               min="1" 
                                               max="{{ $item->salesItem->quantity ?? 0 }}">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input is-sellable" 
                                               {{ $item->is_sellable ? 'checked' : '' }}
                                               data-sales-item-id="{{ $item->sales_item_id }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Return Information -->
            <div class="card border mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Return Information</h5>
                </div>
                <div class="card-body">
                    <form id="returnUpdateForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            
                            <div class="col-12 col-lg-4 form-group">
                                <label for="returnStatus">Status</label>
                                <select class="form-control rounded" name="status" id="returnStatus">
                                    <option value="pending" {{ $return->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $return->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $return->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="col-12 col-lg-4 form-group">
                                <label for="adjustmentAmt">Adjustment Amount</label>
                                <input type="number" step="0.01" class="form-control rounded" name="adjustment_amt" id="adjustmentAmt" value="{{ $return->adjustment_amt }}" oninput="WinPos.Return.calculateTotal()">
                            </div>
                            <div class="col-12 col-lg-4 form-group">
                                <label for="totalPayableAtm">Total Payable Amount*</label>
                                <input type="number" step="0.01" class="form-control rounded" name="total_payable_atm" id="totalPayableAtm" value="{{ $return->total_payable_atm }}" readonly>
                            </div>
                            <div class="col-12 col-lg-12 form-group">
                                <label for="returnReason">Reason</label>
                                <textarea name="reason" id="returnReason" class="form-control rounded" rows="3" placeholder="Return reason">{{ $return->reason }}</textarea>
                            </div>
                            <div class="col-12 form-group">
                                <label for="returnNote">Note</label>
                                <textarea name="note" id="returnNote" class="form-control rounded" cols="30" rows="3" placeholder="Additional notes">{{ $return->note }}</textarea>
                            </div>
                        </div>
                        <div class="text-right mt-3">
                            <button type="button" id="updateReturn" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update Return</button>
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
    'updateReturn': "{{ route('stock.return.update', ['return' => $return->id]) }}"
};

let returnItems = @json($return->returnItems);

$(document).ready(function() {
    // Calculate total when qty or sellable changes
    $(document).on('input', '.return-qty, .is-sellable', function() {
        WinPos.Return.calculateTotal();
    });

    // Update return
    $('#updateReturn').on('click', function() {
        WinPos.Return.updateReturn({{ $return->id }});
    });

    // Initial calculation
    WinPos.Return.calculateTotal();
});
</script>
@endsection
