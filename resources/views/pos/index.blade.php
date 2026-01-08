@extends('layouts.main-layout')
@section('style')
@vite(['resources/css/pos/pos-style.css'])
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')

@include('pos.cash-payment-modal')
@include('pos.card-payment-modal')
@include('pos.wallet-payment-modal')
@include('pos.discount-modal')
@include('pos.staff-assign-modal')
@include('components.customer-info-modal')
@include('pos.last-sales-history-modal')


@if(isFeatureEnabled('ENABLED_LOYALTY'))

@include('pos.loyalty-rules-modal')
@include('pos.loyalty-card-verification-modal')
@include('pos.loyalty-history-model')

@endif


<div class="row">
    <!-- POS Terminal -->
    <div class="col-md-8">
        <div class="card pos-page-font-size">
            <div class="card-header d-flex justify-content-between align-items-center terminal-header">
                <span>POS Terminal</span>
                <div class="d-flex align-items-center gap-2">

                    @if(isFeatureEnabled('ENABLED_LOYALTY'))
                    <button disabled id="posLoyaltyHistoryModalBtn" title="Loyalty History" data-toggle="tooltip" data-placement="top"
                        class="pos-page-font-size btn thm-btn-bg thm-btn-text-color rounded btn-sm lh-btn">
                        <i class="fa-solid fa-id-card"></i> LH
                    </button>

                    <button id="posLoyaltyRulesModalBtn" title="Loyalty Rules" data-toggle="tooltip" data-placement="top"
                        class="pos-page-font-size btn thm-btn-bg thm-btn-text-color rounded btn-sm lr-btn">
                        <i class="fa-solid fa-id-card"></i> LR
                    </button>
                    @endif

                    <button disabled id="posLastSalesHistoryModalBtn" type="button" data-toggle="tooltip" data-placement="top"
                        class="pos-page-font-size btn thm-btn-bg thm-btn-text-color rounded btn-sm sh-btn" title="Last Sales History">
                        <i class="fa-solid fa-dolly"></i> SH
                    </button>

                    <button disabled id="posCustomerInfoModalBtn" type="button" data-toggle="tooltip" data-placement="top"
                        class="pos-page-font-size btn thm-btn-bg thm-btn-text-color rounded btn-sm customer-info-modal-icon ci-btn" title="Customer Information">
                        <i class="fa-solid fa-people-carry-box"></i> CI
                    </button>
                </div>
            </div>
            <div class="card-body p-1">
                <form id="pos-form">

                    <div class="card border mb-1 p-1" id="terminalCustomerAddForm">
                        <div class="mb-2 row">
                            <div class="col-sm-4">
                                <label class="terminal-input-label" for="terminalCustomerAgeGroup">Customer Age group
                                    <span><span style="opacity: 0;" class="text-danger required-star">*</span></span>
                                </label>
                                <div class="input-group mb-1">
                                    <select name="terminalCustomerAgeGroup" id="terminalCustomerAgeGroup" class="form-select rounded pos-page-font-size">
                                        <option value="">Select Age Group</option>
                                        <option value="Teen (13–19)">Teen (13–19)</option>
                                        <option value="Young Adult (20–35)">Young Adult (20–35)</option>
                                        <option value="Adult (36–55)">Adult (36–55)</option>
                                        <option value="Senior (56+)">Senior (56+)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <label class="terminal-input-label" for="terminalCustomerName">Customer Name
                                    <span><span class="text-danger required-star">*</span></span>
                                </label>
                                <div class="input-group mb-1">
                                    <input name="terminalCustomerName" autocomplete="off" type="text" class="form-control rounded pos-page-font-size" id="terminalCustomerName"
                                        placeholder="Customer Name">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <label class="terminal-input-label" for="terminalCustomerPhone">Customer Phone
                                    <span><span class="text-danger required-star">*</span></span>
                                </label>
                                <div class="d-flex align-items-center position-relative mb-1">
                                    <input name="terminalCustomerPhone" oninput="this.value=this.value.replace(/\D/g,'')" type="text" pattern="^\d+$" autocomplete="off" maxlength="11"
                                        id="terminalCustomerPhone" placeholder="Customer Phone | Search By Phone" class="form-control rounded me-2 pos-page-font-size">

                                    <button id="terminalCustomerAddBtn" type="button" class="btn thm-btn-bg thm-btn-text-color pos-page-font-size" data-toggle="tooltip" data-placement="top"
                                        title="Add Customer">
                                        <i class="fa fa-solid fa-user-plus"></i>
                                    </button>
                                    <div id="terminalCustomerResults" class="list-group" style="position:absolute; z-index:999; width:100%; top:100%; left:0; display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('pos.pos-terminal-customer-ribbon')

                    <!-- Service Table -->
                    <div class="full-height">
                        <table class="table table-bordered pos-page-font-size" id="service-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50%;">Service</th>
                                    <th style="width: 10%;" class="text-center">Quantity</th>
                                    <th style="width: 15%;" class="text-center">Price</th>
                                    <th style="width: 15%;" class="text-center">Subtotal</th>
                                    <th style="width: 10%;" class="text-center"><i class="fa fa-solid fa-times fw-bold"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- Totals -->
                    <div class="total-box d-flex justify-content-between" style="font-size: 20px;">
                        <div class="d-flex flex-column">
                            <span>Items:</span>
                            <span id="total-items">0</span>
                        </div>

                        <div class="d-flex flex-column">
                            <span>Total:</span>
                            <span id="total-price">0.00</span>
                        </div>

                        <div class="d-flex flex-column">
                            <span>Discount: <span id="discountText"></span></span>
                            <div>
                                <span id="overall-discount">0.00</span>
                                <span><i class="fa fa-solid fa-edit card-modal" role="button" data-bs-target="#salesDiscountModal"></i></span>
                            </div>
                        </div>

                        <div class="adjustment-container d-flex flex-column">
                            <span>
                                <label for="adjustmentAmtInput" class="form-label mb-0">
                                    <i class="fas fa-info-circle adjustment-input-info-icon" data-toggle="tooltip" data-placement="top"
                                        title="You can adjust the amount between maximum: {{session('posSettings.adjustment_max')}}Tk and minimum: {{session('posSettings.adjustment_min')}}Tk.">
                                    </i>
                                    Adjustment:
                                </label>
                            </span>
                            <div>
                                <input min="{{session('posSettings.adjustment_min')}}" max="{{session('posSettings.adjustment_max')}}" type="number" class="form-control adjustment-input rounded"
                                    id="adjustmentAmtInput" placeholder="0.00">
                            </div>
                        </div>

                        <div class="d-flex flex-column">
                            <span>Total Payable:</span>
                            <span id="total-payable">0.00</span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-1 d-flex gap-2 justify-content-between">
                        <button type="button" class="btn thm-btn-bg thm-btn-text-color flex-fill btn-pay" data-bs-target="#cardPaymentModal">
                            <i class="fas fa-credit-card"></i> Credit Card
                        </button>
                        <button type="button" class="btn thm-btn-bg thm-btn-text-color flex-fill btn-pay" data-bs-target="#walletPaymentModal">
                            <i class="fas fa-mobile-alt"></i> Mobile Wallet
                        </button>
                        <button type="button" class="btn thm-btn-bg thm-btn-text-color flex-fill btn-pay" data-bs-target="#cashPaymentModal">
                            <i class="fas fa-money-bill-wave"></i> Cash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Service Grid and Recent Transactions -->
    <div class="col-md-4">
        <div class="mb-3">
            <div class="row">
                <div class="col mb-2 d-flex justify-content-between gap-1 category-product-service-select">
                    <select id="posSearchCategory" class="form-select flex-fill rounded pos-page-font-size">
                        <option value="0">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                    <select id="posProductService" class="form-select flex-fill rounded  pos-page-font-size">
                        <option selected value="Product">Product</option>
                        <option value="Service">Service</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 d-flex">
                <input type="text" class="form-control me-2  pos-page-font-size rounded" id="posSearchInput" placeholder="Search service by name or code (min. 3 characters)">
                <button id="toggleViewBtn" class="btn btn-sm thm-btn-bg thm-btn-text-color pos-page-font-size" title="Switch View">
                    <i class="fa-solid fa-th-large"></i>
                </button>
            </div>

            <div class="row show" id="collapseService">
                <div class="col" style="overflow-y: auto; overflow-x: hidden; height: 80vh;">
                    <div id="searchServiceContainer" class="grid-view">
                        @foreach($recentServices as $recProd)
                        <div data-stock="{{ $recProd->stock }}" data-toggle="tooltip" data-placement="top" title="{{ $recProd->name }}" class="grid-item recent-service d-flex flex-column align-items-center p-2"
                            style="background-color: #ccc;" data-id="{{ $recProd->id }}">
                            @if(!empty($recProd->image))
                            <img src="{{ asset("images/{$recProd->POSID}/services/{$recProd->image}") }}" class="rounded" style="width: 100px; height: 50px; object-fit: cover;">
                            @else
                            <div class="rounded" style="background-color: #fff; width: 100px; height: 50px;"></div>
                            @endif

                            <p style="text-align: center;" class="m-0 mt-1 pos-page-font-size" title="{{ $recProd->name }}">
                                {{ mb_strimwidth($recProd->name, 0, 35, '...') }}
                            </p>
                            <p style="font-size: 12px;" class="m-0">({{ $recProd->tagline }} | {{$recProd->stock}})</p>
                            <p style="font-size: 12px;" class="m-0">({{ $recProd->code }})</p>
                            <p style="font-size: 12px;" class="m-0">({{ $recProd->price }}) Tk.</p>
                        </div>

                        <div class="list-item recent-service list-group-item list-group-item-action d-none pos-page-font-size" data-id="{{ $recProd->id }}">
                            {{ $recProd->code }} -> {{ $recProd->name }} ({{ $recProd->tagline }} -> {{$recProd->stock}}) -> ({{ $recProd->price }}) Tk
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@php
$js = [
'resources/js/print-receipt-script.js',
'resources/js/setup/customer-script.js',
'resources/js/pos/pos-script.js',
];

