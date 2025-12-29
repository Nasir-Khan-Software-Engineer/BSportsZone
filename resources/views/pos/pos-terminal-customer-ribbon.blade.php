 <div class="card border mb-1 d-none" id="terminalCustomerRibbon">
     <div class="card-body d-flex flex-wrap justify-content-between p-1">
         <!-- Section 1: Customer Info -->
         <div class="mr-4">
             <p class="mb-1"><strong title="Customer Name">(N):</strong> <span id="terminalCustomerNameShow"></span></p>
             <p class="mb-1"><strong title="Customer Phone">(P):</strong> <span id="terminalCustomerPhoneShow"></span></p>
             <p class="mb-1"><strong title="Customer Age Group">(A):</strong> <span id="terminalCustomerAgeGroupShow"></span></p>
         </div>
         @if(isFeatureEnabled('ENABLED_LOYALTY'))
         <!-- Section 2: Loyalty Card -->
         <div class="mr-4">
             <input type="hidden" id="hiddenTerminalCustomerCardId">
             <div id="terminalCustomerLoyaltyInfo" data-minimum-purchase-amount="{{ session('loyaltySettings.minimum_purchase_amount') }}"
                 data-minimum-purchase-amount-sales="{{ session('loyaltySettings.minimum_purchase_amount_applies_for') }}" data-max-visit="{{ session('loyaltySettings.max_visits') }}">
                 <p class="mb-1 d-none terminal-customer-loyalty-info all-status-info"><strong title="Loyalty Status of Customer">Loyalty:</strong> <span id="terminalCustomerLoyaltyShow">No
                         Card</span></p>

                 <p class="mb-1 d-none terminal-customer-loyalty-info loyal-status-info limitted-status-info completed-status-info expired-status-info"><strong
                         title="Total Visit of Customer under loyalty">Visits:</strong> <span id="terminalCustomerVisitShow">0</span></p>
                 @if(session('loyaltySettings.minimum_purchase_amount_applies_for') == 'Single')
                 <p class="mb-1 d-none terminal-customer-loyalty-info new-customer-info single-condition"><strong title="Current maximum sales amount">Max Sales:</strong> <span
                         id="terminalCustomerMaxSalesShow">0</span></p>
                 @else
                 <p class="mb-1 d-none terminal-customer-loyalty-info new-customer-info all-condition"><strong title="Current total sales amount">Total Spent:</strong> <span
                         id="terminalCustomerTotalSpentShow">0</span></p>
                 @endif

                 <p class="mb-1 d-none terminal-customer-loyalty-info loyal-status-info limitted-status-info"><strong title="Card expiry date">Valid Until:</strong> <span
                         id="terminalCustomerValidUntilShow">-</span></p>
                 <p class="mb-1 d-none terminal-customer-loyalty-info new-customer-info completed-status-info expired-status-info"><strong title="Is the customer eligible for a new card?">New
                         Card:</strong>
                     <span id="terminalCustomerNextCardShow"><span>Not Eligible</span>
                         <i data-toggle="tooltip" data-placement="top"
                             title="New card requires a {{ session('loyaltySettings.minimum_purchase_amount') }} BDT minimum purchase in {{ session('loyaltySettings.minimum_purchase_amount_applies_for') }} visit."
                             class="fas fa-info-circle">
                         </i>
                     </span>
                 </p>
             </div>
         </div>
         @else
         <div class="mr-4">
            <div id="terminalCustomerLoyaltyInfo">
                 <p class="mb-1"><strong title="Loyalty Status of Customer">Loyalty:</strong> Not Enabled</p>
                 <p class="mb-1"><strong title="Total Visit of Customer under loyalty">Visits:</strong>-</p>
                 <p class="mb-1"><strong title="Card expiry date">Valid Until:</strong>-</p>
             </div>
         </div>
         @endif

         <!-- Section 3: Sales Info -->
         <div class="">
             <p class="mb-1"><strong title="How many times the customer taken service">Total Sales:</strong> <span id="terminalCustomerTotalNumberOfSalesShow">0</span></p>
             <p class="mb-1"><strong title="Last visit Date of customer">Last Visit:</strong> <span id="terminalCustomerLastVisitShow">Today</span></p>
             <p class="mb-1">
                 <button id="terminalCustomerClearBtn" type="button" class="btn thm-btn-bg thm-btn-text-color btn-sm d-none  w-100" data-toggle="tooltip" data-placement="top" title="Clear Customer">
                     <i class="fa fa-solid fa-times"></i> Clear Customer
                 </button>
             </p>
         </div>
     </div>
 </div>