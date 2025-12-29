@extends('layouts.main-layout')

@section('content')
<div id="expenseCategoryModalContainer">
</div>
<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Expense Category List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchExpenseCategory" placeholder="Search Category">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="createExpenseCategoryBtn"><i class="fa-solid fa-plus"></i> Create Category</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered datatable" id="expenseCategoryTable">
                <thead>
                    <tr>
                        <th scope="col" class="text-center" style="width: 10%;">ID</th>
                        <th scope="col" class="text-center" style="width: 35%;">TITLE</th>
                        <th scope="col" class="text-center" style="width: 15%;">CREATED ON</th>
                        <th scope="col" class="text-center" style="width: 15%;">CREATED BY</th>
                        <th scope="col" class="text-center" style="width: 15%;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenseCategories as $expenseCategory)
                    <tr>
                        <td class="text-left align-middle text-center">{{$expenseCategory->id}}</td>
                        <td class="align-middle text-center">{{$expenseCategory->title}}</td>
                        <td class="text-center align-middle">
                            <div class="text-center d-inline-block px-2" style="line-height: normal;">
                                {{ $expenseCategory->formattedTime }}
                                <br>
                                {{ $expenseCategory->formattedDate }}
                            </div>
                        </td>
                        <td class="align-middle text-center">{{$expenseCategory->createdBy}}</td>
                        <td class="text-right text-center align-middle">
                            <button data-id="{{$expenseCategory->id}}" data-name="{{$expenseCategory->name}}" class='btn btn-sm edit-expense-category thm-btn-bg thm-btn-text-color'><i
                                    class='fa-solid fa-pen-to-square'></i></button>
                            <button data-id="{{$expenseCategory->id}}" class='btn btn-sm  thm-btn-bg thm-btn-text-color delete-expense-category'><i class='fa-solid fa-trash'></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
@vite(['resources/js/utilities/expense-category-script.js'])
<script>
const ExpenseCategoryUrls = {
    'getExpenseCategories': "{{ route('utilities.expense.category.index') }}",
    'saveExpenseCategory': "{{ route('utilities.expense.category.store') }}",
    'createExpenseCategory': "{{ route('utilities.expense.category.create') }}",
    'updateExpenseCategory': "{{ route('utilities.expense.category.update', ['category' => 'categoryid']) }}",
    'deleteExpenseCategory': "{{ route('utilities.expense.category.destroy', ':id') }}",
    'editExpenseCategory': "{{ route('utilities.expense.category.edit', ['category' => 'categoryid']) }}"
}

$(document).ready(function() {

    WinPos.Datatable.initDataTable("#expenseCategoryTable");
    $("#searchExpenseCategory").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    });

    $("#createExpenseCategoryBtn").on('click', function() {
        WinPos.ExpenseCategory.getCreateExpenseCategoryForm("#expenseCategoryModalContainer",
            function() {
                $("#createExpenseCategoryModal").modal('show');
            });
    });

    $(document).on('click', '#saveUpdateExpenseCategory', function(event) {
        event.preventDefault();
        WinPos.ExpenseCategory.saveExpenseCategory(WinPos.Common.getFormData(
            "#createExpenseCategoryForm"), $("#saveUpdateExpenseCategory").attr('data-type'));
    });

    $(document).on("click", ".edit-expense-category", function() {
        WinPos.Datatable.selectRow(this);
        WinPos.ExpenseCategory.getUpdateExpenseCategoryForm("#expenseCategoryModalContainer", $(this)
            .attr('data-id'),
            function() {
                $("#createExpenseCategoryModal").modal('show');
            });
    });

    $(document).on("click", ".delete-expense-category", function() {
        if (confirm(
                "Are you sure you want to delete this expense category?\nClick OK to continue or Cancel."
            )) {
            WinPos.Datatable.selectRow(this);
            WinPos.ExpenseCategory.deleteExpenseCategory($(this).attr('data-id'));
        }
    });
});
</script>
@endsection