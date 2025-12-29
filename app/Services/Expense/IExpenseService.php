<?php
namespace App\Services\Expense;
use Illuminate\Http\Request;
use App\Models\Expense;

interface IExpenseService
{
    public function getExpenseListWithPagination(Request $request, $posid);
    public function storeExpense(Request $request);
    public function getExpenseForEdit(Expense $expense);
    public function updateExpense(Request $request, Expense $expense);
    public function deleteExpense(Expense $expense);
}