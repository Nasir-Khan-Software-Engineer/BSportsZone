<!-- Unit Add, Edit Modal  -->
<div class="modal fade" id="unitAddEditModal" tabindex="-1" role="dialog" aria-labelledby="unitAddEditModalLebel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="unitAddEditModalLebel">Create New Unit<span id="unitID"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="unitAddEditForm">
                    <div class="row mt-2">
                        <div class="col-12 col-lg-6 form-group">
                            <input type="hidden" id="hiddenUnitID" name="ID">
                            <label for="name">Name*</label>
                            <input required type="text" class="form-control rounded" name="name" id="name" placeholder="Name">
                        </div>

                        <div class="col-12 col-lg-6 form-group">
                            <label for="name">Short Form*</label>
                            <input required type="text" class="form-control rounded" name="shortform" id="shortform" placeholder="Short Form">
                        </div>

                        <!-- <div class="col-12 form-group">
                            <label for="note">Note</label>
                            <textarea name="note" id="note" class="form-control rounded" cols="30" rows="3" placeholder="Note"></textarea>
                        </div> -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" id="saveUnit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                <button type="button" id="updateUnit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update</button>
            </div>
        </div>
    </div>
</div>
