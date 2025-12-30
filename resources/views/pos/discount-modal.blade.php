<!-- Discount Modal -->
<div class="modal fade" id="salesDiscountModal" tabindex="-1" aria-labelledby="salesDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">

            <div class="modal-header rounded">
                <h5 class="modal-title" id="salesDiscountModalLabel">Discount</h5>
                <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Loyalty Rules / Info Line --}}
                @if(isFeatureEnabled('ENABLED_LOYALTY') && session()->has('loyaltySettings'))
                @php
                $ls = session('loyaltySettings');
                @endphp

                <div class="alert alert-info small" id="discountModelLoyaltyRulesInfo">
                    <strong>Loyalty Rules:</strong>
                    @if(!empty($ls['rules_text']))
                    <div class="mt-1">{!! $ls['rules_text'] !!}</div>
                    @endif
                </div>


                @endif

                {{-- Discount Section --}}
                <div class="row">
                    <div class="col mb-3">
                        <div class="mb-3">
                            <label for="discountType" class="form-label">Discount Type
                                <span class="text-danger required-star">*</span>
                            </label>
                            <select class="form-select rounded" id="discountType" name="discountType">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed</option>
                            </select>
                        </div>
                    </div>

                    <div class="col mb-3">
                        <div class="mb-3">
                            <label for="discountAmount" class="form-label" id="discountAmountLabel">
                                Discount <span class="text-danger required-star">*</span>
                            </label>
                            <input type="number" class="form-control input-clear rounded" id="discountAmount" name="discountAmount" min="0" placeholder="0.00" step="0.01">
                        </div>
                    </div>
                </div>

            </div>


            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color close-modal rounded" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" class="btn thm-btn-bg thm-btn-text-color close-modal rounded" data-bs-dismiss="modal" onClick="WinPos.Pos.cart.applyDiscount($('#discountType').val(), $('#discountAmount').val())">
                    <i class="fa-solid fa-check"></i> Apply <span class="d-none" id="loyaltyDiscountBtnText">Loyalty Discount</span>
                </button>
            </div>

        </div>
    </div>
</div>