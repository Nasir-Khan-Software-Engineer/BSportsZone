<div class="modal fade" id="walletPaymentModal" tabindex="-1" aria-labelledby="walletPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded">

            <div class="modal-header rounded">
                <h5 class="modal-title" id="walletPaymentModalLabel">Wallet Payment - <span class="paid-amount-show"></span> Tk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="walletPaymentForm" class="payment-form">
                    <input type="hidden" name="paymentMethod" value="wallet">

                    <div class="mb-3" id="mobileBankingGroup">
                        <label for="mobileBankingType" class="form-label">Wallet Provider <span class="text-danger required-star">*</span></label>
                        <select required class="form-select rounded" id="mobileBankingType" name="mobileBankingType">
                            <option value="">Select Provider</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
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
                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" form="walletPaymentForm"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </div>

        </div>
    </div>
</div>