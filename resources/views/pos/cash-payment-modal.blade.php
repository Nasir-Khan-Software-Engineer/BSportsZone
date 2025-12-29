<!-- Cash Payment Modal -->
<div class="modal fade" id="cashPaymentModal" tabindex="-1" aria-labelledby="cashPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded">

            <div class="modal-header rounded">
                <h5 class="modal-title" id="cashPaymentModalLabel">Cash Payment - <span class="paid-amount-show"></span> Tk.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="cashPaymentForm" class="payment-form">
                    <input type="hidden" name="paymentMethod" value="cash">
                    <div class="mb-3">
                        <label for="paymentNote" class="form-label">Note</label>
                        <textarea name="paymentNote" id="paymentNote" class="form-control rounded" rows="3" placeholder="Note"></textarea>
                        <input type="hidden" class="form-control paid-amount-input rounded" id="cashReceived" name="paidAmount" min="0" readonly required>
                    </div>
                </form>
            </div>

            <div class="modal-footer d-flex">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" style="margin-right: auto;" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="submit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" form="cashPaymentForm"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </div>

        </div>
    </div>
</div>
