<div class="modal fade" id="createExpenseCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createExpenseCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <form id="createExpenseCategoryForm">
                <div class="modal-header rounded">
                    <h5 class="modal-title" id="createExpenseCategoryModalLabel">Create New Expense Category</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    @if (isset($expenseCategory))
                        @method("PUT")
                    @else
                        @method("POST")
                    @endif
                    <input type="hidden" name="expenseCategoryId" id="expenseCategoryId" value="{{isset($expenseCategory)?$expenseCategory->id:"0"}}">
                    <div class="form-group">
                        <label for="expenseCategoryTitle">Title:<span class="text-danger required-star">*</span></label>
                        <input type="text" class="form-control rounded" id="expenseCategoryTitle" placeholder="Name" name="expenseCategoryTitle" required value="{{isset($expenseCategory)?$expenseCategory->title:""}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-type="{{isset($expenseCategory)?"update":"create"}}" data-bs-dismiss="modal" id="saveUpdateExpenseCategory"> <i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
