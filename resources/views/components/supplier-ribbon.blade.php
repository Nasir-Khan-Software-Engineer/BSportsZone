<div class="card border mb-1" id="supplierRibbon">
    <div class="card-body d-flex flex-wrap justify-content-between p-2">
        <!-- Section 1: Supplier Info -->
        <div class="supplier-section mr-4">
            <p class="mb-1"><strong>Name:</strong> {{ $supplier['name'] ?? '-' }}</p>
            <p class="mb-1"><strong>Phone:</strong> 
                @if(hasAccess('show_phone'))
                    {{ $supplier['phone'] ?? '-' }}
                @else
                    {{ maskPhoneNumber($supplier['phone'] ?? '-') }}
                @endif
            </p>
            <p class="mb-1"><strong>Email:</strong> {{ $supplier['email'] ?? '-' }}</p>
        </div>

        <!-- Section 2: Address Info -->
        <div class="supplier-section mr-4">
            <p class="mb-1"><strong>Address:</strong> {{ $supplier['address'] ?? '-' }}</p>
            <p class="mb-1"><strong>City:</strong> {{ $supplier['city'] ?? '-' }}</p>
            <p class="mb-1"><strong>Country:</strong> {{ $supplier['country'] ?? '-' }}</p>
        </div>
    </div>
</div>

