<!-- Unit Add, Edit Modal  -->
<div class="modal fade" id="unitModalShow" tabindex="-1" role="dialog" aria-labelledby="unitModalShowLebel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="unitAddEditModalLebel">Unit Details<span id="showUnitID"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mt-2">
                    <div class="col-12 form-group">
                        <div><b>Name</b></div>
                        <div id="showName">This is the unit's name</div>
                    </div>
                    <div class="col-12 form-group">
                        <div><b>Short Form</b></div>
                        <div id="showShortForm">This is the unit's Short Form</div>
                    </div>
                    <div class="col-12 form-group">
                        <div><b>Note</b></div>
                        <div id="showNote">This is a note about the unit</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
            </div>
        </div>
    </div>
</div>