if(isFeatureEnabled('ENABLED_LOYALTY')) {
$js[] = 'resources/js/loyalty/loyalty-script.js';
}
@endphp

@vite($js)

<script>
let posUrls = {
    'getStaffs': "{{ route('pos.staffs') }}",
    'searchService': "{{ route('pos.search.service') }}",
    'saveSales': "{{ route('pos.sales.save')}}",
    'searchCustomer': "{{ route('pos.customer.search')}}",
    'getAccountInfo': "{{ route('pos.account.get')}}",
    'publicUrl': "{{ asset('') }}",
};

let customerUrls = {
    'saveCustomer': "{{ route('sales.customer.store') }}",
    'getCustomerInfo': "{{ route('sales.customer.info', ['customer' => 'customerID']) }}",
    'getLastSales': "{{ route('pos.customer.lastSales', ['customerId' => 'customerID']) }}",

};

let loyaltyUrls = {
    'getLoyaltyStatus': "{{ route('pos.customer.loyalty.status', ['customerId' => 'customerID'] ) }}",
    'verifyCard': "{{ route('pos.customer.loyalty.verify') }}",
    'getCardHistory': "{{ route('sales.customer.loyalty.cards.history', ['cardId' => 'cardId']) }}",
};

var hasShowPhonePermission = false;

