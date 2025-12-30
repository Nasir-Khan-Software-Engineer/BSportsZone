@extends('layouts.main-layout')

@section('style')
<style>
.customer-section p {
    font-size: 16px !important;
}
</style>
@endsection

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Loyalty Details</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                @if($loyaltyStatus['status'] == 'Loyal' || $loyaltyStatus['status'] == 'Limited')
                <button type="button" title="Edit Loyalty Card" data-toggle="tooltip" data-placement="top" data-bs-toggle="modal" data-bs-target="#editLoyaltyCardModal" id="editLoyalty"
                    class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-pen"></i> Edit Loyalty Card</button>
                @else
                    @if($loyaltyStatus['isEligibleForNewCard'])
                    <button type="button" title="Add Loyalty Card" data-toggle="tooltip" data-placement="top" data-bs-toggle="modal" data-bs-target="#addLoyaltyCardModal" id="createNewLoyalty"
                        class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> Loyalty Card</button>
                    @else
                    <button disabled type="button" title="This customer is not eligible for a Loyalty Card" data-placement="top" data-toggle="tooltip" 
                        class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">Not Eligible for Loyalty Card</button>
                    @endif
                @endif

                <a href="{{ url()->previous() }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
            <div class="row">
                <div class="col-12">
                    <div class="card border mb-1" id="customerLoyaltyRibbon">
                        <div class="card-body d-flex flex-wrap justify-content-between p-2">

                            <!-- Section 1: Customer Info -->
                            <div class="customer-section mr-4">
                                <p class="mb-1"><strong>Card Status:</strong> {{$loyaltyStatus['cardStatus']['status']}}</p>
                                <p class="mb-1"><strong>Card Number:</strong> {{$loyaltyStatus['cardStatus']['card_number']}}</p>
                                <p class="mb-1"><strong>Total Visits:</strong> 
                                {{$loyaltyStatus['cardStatus']['visits_used']}}/{{$loyaltyStatus['cardStatus']['max_visits']}} @if($loyaltyStatus['cardStatus']['status'] == 'Limited') (Used Today) @endif
                                </p>
                            </div>

                            <!-- Section 2: Loyalty Card -->
                            <div class="customer-section mr-4">
                                <p class="mb-1"><strong>Issued Date:</strong> {{$loyaltyStatus['cardStatus']['issued_at']}}</p>
                                <p class="mb-1"><strong>Valid Unit:</strong> {{$loyaltyStatus['cardStatus']['valid_until']}}</p>
                                <p class="mb-1"><strong>Total Discount:</strong> {{$loyaltyStatus['totalDiscount']}} Tk</p>
                                <!-- // need this total discount? -->
                            </div>

                            <!-- Section 3: Sales Info -->
                            <div class="customer-section">
                                <p class="mb-1"><strong>New Card:</strong> {{ $loyaltyStatus['isEligibleForNewCard'] ? 'Eligible For New Card' : 'Not Eligible For New Card' }}</p>
                                @if($loyaltyStatus['settings']['minimum_sales_amount_applies_for'] == 'Single')
                                    <p class="mb-1"><strong>Max Sales:</strong> {{$loyaltyStatus['currentTotalSpent']}} Tk</p>
                                    <p class="mb-1"><strong>Required:</strong> {{$loyaltyStatus['needForNextCard']}} Tk (Single Sale)</p>
                                @else
                                    <p class="mb-1"><strong>Total Spent:</strong> {{$loyaltyStatus['currentTotalSpent']}} Tk / {{$loyaltyStatus['minSales']}} Tk</p>
                                    <p class="mb-1"><strong>Required:</strong> {{$loyaltyStatus['needForNextCard']}} Tk</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card border mb-2" id="loyaltyHistoryCard">

                        <!-- Card Header -->
                        <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
                            <h6 class="mb-0">Card's Transaction History</h6>

                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <select class="form-control form-control-sm rounded" style="min-width:200px;" id="historyCardSelector">
                                    <option value="">-- Select Loyalty Card --</option>
                                    @foreach($loyaltyCards['cards'] as $card)
                                    <option value="{{ $card['card_id'] }}">
                                        {{ $card['card_number'] }} ({{ $card['status'] }})
                                    </option>
                                    @endforeach
                                </select>

                                <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="filterHistoryBtn">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                            </div>
                        </div>

                        <!-- Card Body (Table) -->
                        <div class="card-body p-2">
                            <div style="overflow-x: auto; overflow-y: auto; max-height: 650px;">
                                <table class="table table-bordered" id="cardHistoryTable">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" style="width: 5%;">Visits</th>
                                            <th scope="col" class="text-center" style="width: 20%;">Date</th>
                                            <th scope="col" class="text-center" style="width: 20%;">Invoice No</th>
                                            <th scope="col" class="text-center" style="width: 15%;">Total Amount</th>
                                            <th scope="col" class="text-center" style="width: 15%;">Discount</th>
                                            <th scope="col" class="text-center" style="width: 15%;">Discount Amount</th>
                                            <th scope="col" class="text-center" style="width: 25%;">Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data rows go here -->
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



