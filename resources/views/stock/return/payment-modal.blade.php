<!-- Return Payment Modal -->
<div class="modal fade" id="returnPaymentModal" tabindex="-1" role="dialog" aria-labelledby="returnPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="returnPaymentModalLabel">Add Payment</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="returnPaymentForm">
                    @csrf
                    <input type="hidden" id="paymentId" name="payment_id">
                    <input type="hidden" id="returnId" name="return_id">
                    
                    <div class="row">
                        <div class="col-12 col-lg-6 form-group">
                            <label for="paymentMethod">Payment Method*</label>
                            <select class="form-control rounded" name="payment_method" id="paymentMethod" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="wallet">Mobile Wallet</option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-6 form-group" id="paymentViaGroup" style="display: none;">
                            <label for="paymentVia">Payment Via*</label>
                            <select class="form-control rounded" name="payment_via" id="paymentVia">
                                <option value="">Select Option</option>
                            </select>
                        </div>

                        <div class="col-12 col-lg-6 form-group">
                            <label for="paymentAmount">Amount*</label>
                            <input type="number" step="0.01" class="form-control rounded" name="amount" id="paymentAmount" required min="0.01" placeholder="0.00">
                        </div>

                        <div class="col-12 col-lg-6 form-group" id="transactionIdGroup" style="display: none;">
                            <label for="transactionId">Transaction ID</label>
                            <input type="text" class="form-control rounded" name="transaction_id" id="transactionId" placeholder="Transaction ID">
                        </div>

                        <div class="col-12 form-group">
                            <label for="paymentNote">Note</label>
                            <textarea name="note" id="paymentNote" class="form-control rounded" rows="3" placeholder="Payment note"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Close
                </button>
                <button type="button" class="btn thm-btn-bg thm-btn-text-color btn-sm" id="savePaymentBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Payment
                </button>
            </div>
        </div>
    </div>
</div>