$.fn.select2.defaults.set("theme", "classic");

let customers = [];
let cashier = {
    id: "{{ auth()->user()->id }}",
    name: "{{ auth()->user()->name }}"
}

const recentServices = @JSON($recentServices);

$(document).ready(function() {

    // Permission status for phone number masking
    hasShowPhonePermission = WinPos.Common.hasPermission('show_phone') ? 'true' : 'false';


    $('[data-toggle="tooltip"]').tooltip({
        html: true
    });

    $('#adjustmentAmtInput').on('change keyup', function() {
        WinPos.Pos.cart.applyAdjustment($(this).val());
    });

    const posSearchCategory = $("#posSearchCategory");
    posSearchCategory.val("0").trigger('change');

    const posProductService = $("#posProductService");
    $('#posProductService').val('Product').trigger('change');

    const posSearchInput = $("#posSearchInput");
    posSearchInput.val("");

    WinPos.Pos.cart.setCashier(cashier);

    $(document).on('click', '.remove-cart-service', function() {
        const prodId = $(this).attr('data-id');
        WinPos.Pos.cart.remove(prodId);
    });

    $(document).on('blur change', '.cart-qty-input', function() {
        const prodId = $(this).attr('data-id');
        const qty = $(this).val();

        WinPos.Pos.cart.updateQuantity(prodId, qty);
    });

    $(document).on('click', '.recent-service', function() {
        const prod = recentServices.find((item) => item.id == $(this).attr('data-id'));

        if (prod) {
            WinPos.Pos.cart.addItem(prod);
        }
    });

    $(document).on('click', '.card-modal', function() {
        // discount modal
        const cartCustomer = WinPos.Pos.cartObj.customer;

        if (cartCustomer == null || cartCustomer.id <= 0) {
            toastr.warning("Please select or add customer first.", "No Customer Selected");
            $('#terminalCustomerName').focus();
            return;
        }

        if (WinPos.Pos.cart.isEmpty()) {
            toastr.warning("Please add item first.", "Empty List");
            return;
        }

        const modalId = $(this).attr('data-bs-target');

        $(modalId).modal('toggle');
    });

    $(document).on('click', '.close-modal', function() {
        $('.input-clear').val('');
    });

    $(document).on('click', '.btn-pay', function() {
        const amount = parseFloat($('#total-payable').text());
        const cartCustomer = WinPos.Pos.cartObj.customer;

        if (cartCustomer == null || cartCustomer.id <= 0) {
            toastr.warning("Please select or add customer first.", "No Customer Selected");
            $('#terminalCustomerName').focus();
            return;
        }

        if (amount <= 0) {
            toastr.warning("Please add item to the list", "Empty List");
            return;
        }

        const modalId = $(this).attr('data-bs-target');

        $('.paid-amount-input').val(amount.toFixed(2));
        $('.paid-amount-show').text(amount.toFixed(2));
        $('.input-clear').val('');

        $(modalId).modal('toggle');
    });

    $(document).on('submit', '.payment-form', function(e) {
        e.preventDefault();
        const formData = WinPos.Common.getFormData('#' + $(this).attr('id'));

        // validate
        // call save
        WinPos.Pos.saveSalesDetails(formData)
            .then((response) => {
                WinPos.Pos.printReceipt(response);
            }).catch((response) => {
                if (response.responseJSON && response.responseJSON.message) {
                    toastr.error(response.responseJSON.message, "Error");
                } else if (response.message) {
                    toastr.error(response.message, "Error");
                } else {
                    toastr.error("Something went wrong.", "Error");
                }
            });

        $('.input-clear').val('');
        $('#' + formData.paymentMethod + 'PaymentModal').modal('toggle');
    });

    $(document).on('keyup', '#posSearchInput', function() {
        runServiceSearch();
    });

    $(document).on('change', '#posSearchCategory', function() {
        runServiceSearch();
    });

    $(document).on('change', '#posProductService', function() {
        runServiceSearch();
    });

    $('#posSearchCategory').select2({
        placeholder: {
            id: '0',
            text: 'All Categories'
        },
        allowClear: true,
        width: '100%',
        theme: 'classic'
    });

    $('#posProductService').select2({
        placeholder: {
            id: '0',
            text: 'Select Type'
        },
        allowClear: true,
        width: '100%',
        theme: 'classic'
    });

    WinPos.Pos.cart.listener(renderCart);

    let typingTimer;
    $('#terminalCustomerPhone').on('input', function() {
        const phone = $(this).val().trim();
        clearTimeout(typingTimer);

        if (phone.length >= 5) {
            typingTimer = setTimeout(() => {
                $.ajax({
                    url: posUrls.searchCustomer,
                    data: {
                        term: phone
                    },
                    success: function(data) {
                        if (data.length > 0) {
                            let html = data.map(item =>
                                `<span style="cursor: pointer;" class="list-group-item list-group-item-action thm-btn-bg text-white customer-option"
                                    data-id="${item.id}"
                                    data-name="${item.name}"
                                    data-phone="${item.phone1}"
                                    data-age-group="${item.age_group || ''}"
                                    data-totalSales="${item.totalSales}"
                                    data-lastVisit="${item.lastVisit}">
                                    ${item.name} - ${item.phone1}
                                </span>`
                            ).join('');
                            $('#terminalCustomerResults').html(html).show();
                        } else {
                            $('#terminalCustomerResults').hide();
                        }
                    }
                });
            }, 300);
        } else {
            $('#terminalCustomerResults').hide();
        }
    });


    $('#terminalCustomerAddBtn').on('click', function(e) {
        const phone = $('#terminalCustomerPhone').val().trim();
        const name = $('#terminalCustomerName').val().trim();
        const ageGroup = $('#terminalCustomerAgeGroup').val();

        if (name.length < 3) {
            toastr.error("Please enter a valid customer name (min 3 characters).");
            $('#terminalCustomerName').focus();
        } else if (phone.length !== 11) {
            toastr.error("Please enter a valid 11-digit phone number.");
            $('#terminalCustomerPhone').focus();
        } else {

            let newCustomer = {
                name: name,
                phone1: phone,
                age_group: ageGroup,
                gender: 'F'
            };

            // check if customer is already selected
            // i think we can only check the phone number 
            let cartCustomer = WinPos.Pos.cartObj.customer;
            if (
                cartCustomer &&
                cartCustomer.name.trim().toLowerCase() === name.trim().toLowerCase() &&
                cartCustomer.phone1.trim() === phone.trim() &&
                cartCustomer.id != '-1'
            ) {
                toastr.info("Customer is already selected.");
                return;
            }

            WinPos.Customer.saveCustomer(newCustomer, function(customer) {
                if (customer && customer.id) {

                    WinPos.Pos.cart.setCustomer({
                        id: customer.id,
                        name: customer.name,
                        phone1: customer.phone1,
                        age_group: customer.age_group,
                    });

                    if (WinPos.Common.isFeatureEnabled('ENABLED_LOYALTY')) {
                        WinPos.Pos.customer.setCustomerLoyaltyCard(
                            {
                                id: '',
                                card_number: '',
                                status: '',
                                verifyCard: false,
                                skipLoyalty: false,
                                skipLoyaltyReason: ''
                            }
                        );
                    }

                    WinPos.Pos.customer.setTerminalCustomerForm(customer);

                    if (WinPos.Common.isFeatureEnabled('ENABLED_LOYALTY')) {
                        WinPos.Loyalty.setNewCustomerLoyaltyInfo();
                    }

                    $('#terminalCustomerResults').hide();
                }
            });
        } // end save customer
    }); // end add button click

    $('#terminalCustomerClearBtn').on('click', function(e) {
        WinPos.Pos.customer.clearTerminalCustomerForm();
        WinPos.Pos.cart.setCustomer({
            id: '-1',
            name: '',
            phone1: '',
            age_group: ''
        });

        if (WinPos.Common.isFeatureEnabled('ENABLED_LOYALTY')) {
            // set loyalty card to empty
            WinPos.Pos.customer.setCustomerLoyaltyCard({
                id: '',
                card_number: '',
                status: '',
                verifyCard: false,
                skipLoyalty: false,
                skipLoyaltyReason: ''
            });
        }

        $('#terminalCustomerResults').hide();
    });

    // no need to check loyalty sitefeature
    $('#skipLoyaltyVerification').change(function() {
        if ($(this).is(':checked')) {
            $('#skipReasonDiv').slideDown();
            $('#verifyLoyaltyCardNumber').val('');
            $('#verifyLoyaltyCardNumber').prop('disabled', true);
            $("#btnVerifyLoyaltyCard").html('<i class="fa-solid fa-check"></i> Skip');
        } else {
            $('#skipReasonDiv').slideUp();
            $("#skipReason").val('');
            $('#verifyLoyaltyCardNumber').prop('disabled', false);
            $("#btnVerifyLoyaltyCard").html('<i class="fa-solid fa-check"></i> Verify');
        }
    });

    if (WinPos.Common.isFeatureEnabled('ENABLED_LOYALTY')) {
        // show loyalty rules info in discount modal
        $('#salesDiscountModal').on('show.bs.modal', function() {
            const isVerified = WinPos.Pos.cartObj.loyaltyCard.verifyCard === true;
            if (!isVerified) {
                $('#discountModelLoyaltyRulesInfo').hide();
                $('#loyaltyDiscountBtnText').addClass('d-none');
            } else {
                $('#discountModelLoyaltyRulesInfo').show();
                $('#loyaltyDiscountBtnText').removeClass('d-none');
            }
        });
    } // end

}); // end jquery


