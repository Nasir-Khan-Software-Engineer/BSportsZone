<div class="card border mb-1" id="customerRibbon">
    <div class="card-body d-flex flex-wrap justify-content-between p-2">
        <!-- Section 1: Customer Info -->
        <div class="customer-section mr-4">
            <p class="mb-1"><strong>(N):</strong> {{ $customer['name'] ?? '-' }}</p>
            <p class="mb-1"><strong>(P):</strong> 
                @if(hasAccess('show_phone'))
                    {{ $customer['phone'] ?? '-' }}
                @else
                    {{ maskPhoneNumber($customer['phone'] ?? '-') }}
                @endif
            </p>
            <p class="mb-1"><strong>(A):</strong> {{ $customer['age_group'] ?? '-' }}</p>
        </div>
        @if(isFeatureEnabled('ENABLED_LOYALTY'))
        <!-- Section 2: Loyalty Card -->
        <div class="customer-section mr-4">
            @if($customer['status'] == 'Loyal')
                <p class="mb-1"><strong>Loyalty:</strong> {{ $customer['status'] }}</p>
                <p class="mb-1"><strong>Visits:</strong> {{ $customer['visitCount'] ?? '0' }} / {{ $customer['settings']['max_visits'] }}</p>
                <p class="mb-1"><strong>Valid Until:</strong> {{ $customer['cardStatus']['valid_until'] ?? '-' }}</p>
            @elseif($customer['status'] == 'Limited')
                <p class="mb-1"><strong>Loyalty:</strong> {{ $customer['status'] }} (Loyal)</p>
                <p class="mb-1"><strong>Visits:</strong> {{ $customer['visitCount'] ?? '0' }} / {{ $customer['settings']['max_visits'] }} (Used Today)</p>
                <p class="mb-1"><strong>Valid Until:</strong> {{ $customer['cardStatus']['valid_until'] ?? '-' }}</p>
            @else
                <p class="mb-1"><strong>Loyalty:</strong> {{ $customer['status'] }}</p>
                @if($customer['settings']['minimum_sales_amount_applies_for'] == 'Single')
                    <p class="mb-1"><strong>Max Sales:</strong> {{ $customer['currentTotalSpent'] }} Tk / {{ $customer['settings']['minimum_sales_amount'] }} Tk</p>
                    <p class="mb-1"><strong>New Card:</strong> @if($customer['isEligibleForNewCard']) Eligible For New Card @else Not Eligible For New Card @endif</p>
                @else
                    <p class="mb-1"><strong>Total Spent:</strong> {{ $customer['currentTotalSpent'] ?? '-' }} Tk / {{ $customer['settings']['minimum_sales_amount'] }} Tk</p>
                    <p class="mb-1"><strong>New Card:</strong> @if($customer['isEligibleForNewCard']) Eligible For New Card @else Not Eligible For New Card @endif </p>
                @endif
            @endif
        </div>
        @else
            <div class="customer-section mr-4">
                <p class="mb-1"><strong>Loyalty:</strong> Not Enabled</p>
                <p class="mb-1"><strong>Visits:</strong>-</p>
                <p class="mb-1"><strong>Valid Until:</strong>-</p>
            </div>
        @endif
        <!-- Section 3: Sales Info -->
        <div class="customer-section">
            <p class="mb-1"><strong>Total Sales:</strong> {{ $customer['total_sales'] ?? 0 }}</p>
            <p class="mb-1"><strong>Total Service:</strong> {{ $customer['total_service'] ?? 0 }}</p>
            <p class="mb-1"><strong>Total Paid Amount:</strong> TK.{{ $customer['total_paid'] ?? 0 }}</p>
        </div>
    </div>
</div>
