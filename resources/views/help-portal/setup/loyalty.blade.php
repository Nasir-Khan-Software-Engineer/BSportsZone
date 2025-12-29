@extends('help-portal.layouts.help-layout')

@section('style')
    <style>
        img{
            border: 1px solid #5e1d66;
            border-radius: 5px;
            box-sizing: border-box;
            padding: 5px;
        }
        .card-body{
            position: relative;
        }
        .sticky-card-header {
            position: sticky;
            top: 0;
            background: #ffffffff; 
            padding:10px 0px;
            border-bottom: 1px solid #dee2e6;
            z-index: 10;
        }
    </style>
@endsection

@section('content')

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="card shadow custom-verflow-scroll-y">
            <div class="card-body py-0">
                <div class="sticky-card-header d-flex justify-content-between align-items-center mb-4">
                    <h3 class="card-title mb-0">Loyalty Program</h3>

                    <div>
                        <button class="btn thm-outline-btn thm-active-btn btn-sm">English</button>
                        <button class="btn thm-outline-btn btn-sm">Bangla</button>
                    </div>
                </div>

                <!-- Section 1 -->
                <h5>1. How the System Manages the Loyalty Program</h5>
                <ul>
                    <li>Automatically identifies loyal customers based on rules set in the system.</li>
                    <li>The Customer Table shows customer type: <strong>Loyal</strong> or <strong>General</strong>.</li>
                    <li>The Customer Details Page displays loyalty information.</li>
                    <li>Shows eligibility for a new loyalty card.</li>
                    <li>Shows current loyalty card status: Loyal (Active), Limited (reached daily limit), Completed (reached full limit), Expired (past validity).</li>
                    <li>The Loyalty Details Page shows full card information and transaction history.</li>
                </ul>
                <hr>

                <!-- Section 2 -->
                <h5>2. POS Page Features</h5>
                <ul>
                    <li>Displays whether the customer is Loyal or General.</li>
                    <li>Shows eligibility for a loyalty card.</li>
                    <li>Shows card status: Expired, Limited, Completed.</li>
                    <li>Allows checking of customer loyalty history.</li>
                    <li>Shows business loyalty rules.</li>
                    <li>Enables verification of loyalty cards.</li>
                </ul>
                <hr>

                <!-- Section 3 -->
                <h5>3. Loyalty Permissions</h5>
                <ul>
                    <li>Access to view loyalty details, add new cards, or edit cards can be controlled via user permissions.</li>
                </ul>
                <hr>

                <!-- Section 4 -->
                <h5>4. Loyalty Feature Control</h5>
                <ul>
                    <li>The loyalty program can be enabled or disabled at any time from system settings.</li>
                </ul>

                <h4 class="mt-4">Set Up Loyalty Rules</h4>

                <p>
                    Every business may have different loyalty policies. So, in our system, you can set your own loyalty rules from
                    <strong>Account Setup → Loyalty Setup</strong>.
                </p>

                <p><strong>You will be able to configure the following:</strong></p>

                <div class="mt-3">
                    <h6 class="font-weight-bold">Minimum Amount of Transaction</h6>
                    <p>The minimum total amount a customer must spend to receive a loyalty card. You can choose whether this amount is:</p>
                    <ul class="mb-3">
                        <li><strong>From one single transaction</strong> — Example: If a customer spends 10,000 TK in one time, they get a loyalty card.</li>
                        <li><strong>From the total of all transactions</strong> — Example: If a customer reaches 10,000 TK across multiple visits, they get a loyalty card.</li>
                    </ul>
                </div>

                <div class="mt-3">
                    <h6 class="font-weight-bold">Default Maximum Validity (Months)</h6>
                    <p>Set how long a loyalty card remains valid. For example, you may set the card to be valid for 12 months from the issue date.</p>
                </div>

                <div class="mt-3">
                    <h6 class="font-weight-bold">Maximum Visits</h6>
                    <p>The total number of times the loyalty card can be used.</p>
                </div>

                <div class="mt-3">
                    <h6 class="font-weight-bold">Per-Day Maximum Visit</h6>
                    <p>The maximum number of times a loyalty card can be used in a single day.</p>
                </div>

                <div class="mt-3">
                    <h6 class="font-weight-bold">Loyalty Rules</h6>
                    <p>These are the discount rules or instructions that the salesperson must follow while providing loyalty benefits.</p>
                </div>
                
                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/setup-loyalty.jpg') }}" alt="Setup Loyalty">

                <hr>

                <h4>Customer Table</h4>
                <p>After enabling the Loyalty Program, customers will be divided into two types:</p>
                <ul>
                    <li>General Customer</li>
                    <li>Loyal Customer</li>
                </ul>
                <p>In the Customer Table, a new column will display the Customer Type. This helps you quickly see which customers are loyal and which are general.</p>

                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/customer-table.jpg')}}" alt="Customer Table">

                <hr>

                <h4>Customer Details Page</h4>
                <p>On the Customer Details Page, loyalty information will be shown clearly at the top.</p>
                <ul>
                    <li>Whether the customer is loyal or general</li>
                    <li>Whether the customer is eligible for a new card or not</li>
                    <li>The reason why the customer is eligible or not eligible</li>
                </ul>
                <p>There will be a button called <strong>“View Loyalty Details”</strong> which will take you to the Loyalty Details Page.</p>

                <h5>Loyalty Details Page</h5>
                <ul>
                    <li>View all loyalty cards the customer has received in their lifetime</li>
                    <li>Check the card status (active, expired, completed, etc.)</li>
                    <li>View full usage history of the card</li>
                    <li>See which services used the card and what discount was applied</li>
                </ul>
                <p>Since a customer may receive multiple cards over time, all card records and transaction histories will be visible.</p>

                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/customer-details.jpg')}}" alt="Customer ">

                <hr>

                <h4>Loyalty Status</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Meaning</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Loyal</td>
                            <td>The card is active and can be used</td>
                        </tr>
                        <tr>
                            <td>Limited</td>
                            <td>The customer has reached the daily limit (can't use again today)</td>
                        </tr>
                        <tr>
                            <td>Completed</td>
                            <td>The customer has used the card for the maximum number of total visits</td>
                        </tr>
                        <tr>
                            <td>Expired</td>
                            <td>The card’s validity date has passed</td>
                        </tr>
                    </tbody>
                </table>

                <p>If a card is not Limited, Completed, or Expired, then it is considered <strong>Active / Loyal</strong>.</p>

                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/loyalty-details.jpg')}}" alt="Loyalty Status">

                <hr>

                <h4>POS Page</h4>
                <p>On the POS page, the system will automatically display loyalty-related information:</p>
                <ul>
                    <li>Whether the customer is Loyal or General</li>
                    <li>Whether the customer is eligible for a loyalty card</li>
                    <li>Whether the card is Expired, Limited, or Completed</li>
                </ul>

                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/pos-page-status.jpg')}}" alt="POS Page">

                <p>You will also be able to:</p>
                <ul>
                    <li>View loyalty history (usage transactions)</li>
                    <li>View loyalty discount rules</li>
                    <li>Verify the loyalty card when applying discounts</li>
                </ul>

                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/loyalty-history-modal.jpg')}}" alt="Loyalty History">

                <h6>When a loyal customer takes service:</h6>
                <ul>
                    <li>The system will show a Card Verification Popup.</li>
                    <li>After verification, the salesperson can apply the discount according to the loyalty rules.</li>
                    <li>The salesperson will be able to review the loyalty history, loyalty rules, and the card status.</li>
                    <li>The discount modal will also show all loyalty rules.</li>
                    <li>The system will automatically track every loyalty discount.</li>
                </ul>

                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/loyalty-verification-modal.jpg')}}" alt="Loyalty Verify Modal">

                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/loyalty-discount-modal.jpg')}}" alt="Loyalty Discount Modal">

                <p>If the customer does not want to use the loyalty card or forgot to bring the card, the salesperson can simply choose to Skip. The system will record the reason for future
                    monitoring.</p>

                <hr>

                <h4>Loyalty Permissions</h4>
                <p>Like other modules, the loyalty feature is controlled by user permissions.</p>
                <ul>
                    <li>View Loyalty Details</li>
                    <li>Add New Loyalty Card</li>
                    <li>Edit Existing Loyalty Card</li>
                </ul>
                <p>Only authorized users will be able to perform these actions.</p>

                <img class="w-100 my-2" src="{{asset('images/help-portal/setup/loyalty/loyalty-permission.jpg')}}" alt="Loyalty Permissions">

                <hr>

                <h4>Enable / Disable Loyalty Feature</h4>
                <p>You can enable or disable the Loyalty Program anytime from system settings. When disabled, all loyalty-related pages, buttons, and prompts will be automatically hidden.</p>

            </div>
        </div>
    </div>
</div>


@endsection

@section('script')

<script>

</script>
@endsection