<div class="modal fade" id="loyaltyVerificationModal" tabindex="-1" role="dialog" aria-labelledby="loyaltyVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="loyaltyVerificationForm">
                <div class="modal-header">
                    <h3 class="modal-title" id="loyaltyVerificationModalLabel">Verify Loyalty Card</h3>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <input pattern="\d{1,20}" oninput="this.value=this.value.replace(/\D/g,'').slice(0,20)" type="text" name="verifyLoyaltyCardNumber" id="verifyLoyaltyCardNumber" class="form-control" placeholder="Enter Card Number" autocomplete="off" maxlength="20" style="font-size:20px; height:40px;">
                    </div>

                    <div class="form-check mt-3 cursor-pointer">
                        <input style="cursor:pointer; width:25px; height:25px;" class="form-check-input  cursor-pointer" type="checkbox" value="" id="skipLoyaltyVerification">
                        <label style="cursor:pointer; font-size:19px; margin-top:3px;" class="form-check-label  cursor-pointer" for="skipLoyaltyVerification">
                            &nbsp; Skip verification (Do not apply loyalty discount)
                        </label>
                    </div>

                    <div class="mt-2" id="skipReasonDiv" style="display:none;">
                        <label for="skipReason">Reason:</label>
                        <textarea class="form-control" id="skipReason" name="skipReason" rows="2" placeholder="Enter reason..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="btnVerifyLoyaltyCard" class="btn thm-btn-bg thm-btn-text-color"><i class="fa-solid fa-check"></i> Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>
