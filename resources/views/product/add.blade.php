<!-- product Add, Edit Modal -->
<div class="modal fade" id="productAddModal" tabindex="-1" role="dialog" aria-labelledby="productAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="productAddModalLabel">Create New Service<span id="productID"></span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <ul class="nav nav-tabs" id="productSetupTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="productBasicInfoTab" data-bs-toggle="tab" data-bs-target="#productBasicInfoPane" 
                            type="button" role="tab" aria-controls="productBasicInfoPane" aria-selected="true">Basic Info</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="productDetailsTab" data-bs-toggle="tab" data-bs-target="#productDetailsPane" type="button" 
                            role="tab" aria-controls="productDetailsPane" aria-selected="false">Details</button>
                    </li>
                </ul>

                <form id="productAddForm">
                    <div class="tab-content" id="productSetupTabContent">
                        <div class="tab-pane fade show active" id="productBasicInfoPane" role="tabpanel" aria-labelledby="productBasicInfoTab" tabindex="0">
                            <div class="row mt-2">
                                <div class="col-12 col-lg-3 form-group">
                                   <label for="code">Code* <small>(Will Start With {{ (session('accountInfo.productCodePrefix') ?? 'AU').'-' }})</small></label>
                                    <input required type="text" class="form-control rounded" name="code" id="code" placeholder="Service Code">
                                </div>

                                <div class="col-12 col-lg-9 form-group">
                                    <label for="name">Name*</label>
                                    <input type="text" class="form-control rounded" name="name" id="name" placeholder="Service Name">
                                </div>

                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-3 form-group">
                                                <label for="price">Price*</label>
                                                <input step=".01" type="number" class="form-control rounded" name="price" id="price" placeholder="Service Price">
                                            </div>
                                            <div class="col-lg-3 form-group">
                                                <label for="beautician_id">Default Beautician</label>
                                                <select class="form-control rounded" name="beautician_id" id="beautician_id">
                                                    <option value="">Select Beautician</option>
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12 col-lg-6 form-group">
                                                <label for="category_id">Category*</label>
                                                <select style="height: 120px;" multiple class="form-control rounded" name="category_id" id="category_id">
                                                    <option value="">Select category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-6 form-group">
                                                <label for="image">Image*</label>
                                                <div class="avatar-upload">
                                                    <div class="avatar-edit">
                                                        <input type='file' id="image" name="image" accept=".png, .jpg, .jpeg" />
                                                        <label for="image"></label>
                                                    </div>
                                                    <div class="avatar-preview">
                                                        <div id="imagePreview" style="background-image: url();"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade hide" id="productDetailsPane" role="tabpanel" aria-labelledby="productDetailsTab" tabindex="1">
                            <div class="row mt-2">
                                <div class="col-12 form-group">
                                    <label for="details">Note</label>
                                    <textarea name="description" id="details" class="form-control rounded" cols="30" rows="10" placeholder="Note"></textarea>
                                </div> 
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                <button type="button" id="saveProduct" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </div>
        </div>
    </div>
</div>