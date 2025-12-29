<!-- customer Add, Edit Modal  -->
<div class="modal fade" id="customerAddEditModal" tabindex="-1" role="dialog" aria-labelledby="customerAddEditModalLebel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="customerAddEditModalLebel">Create New Customer<span id="customerID"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="customerAddEditForm" autocomplete="off" data-formSubmitFor="create">
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="customerSetupTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="customerBasicInfoTab" data-bs-toggle="tab" data-bs-target="#customerBasicInfoPane" type="button" role="tab" aria-controls="customerBasicInfoPane" aria-selected="true">Basic Info</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="customerSetupTabContent">
                        <div class="tab-pane fade show active" id="customerBasicInfoPane" role="tabpanel" aria-labelledby="customerBasicInfoTab" tabindex="0">
                            <div class="row mt-2">
                                <div class="col-12 col-lg-6 form-group">
                                    <input type="hidden" id="hiddenCustomerID" name="ID">
                                    <input type="hidden" name="gender" value="F">

                                    <label class="form-label icon-label" for="name">Name<span class="text-danger required-star">*</span></label>
                                    <input required minlength="3" maxlength="100" type="text" class="form-control rounded" name="name" id="name" placeholder="Customer Name">
                                </div>

                                <div class="col-12 col-lg-6 form-group">
                                    <label class="form-label icon-label" for="phone1">Phone<span class="text-danger required-star">*</span></label>
                                    <input required minlength="11" maxlength="11" type="text" class="form-control rounded" name="phone1" id="phone1" placeholder="Phone Number">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label icon-label"><i class="bi bi-telephone-plus-fill"></i> Age group</label>
                                    <select name="age_group" id="age_group" class="form-select rounded">
                                        <option value="">Select age group</option>
                                        <option value="Teen (13–19)">Teen (13–19)</option>
                                        <option value="Young Adult (20–35)">Young Adult (20–35)</option>
                                        <option value="Adult (36–55)">Adult (36–55)</option>
                                        <option value="Senior (56+)">Senior (56+)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-lg-6 form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control rounded" name="email" id="email" placeholder="Customer Email">
                                </div>

                                <div class="col-12 form-group">
                                    <label for="address">Address</label>
                                    <textarea name="address" id="address" class="form-control rounded" cols="30" rows="1" placeholder="Address"></textarea>
                                </div>

                                <div class="col-12 form-group">
                                    <label for="note">Note</label>
                                    <textarea name="note" id="note" class="form-control rounded" cols="30" rows="3" placeholder="Note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button type="submit" id="saveCustomer" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>