$(document).on('click', '.customer-option', function(e) {
    const customer = {
        id: $(this).data('id'),
        name: $(this).data('name'),
        phone1: $(this).data('phone'),
        age_group: $(this).data('age-group'),
        totalSales: $(this).data('totalsales'),
        lastVisit: $(this).data('lastvisit')
    };

    WinPos.Pos.cart.setCustomer({
        id: customer.id,
        name: customer.name,
        phone1: customer.phone1,
        age_group: customer.age_group
    });

    WinPos.Pos.customer.setTerminalCustomerForm(customer);
    $('#posCustomerInfoModalBtn').prop('disabled', false);
    $('#posLastSalesHistoryModalBtn').prop('disabled', false);

    if (WinPos.Common.isFeatureEnabled('ENABLED_LOYALTY')) {
        WinPos.Loyalty.getLoyaltyStatus(customer.id);
    }

    $('#terminalCustomerResults').hide();
})

// Handle Customer Info icon click in POS
$(document).on('click', '#posCustomerInfoModalBtn', function() {
    let customerId = WinPos.Pos.cartObj.customer.id;
    if (customerId && customerId !== '-1') {
        WinPos.Customer.showCustomerInfo(customerId);
    } else {
        toastr.warning("No customer selected.");
    }
});

// Handle Last Sales History icon click in POS
$(document).on('click', '#posLastSalesHistoryModalBtn', function() {
    let customerId = WinPos.Pos.cartObj.customer.id;
    if (customerId && customerId !== '-1' && customerId > 0) {
        WinPos.Customer.showLastSalesHistory(customerId);
    } else {
        toastr.warning("No customer selected.");
    }
});

