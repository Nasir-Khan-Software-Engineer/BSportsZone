<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="categoryName">Category Name:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded" id="categoryName" aria-describedby="" placeholder="Enter category name" name="categoryName" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="categorySlug">Slug: <small>(Auto-generated from name, max 100 characters)</small></label>
                                <input type="text" class="form-control rounded" id="categorySlug" placeholder="category-slug" name="slug" maxlength="100">
                                <small class="form-text text-muted">This will be used in the category URL. Auto-generated if left empty.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="categoryTitle">Title:</label>
                                <input type="text" class="form-control rounded" id="categoryTitle" placeholder="Enter category title for SEO" name="title" maxlength="255">
                                <small class="form-text text-muted">SEO title for the category page.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="categoryKeyword">Keywords:</label>
                                <textarea class="form-control rounded" id="categoryKeyword" rows="2" placeholder="Enter SEO keywords (comma separated)" name="keyword"></textarea>
                                <small class="form-text text-muted">SEO keywords for search engines.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="categoryDescription">Description:</label>
                                <textarea class="form-control rounded" id="categoryDescription" rows="3" placeholder="Enter category description for SEO" name="description"></textarea>
                                <small class="form-text text-muted">SEO description for the category page.</small>
                            </div>
                        </div>
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
