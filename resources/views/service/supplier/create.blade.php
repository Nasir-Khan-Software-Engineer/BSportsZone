<div class="modal fade" id="createSupplierModal" tabindex="-1" role="dialog" aria-labelledby="createSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <form id="createSupplierForm">
                <div class="modal-header rounded">
                    <h5 class="modal-title" id="createSupplierModalLabel">Create New Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    @if (isset($supplier))
                        @method("PUT")
                    @else
                        @method("POST")
                    @endif
                    <input type="hidden" name="supplierId" id="supplierId" value="{{isset($supplier)?$supplier->id:"0"}}">
                    <div class="form-group">
                        <label for="supplierName">Name:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded" id="supplierName" placeholder="Enter supplier name" name="name" required value="{{isset($supplier)?$supplier->name:""}}">
                    </div>

                    <div class="form-group">
                        <label for="shortAddress">Address:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded" placeholder="Enter supplier address" name="address" id="shortAddress" value="{{isset($supplier)?$supplier->address:""}}">
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="supplierPhone">Phone:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded" id="supplierPhone" placeholder="Enter supplier phone number" name="phone" required value="{{isset($supplier)?$supplier->phone_1:""}}">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="supplierEmail">Email:</label>
                                <input type="email" class="form-control rounded" id="supplierEmail" placeholder="Enter supplier email" name="email" required value="{{isset($supplier)?$supplier->email:""}}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="supplierNote">Note:</label>
                        <textarea class="form-control rounded" placeholder="Enter note" name="note" id="supplierNote">{{isset($supplier)?$supplier->note:""}}</textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-type="{{isset($supplier)?"update":"create"}}" id="saveUpdateSupplier"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