function renderCart(cart) {
    const dom = [];
    cart.items.forEach((item) => {

        dom.push('<tr>');

        if(item.type == 'Service') {
            const staffName = item.staff_name || '---------';
            const staffDisplay = '<span class="text-muted assigned-staff" data-toggle="tooltip" data-placement="top" title="Click to Change" data-item-id="' + item.id + '" style="cursor: pointer;"> <i class="fa-solid fa-hand-holding-heart"></i> Staff: ' +
            staffName + ' <button class="staff-change-button"><i class="fa-solid fa-pen-to-square"></i></button></span>';
            dom.push('<td class="selected-service-name" style="width: 50%;">' + item.code + ' - ' + item.name + 
                ' <br> ' + staffDisplay + '</td>');
            dom.push('<td style="width: 10%; vertical-align: middle" class="text-center"><input type="number" class="form-control cart-qty-input pos-page-font-size" value="' + item.quantity + '" min="1" data-id="' + item.id + '"></td>');
            
        }else{
            dom.push('<td class="selected-service-name" style="width: 50%;">' + item.code + ' - ' + item.name + ' (' + item.tagline + ')</td>');
            dom.push('<td style="width: 10%; vertical-align: middle" class="text-center"><input max="' + item.stock + '" type="number" class="form-control cart-qty-input pos-page-font-size" value="' + item.quantity + '" min="1" data-id="' + item.id + '"></td>');
        }
        
        dom.push('<td style="width: 15%; vertical-align: middle" class="text-end">' + item.price.toFixed(2) + ' Tk.</td>');
        dom.push('<td style="width: 15%; vertical-align: middle" class="text-end">' + ((item.price) * item.quantity).toFixed(2) + ' Tk.</td>');
        dom.push('<td style="width: 10%; vertical-align: middle" class="text-center"><button type="button" class="btn thm-btn-bg thm-btn-text-color btn-sm remove-cart-service pos-page-font-size" data-id="' +
            item.id + '"><i class="fa fa-solid fa-times"></i></button></td>')
        dom.push('</tr>');
    });

    $('#service-table tbody').html(dom.join(''));

    const totalAmount = cart.total;
    const discountAmount = (cart.discountType == 'fixed') ? cart.discount : (totalAmount * cart.discount) / 100;
    const totalAfterDiscount = totalAmount - discountAmount;
    const totalAfterAdjustment = totalAfterDiscount + cart.adjustment;

    const discountText = cart.discountType == 'fixed' ? 'Fixed' : cart.discount.toFixed(2) + '%';

    $('#discountText').text(discountText);
    $('#total-items').text(cart.items.length);
    $('#total-price').text(cart.total.toFixed(2) + ' Tk.');
    $('#overall-discount').text(discountAmount.toFixed(2) + ' Tk.');
    $('#total-payable').text(totalAfterAdjustment.toFixed(2) + ' Tk.');

    const sign = cart.adjustment >= 0 ? '+' : '-';
    const absAdj = Math.abs(cart.adjustment);

    const tooltipText = `
        Total: ${totalAmount}Tk.<br>
        Discount: ${discountAmount}Tk. (${discountText})<br>
        After Discount: ${totalAmount} - ${discountAmount} = ${totalAfterDiscount}Tk.<br>
        Adjustment: ${sign}${absAdj}Tk.<br>
        After Adjustment: ${totalAfterDiscount} ${sign} ${absAdj} = ${totalAfterAdjustment}Tk.<br>
        <strong>Final Total Payable: ${totalAfterAdjustment}Tk.</strong>
    `;

    const $el = $('#total-payable');
    $el.removeAttr('title')
        .removeAttr('data-original-title');
    $el.tooltip('dispose');
    $el.attr('title', tooltipText)
        .attr('data-original-title', tooltipText);
    $el.tooltip({
        html: true
    });
}

