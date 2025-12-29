@extends('layouts.main-layout')

@section('content')

<div id="expenseModalContainer">
</div>
<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Expense List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchExpense" placeholder="Search Expense">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="createExpenseBtn"><i class="fa-solid fa-plus"></i> Create Expense</button>

                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered datatable" id="expenseTable">
                <thead>
                    <tr>
                        <th scope="col" style="width: 10%;" class="text-center">ID</th>
                        <th scope="col" style="width: 20%;" class="text-center">TITLE</th>
                        <th scope="col" class="text-center" style="width: 15%;">Exp. CATEGORY</th>
                        <th scope="col" class="text-center" style="width: 10%;">AMOUNT</th>
                        <th scope="col" class="text-center" style="width: 10%;">Exp. ON</th>
                        <th scope="col" class="text-center" style="width: 10%;">CREATED ON</th>
                        <th scope="col" class="text-center" style="width: 10%;">CREATED BY</th>
                        <th scope="col" class="text-center" style="width: 15%;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
@vite(['resources/js/utilities/expense-script.js'])
<script>
const ExpenseUrls = {
    'getExpenses': "{{ route('utilities.expenses.index') }}",
    'saveExpense': "{{ route('utilities.expenses.store') }}",
    'createExpense': "{{ route('utilities.expenses.create') }}",
    'updateExpense': "{{ route('utilities.expenses.update', ['expense' => 'expenseid']) }}",
    'deleteExpense': "{{ route('utilities.expenses.destroy', ['expense' => 'expenseid']) }}",
    'editExpense': "{{ route('utilities.expenses.edit', ['expense' => 'expenseid']) }}",
    'datatable': "{{ route('utilities.expenses.datatable') }}"
}

$(document).ready(function() {

    WinPos.Datatable.initDataTable("#expenseTable", WinPos.Expense.datatableConfig());

    $("#searchExpense").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    });

    //WinPos.Expense.disableEditUpdateButtons();

    $("#createExpenseBtn").on('click', function() {
        WinPos.Expense.getCreateExpenseForm("#expenseModalContainer", function() {
            $("#createExpenseModal").modal('show');
        });
    });

    $(document).on('click', '#saveUpdateExpense', function(e) {
        e.preventDefault();
        WinPos.Expense.saveExpense(WinPos.Common.getFormData("#createExpenseForm"), $(
                "#saveUpdateExpense").attr('data-type'))
            .then(() => {
                $("#createExpenseModal").modal('hide');
            })
            .catch(() => {

            });
    });

    $(document).on("click", ".edit-expense", function() {
        WinPos.Datatable.selectRow(this);
        WinPos.Expense.getUpdateExpenseForm("#expenseModalContainer", $(this).attr('data-expenseid'),
            function() {
                $("#createExpenseModal").modal('show');
            });
    });

    $(document).on("click", ".delete-expense", function() {
        if (confirm("Are you sure you want to delete this expense?\nClick OK to continue or Cancel.")) {
            WinPos.Datatable.selectRow(this);
            WinPos.Expense.deleteExpense($(this).attr('data-expenseid'));
        }
    });
});
</script>
@endsection