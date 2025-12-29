<!-- Shop Add, Edit Modal -->
<div class="modal fade" id="shopAddEditModal" tabindex="-1" role="dialog" aria-labelledby="shopAddEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="shopAddEditModalLabel">Create New Shop<span id="shopID"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="shopSetupTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="shopBasicInfoTab" data-toggle="tab" data-target="#shopBasicInfoPane" 
                        type="button" role="tab" aria-controls="shopBasicInfoPane" aria-selected="true">Basic Info</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="shopAboutTab" data-toggle="tab" data-target="#shopAboutPane" type="button" 
                        role="tab" aria-controls="shopAboutPane" aria-selected="false">About</button>
                    </li>
                </ul>

                <form id="shopAddEditForm">
                    <div class="tab-content" id="shopSetupTabContent">
                        <div class="tab-pane fade show active" id="shopBasicInfoPane" role="tabpanel" aria-labelledby="shopBasicInfoTab">
                        <div class="row mt-2">
                                <div class="col-12">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <input type="hidden" id="hiddenShopID" name="ID">
                                                <label for="name">Name*</label>
                                                <input required type="text" class="form-control rounded" name="name" id="name" placeholder="Office Name">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email*</label>
                                                <input required type="Email" class="form-control rounded" name="email" id="email" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="primaryPhone">Primary Phone*</label>
                                            <input type="text" class="form-control rounded" name="primaryPhone" id="primaryPhone" placeholder="Primary Phone">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="secondaryPhone">Secondary Phone</label>
                                            <input type="text" class="form-control rounded" name="secondaryPhone" id="secondaryPhone" placeholder="Secondary Phone">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="division">Division*</label>
                                            <input required type="text" class="form-control rounded" name="division" id="division" placeholder="Division">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="district">District*</label>
                                            <input required type="text" class="form-control rounded" name="district" id="district" placeholder="District">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="area">Area / Thana*</label>
                                            <input required type="text" class="form-control rounded" id="thana" name="thana" id="area" placeholder="Area / Thana">
                                        </div>
                                        <div class="form-group col-12">
                                            <label for="address">Address*</label>
                                            <textarea required class="form-control rounded" name="address" id="address" rows="1" placeholder="Address"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="shopAboutPane" role="tabpanel" aria-labelledby="shopAboutTab">
                            <div class="row mt-2">
                                <div class="form-group col-12">
                                    <label for="about">About</label>
                                    <textarea required class="form-control rounded" name="about" id="about" rows="5" placeholder="Keep Some Notes About Your Shop"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" id="saveShop" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                <button type="button" id="updateShop" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">Update</button>
            </div>
        </div>
    </div>
</div>