function runServiceSearch() {
    let searchInput = $('#posSearchInput').val();
    let searchCategory = $('#posSearchCategory').val();
    let searchProductOrService = $('#posProductService').val();

    if (searchInput.length == 1 || searchInput.length == 2) {
        return;
    }

    WinPos.Pos.searchService(searchInput, searchCategory, searchProductOrService)
        .then((searchResult) => {
            WinPos.Pos.RenderSearchService(searchResult);
        })
        .catch((error) => {
            console.log(error);
        });
}

$(document).on('input change', '#adjustmentAmtInput', function() {
    let min = parseFloat($(this).attr('min'));
    let max = parseFloat($(this).attr('max'));
    let val = parseFloat($(this).val());

    if (isNaN(val)) return;

    if (val < min) {
        $(this).val(min);
    } else if (val > max) {
        $(this).val(max);
    }
});


$(document).on('click', '#toggleViewBtn', function() {
    let container = $('#searchServiceContainer');
    let icon = $(this).find('i');

    if (container.hasClass('grid-view')) {
        container.removeClass('grid-view').addClass('list-view');
        icon.removeClass('fa-th-large').addClass('fa-list');
    } else {
        container.removeClass('list-view').addClass('grid-view');
        icon.removeClass('fa-list').addClass('fa-th-large');
    }
});

