<!-- role Add, Edit Modal  -->
<div class="modal fade" id="roleShowModal" tabindex="-1" role="dialog" aria-labelledby="roleShowModalLebel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="roleShowModalLebel">Show Role</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="roleShowModalBodyContent">
                    <div class="mb-3">
                        <h5>Role: <span id="roleName">-</span></h5>
                        <p>Description: <span id="roleDescription">-</span></p>
                    </div>
                    
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Access Right</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody id="roleAccessTableBody">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
            </div>
        </div>
    </div>
</div>