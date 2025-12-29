<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded">
            <form id="createCategoryForm">
                <div class="modal-header rounded">
                    <h5 class="modal-title" id="createCategoryModalLabel">Create New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="categoryID" id="categoryID">
                    <div class="form-group">
                        <label for="categoryName">Category Name:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded" id="categoryName" aria-describedby="" placeholder="Enter category name" name="categoryName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-type="create" id="saveUpdateCategory"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