// no need to check loyalty sitefeature
$(document).on('click', '#btnVerifyLoyaltyCard', function() {
    const customerId = WinPos.Pos.cartObj.customer.id;
    const cardNumber = $('#verifyLoyaltyCardNumber').val();
    const skipVerification = $('#skipLoyaltyVerification').is(':checked');
    const skipReason = $('#skipReason').val().trim();

    if (skipVerification) {
        if (skipReason.length < 3) {
            toastr.warning("Please enter a valid reason.", "Input Required");
            $('#skipReason').focus();
            return;
        }
        const cardId = $("#hiddenTerminalCustomerCardId").val();
        WinPos.Pos.customer.setCustomerLoyaltyCard({
            id: cardId, // set the valid card id so that we can store skip reason in history table. It will store the reasone under the latest card.
            card_number: '',
            status: '',
            verifyCard: false,
            skipLoyalty: true,
            skipLoyaltyReason: skipReason
        });

        $('#loyaltyVerificationModal').modal('hide');
        return;
    }

    // Regular validation for card number
    if (!cardNumber || cardNumber.trim() === '' || cardNumber.length < 11 || cardNumber.length > 20) {
        toastr.warning("Please enter a valid loyalty card number.", "Input Required");
        $('#verifyLoyaltyCardNumber').focus();
        return;
    }
    WinPos.Loyalty.verifyLoyaltyCardAndGetHistory(cardNumber, customerId);
});

