<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="reviewModalLabel">Add Review</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" id="reviewId" name="review_id">
                    <input type="hidden" id="reviewEmployeeId" name="employee_id" value="{{ $employee->id ?? '' }}">

                    <div class="form-group">
                        <label for="reviewDate">Review Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control rounded" id="reviewDate" name="review_date" required>
                    </div>

                    <div class="form-group">
                        <label for="reviewTitle">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded" id="reviewTitle" name="title" placeholder="Enter review title" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="reviewStatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control rounded" id="reviewStatus" name="status" required>
                            <option value="">Select Status</option>
                            <option value="positive">Positive</option>
                            <option value="negative">Negative</option>
                            <option value="warning">Warning</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reviewDetails">Details</label>
                        <textarea class="form-control rounded" id="reviewDetails" name="details" rows="5" placeholder="Enter review details" maxlength="5000"></textarea>
                        <small class="form-text text-muted">Maximum 5000 characters</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </button>
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="saveReviewBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

