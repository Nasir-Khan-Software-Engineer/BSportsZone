<!-- Discount Modal -->
<div class="modal fade" id="loyaltyRulesModal" tabindex="-1" aria-labelledby="loyaltyRulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">

            <div class="modal-header rounded">
                <h5 class="modal-title" id="loyaltyRulesModalLabel">Loyalty Rules</h5>
                <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Loyalty Rules / Info Line --}}
                @if(isFeatureEnabled('ENABLED_LOYALTY') && session()->has('loyaltySettings'))
                @php
                $ls = session('loyaltySettings');
                @endphp

                <div class="alert alert-info small" id="loyaltyRulesInfo">
                    <strong>Loyalty Rules:</strong>
                    @if(!empty($ls['rules_text']))
                    <div class="mt-1">{!! $ls['rules_text'] !!}</div>
                    @endif
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color close-modal rounded" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
            </div>
        </div>
    </div>
</div>