// no need to check loyalty sitefeature
$(document).on('click', '#posLoyaltyHistoryModalBtn', function() {
    const cardId = $("#hiddenTerminalCustomerCardId").val();
    if (!cardId) {
        toastr.warning("Please verify the loyalty card first.", "No Loyalty Card");
        return;
    } else {
        WinPos.Loyalty.showLoyaltyHistoryModal(cardId);
    }
});

// no need to check loyalty sitefeature
$(document).on('click', '#posLoyaltyRulesModalBtn', function() {
    $('#loyaltyRulesModal').modal('show');
});

// Staff assignment handlers
let currentCartItemId = null;

$(document).on('click', '.assigned-staff, .staff-change-button', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const itemId = $(this).closest('.assigned-staff').data('item-id') || $(this).closest('tr').find('.assigned-staff').data('item-id');
    if (!itemId) return;
    
    currentCartItemId = itemId;
    const cartItem = WinPos.Pos.cartObj.items.find(item => item.id == itemId);
    
    if (!cartItem) return;
    
    $('#staffModalServiceName').text(cartItem.code + ' - ' + cartItem.name);
    
    // Load staffs
    WinPos.Common.getAjaxCall(posUrls.getStaffs, function(response) {
        if (response.status === 'success') {
            WinPos.Pos.renderStaffCards(response.staffs, cartItem.staff_id);
            $('#staffAssignModal').modal('show');
        }
    });
});


$(document).on('click', '.staff-card', function() {

    if ($(this).hasClass('disabled')) return;

    const staffId = $(this).data('staff-id');
    const staffName = $(this).data('staff-name');
    
    if (!currentCartItemId) return;

    let isAssigned = WinPos.Pos.cart.updateStaff(currentCartItemId, staffId, staffName);
    if(isAssigned) {
        $('#staffAssignModal').modal('hide');
        currentCartItemId = null;
        toastr.success('Staff '+ staffName +' assigned successfully.', 'Success');
    }else{
        toastr.error('Failed to assign staff.', 'Error');
    }
});
</script>
@endsection