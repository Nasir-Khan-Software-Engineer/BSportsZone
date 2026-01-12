<!-- Individual Discount Modal -->
<div class="modal fade" id="individualDiscountModal" tabindex="-1" aria-labelledby="individualDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="individualDiscountModalLabel">Individual Discount - <span id="individualDiscountItemName"></span></h5>
                <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <!-- // hidden product id 
                // hiddle variation id -->
                
                <input type="hidden" id="individualDiscountProductId">
                <input type="hidden" id="individualDiscountVariationId">
                <input type="hidden" id="individualDiscountItemType">

                <!-- Discount Type -->
                <div class="mb-3">
                    <label class="form-label"><strong>Discount Type</strong></label>
                    <select class="form-select" id="individualDiscountType">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>

                <!-- Discount Value -->
                <div class="mb-3">
                    <label class="form-label"><strong>Discount Value</strong></label>
                    <input type="number" id="individualDiscountValue" class="form-control" placeholder="Enter discount value" min="0">
                </div>

                <small class="text-muted">
                    Percentage = % based discount, Fixed = flat amount.
                </small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded btn-sm" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </button>

                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="applyIndividualDiscountBtn">
                    <i class="fa-solid fa-check"></i> Apply Discount
                </button>
            </div>
        </div>
    </div>
</div>
