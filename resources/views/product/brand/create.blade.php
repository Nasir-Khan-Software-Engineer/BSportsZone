<div class="modal fade" id="createBrandModal" tabindex="-1" role="dialog" aria-labelledby="createBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <form id="createBrandForm">
                <div class="modal-header rounded">
                    <h5 class="modal-title" id="createBrandModalLabel">Create New Brand</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    @if (isset($brand))
                        @method("PUT")
                    @else
                        @method("POST")
                    @endif
                    <input type="hidden" name="brandId" id="brandId" value="{{isset($brand)?$brand->id:"0"}}">
                    <div class="form-group">
                        <label for="brandName">Name:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded" id="brandName" placeholder="Name" name="brandName" required value="{{isset($brand)?$brand->name:""}}">
                    </div>
                    <div class="form-group">
                        <label style="display: none;" for="brandDescription">Full Name:</label>
                        <input hidden value="defaulttext" type="text" class="form-control rounded" id="brandDescription" placeholder="Enter brand full name" name="brandDescription" required value="{{isset($brand)?$brand->description:""}}">
                    </div>
                    <div class="form-group">
                        <label style="display: none;" for="brandNote">Note:</label>
                        <textarea hidden class="form-control rounded" placeholder="Enter brand note" name="brandNote">{{isset($brand)?$brand->note:""}}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-type="{{isset($brand)?"update":"create"}}" data-bs-dismiss="modal" id="saveUpdateBrand"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
