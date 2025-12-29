<!-- Card Payment Modal -->
<div class="modal fade" id="cardPaymentModal" tabindex="-1" aria-labelledby="cardPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded">

            <div class="modal-header rounded">
                <h5 class="modal-title" id="cardPaymentModalLabel">Card Payment - <span class="paid-amount-show"></span> Tk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="cardPaymentForm" class="payment-form">
                    <input type="hidden" name="paymentMethod" value="card">

                    <div class="mb-3" id="cardTypeGroup">
                        <label for="cardType" class="form-label">Card Type <span class="text-danger required-star">*</span></label>
                        <select required class="form-select rounded" id="cardType" name="cardType">
                            <option value="">Select Card Type</option>
                            <option value="visa">Visa</option>
                            <option value="mastercard">MasterCard</option>
                            <option value="amex">American Express</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="transactionId" class="form-label">Transaction ID <span class="text-danger required-star">*</span></label>
                        <input type="text" class="form-control rounded" id="transactionId" name="transactionId" required>
                    </div>

                    <!-- Paid Amount -->
                    <div class="mb-3">
                        <label for="paymentNote" class="form-label">Note</label>
                        <textarea name="paymentNote" id="paymentNote" class="form-control rounded" rows="3" placeholder="Note"></textarea>
                        <input type="hidden" class="form-control paid-amount-input rounded" id="paidAmount" name="paidAmount" min="0" readonly required>
                    </div>

                </form>
            </div>

            <div class="modal-footer d-flex">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" style="margin-right: auto;" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" form="cardPaymentForm"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </div>

        </div>
    </div>
</div>
