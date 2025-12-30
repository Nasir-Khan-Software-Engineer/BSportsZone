<!-- pervice Add, Edit Modal -->
<div class="modal fade" id="serviceEditModal" tabindex="-1" role="dialog" aria-labelledby="perviceEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="perviceEditModalLabel">Update Service<span id="editServiceID"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <ul class="nav nav-tabs" id="perviceEditMenuTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="editServiceBasicInfoTab" data-bs-toggle="tab" data-bs-target="#eidtServiceBasicInfoPane" 
                            type="button" role="tab" aria-controls="eidtServiceBasicInfoPane" aria-selected="true">Basic Info</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="eidtServiceDetailsTab" data-bs-toggle="tab" data-bs-target="#editServiceDetailsPane" type="button" 
                            role="tab" aria-controls="editServiceDetailsPane" aria-selected="false">Details</button>
                    </li>
                </ul>

                <form id="serviceEditForm">
                    <div class="tab-content" id="perviceEditMenuTabContent">
                        <div class="tab-pane fade show active" id="eidtServiceBasicInfoPane" role="tabpanel" aria-labelledby="editServiceBasicInfoTab" tabindex="0">
                            <div class="row mt-2">
                                <div class="col-12 col-lg-3 form-group">
                                    <input type="hidden" id="hiddenServiceID" name="editID">
                                    <label for="editCode">Code* <small>(Will Start With {{ (session('accountInfo.perviceCodePrefix') ?? 'AU').'-' }})</small></label>
                                    <input required readonly type="text" class="form-control rounded" name="code" id="editCode" placeholder="Service Code">
                                </div>

                                <div class="col-12 col-lg-9 form-group">
                                    <label for="editName">Name*</label>
                                    <input type="text" class="form-control rounded" name="name" id="editName" placeholder="Service Name">
                                </div>

                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <div class="row">
                                            <div class="col-4 form-group">
                                                <label for="editPrice">Price*</label>
                                                <input step=".01" type="number" class="form-control rounded" name="price" id="editPrice" placeholder="Service Price" min="0">
                                            </div>
                                            <div class="col-4 form-group">
                                                <label for="editStaff">Default Staff</label>
                                                <select class="form-control rounded" name="staff_id" id="editStaff">
                                                    <option value="">Select Staff</option>
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-12 form-group">
                                            <label for="editCategory">Category*</label>
                                            <select style="height: 120px;" multiple class="form-control rounded" name="category" id="editCategory">
                                                <option value="">Select category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-12">
                                            <label for="showImage"><b>Image</b></label>

                                            <div class="avatar-upload">
                                                    <div class="avatar-edit">
                                                        <input type='file' id="editImage" name="image" accept=".png, .jpg, .jpeg" />
                                                        <label for="editImage"></label>
                                                    </div>
                                                    <div id="perviceImagePreviewEdit" class="avatar-preview">
                                                        <div id="imagePreviewEdit" style="background-image: url();"></div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade hide" id="editServiceDetailsPane" role="tabpanel" aria-labelledby="editServiceDetailsTab" tabindex="1">
                            <div class="row mt-2">
                                <div class="col-12 form-group">
                                    <label for="editDetails">Details</label>
                                    <textarea name="description" id="editDetails" class="form-control rounded" cols="30" rows="10" placeholder="Details"></textarea>
                                </div> 
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" id="updateService" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Update</button>
            </div>
        </div>
    </div>
</div>