<div class="modal fade" id="createExpenseModal" tabindex="-1" role="dialog" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <form id="createExpenseForm">
                <div class="modal-header rounded">
                    <h5 class="modal-title" id="createExpenseModalLabel">
                        @if (isset($expense))
                            Update Expense || Expense ID: {{$expense->id}}
                        @else
                            Add New Expense
                        @endif
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    @if (isset($expense))
                        @method("PUT")
                    @else
                        @method("POST")
                    @endif
                    <input type="hidden" name="expenseId" id="expenseId" value="{{isset($expense)?$expense->id:"0"}}">
                    <div class="form-group">
                        <label for="expenseTitle">Title: <span class="text-danger required-star">*</span></label>
                        <input type="text" class="form-control rounded" id="expenseTitle" placeholder="Enter expense title" name="expenseTitle" required value="{{isset($expense)?$expense->title:""}}">
                    </div>
                    <div class="form-group">
                        <label for="expenseCategory">Expense Category: <span class="text-danger required-star">*</span></label>
                        <select class="form-control rounded" id="expenseCategory"  name="expenseCategory">
                            @foreach($expenseCategories as $expenseCategory)
                                <option value="{{ $expenseCategory->id }}"
                                    @if(isset($expense) && $expenseCategory->id == $expense->categoryId)
                                        selected
                                    @endif
                                >{{ $expenseCategory->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="expenseOn">Expense On: <span class="text-danger required-star">*</span></label>
                                <input
                                    type="date"
                                    id="expenseOn"
                                    name="expensedOn"
                                    class="form-control rounded"
                                    required
                                    max="{{ date('Y-m-d') }}"
                                    value="{{ old('expensedOn', $expense->formatteExpenseDate ?? date('Y-m-d')) }}"
                                >

                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="expenseAmount">Amount: <span class="text-danger required-star">*</span></label>
                                <input type="number" class="form-control rounded" id="expenseAmount" placeholder="Enter expense amount" name="expenseAmount" required value="{{isset($expense)?$expense->amount:"0"}}" pattern="^\d*(\.\d{0,2})?$">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-type="{{isset($expense)?"update":"create"}}"id="saveUpdateExpense"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