@if($loyaltyStatus['status'] == 'Loyal' || $loyaltyStatus['status'] == 'Limited')
<!-- Edit Loyalty Card Modal -->
<div class="modal fade" id="editLoyaltyCardModal" tabindex="-1" aria-labelledby="editLoyaltyCardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editLoyaltyCardForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLoyaltyCardModalLabel">Edit Loyalty Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="card_id" id="lc_card_id" value="{{ $loyaltyStatus['cardStatus']['card_id'] }}">

                    <div class="mb-3">
                        <label for="card_number" class="form-label">Card Number</label>
                        <input @if($loyaltyStatus['cardStatus']['visits_used']>0) readonly @endif value="{{$loyaltyStatus['cardStatus']['card_number']}}" type="text" class="form-control" id="card_number" name="card_number" minlength="11"
                        maxlength="20" placeholder="Only numbers" required pattern="\d+">
                        <div class="form-text">
                            @if($loyaltyStatus['cardStatus']['visits_used'] >0)
                            This card has already been used.
                            @else
                            11–20 digits. Numbers only.
                            @endif
                        </div>
                    </div>

                    <div class="">
                        <label for="valid_until" class="form-label">Valid Until</label>
                        <input type="date" value="{{ \Carbon\Carbon::parse($loyaltyStatus['cardStatus']['valid_until'])->format('Y-m-d') }}" class="form-control" id="valid_until" name="valid_until" min="{{ date('Y-m-d') }}" required>
                        <div class="form-text">Must be a future date (greater than issue date ({{$loyaltyStatus['cardStatus']['issued_at']}})).</div>
                    </div>

                    <div id="lc_errors" class="d-none alert alert-danger"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-bs-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button id="lc_save_btn" type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">
                        <i class="fa-solid fa-floppy-disk"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@elseif($loyaltyStatus['isEligibleForNewCard'])
<!-- Add Loyalty Card Modal -->
<div class="modal fade" id="addLoyaltyCardModal" tabindex="-1" aria-labelledby="addLoyaltyCardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addLoyaltyCardForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLoyaltyCardModalLabel">Add Loyalty Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="lc_customer_id" value="{{ $customerId }}">

                    <div class="mb-3">
                        <label for="card_number" class="form-label">Card Number</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" minlength="11" maxlength="20" placeholder="Only numbers" required pattern="\d+">
                        <div class="form-text">11–20 digits. Numbers only.</div>
                    </div>

                    <div class="">
                        <label for="valid_until" class="form-label">Valid Until</label>
                        <input type="date" class="form-control" id="valid_until" name="valid_until" min="{{ date('Y-m-d') }}" required>
                        <div class="form-text">Must be a future date (greater than today).</div>
                    </div>

                    <div id="lc_errors" class="d-none alert alert-danger"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-bs-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button id="lc_save_btn" type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">
                        <i class="fa-solid fa-floppy-disk"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif



@endsection

@section('script')
@vite('resources/js/loyalty/loyalty-script.js')
<script>
let loyaltyUrls = {
    'saveLoyaltyCard': "{{ route('sales.customer.loyalty.cards.store') }}",
    'getCardHistory': "{{ route('sales.customer.loyalty.cards.history', ['cardId' => 'cardId']) }}",
    'updateLoyaltyCard': "{{ route('sales.customer.loyalty.cards.update', ['cardId' => 'cardId']) }}"
};



$(document).ready(function() {

    // Numeric-only enforcement
    $('#card_number').on('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 20);
    });

    // Quick date validation before submit

    $("#addLoyaltyCardForm").submit(function(event) {
        event.preventDefault();
        let data = WinPos.Common.getFormData(this);
        const clientErrors = WinPos.Loyalty.validateCardForm();

        if (clientErrors.length) {
            toastr.error(clientErrors.join('<br>'));
            return;
        }

        console.log(data);

        WinPos.Loyalty.saveLoyaltyCard(data);
    })

    $("#editLoyaltyCardForm").submit(function(event) {
        event.preventDefault();
        let data = WinPos.Common.getFormData(this);

        console.log(data);

        WinPos.Loyalty.updateLoyaltyCard(data, $('#lc_card_id').val());
    })

});

$('#filterHistoryBtn').on('click', function() {
    let cardId = $('#historyCardSelector').val();
    if (!cardId) {
        alert("Please select a card first!");
        return;
    }
    WinPos.Loyalty.getCardHistory(cardId);
});
</script>
@endsection