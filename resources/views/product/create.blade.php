<!-- Product Create Modal -->
<div class="modal fade" id="productCreateModal" tabindex="-1" role="dialog" aria-labelledby="productCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="productCreateModalLabel">Create New Product</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="productCreateForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-lg-4 form-group">
                            <label for="productCode">Code* <small>(Will Start With {{ (session('accountInfo.productCodePrefix') ?? 'PR').'-' }})</small></label>
                            <input required type="text" class="form-control rounded" name="code" id="productCode" placeholder="Product Code">
                        </div>

                        <div class="col-12 col-lg-8 form-group">
                            <label for="productName">Name*</label>
                            <input required type="text" class="form-control rounded" name="name" id="productName" placeholder="Product Name">
                        </div>

                        <!-- Left Side: Unit and Brand -->
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="productUnit">Unit</label>
                                <select class="form-control rounded" name="unit_id" id="productUnit">
                                    <option value="">Select Unit</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="productBrand">Brand</label>
                                <select class="form-control rounded" name="brand_id" id="productBrand">
                                    <option value="">Select Brand</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Side: Category -->
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="productCategory">Category*</label>
                                <select style="height: 120px;" multiple class="form-control rounded" name="category_id" id="productCategory" required>
                                    <option value="">Select category</option>
                                </select>
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

