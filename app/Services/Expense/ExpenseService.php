<?php

namespace App\Services\Expense;
use App\Services\Expense\IExpenseService;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Number;

class ExpenseService implements IExpenseService
{
    public function getExpenseListWithPagination(Request $request, $posid)
    {
        $searchCriteria = $request->input('search');

        $query = Expense::with('expenseCategory')
            ->where('posid', $posid)
            ->when($searchCriteria, function ($q) use ($searchCriteria) {
                $q->where('title', 'like', "%{$searchCriteria}%")
                    ->orWhereHas('expenseCategory', function ($q2) use ($searchCriteria) {
                        $q2->where('title', 'like', "%{$searchCriteria}%");
                    });
            });

        $totalRecord = Expense::where('posid', $posid)->count();
        $filteredRecord = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');

        $expenses = $this->applySorting(clone $query, $orderColumn, $orderDir)
            ->skip($request->input('start'))
            ->take($request->input('length'))
            ->get();

        $expenses->transform(function ($expense) {
            return $this->transformExpense($expense);
        });

        return [
            "draw" => $request->input('draw'),
            "recordsTotal" => $totalRecord,
            "recordsFiltered" => $filteredRecord,
            "data" => $expenses->toArray(),
        ];
    }


    public function storeExpense(Request $request)
    {
        // Validate the request
        $request->validate([
            'expenseTitle' => 'required|string|min:3|max:500',
            'expenseAmount' => 'required|numeric|decimal:2|max:1000000|gt:0',
            'expensedOn' => 'required|date|before_or_equal:today',
        ]);

        // Create expense
        $expense = new Expense();
        $expense->title = $request->expenseTitle;
        $expense->posid = auth()->user()->posid;
        $expense->shopid = 1; // You may replace with dynamic shop id
        $expense->categoryId = $request->expenseCategory;
        $expense->expenseDate = $request->expensedOn;
        $expense->amount = $request->expenseAmount;
        $expense->created_by = auth()->user()->id;
        $expense->save();

        return $this->transformExpense($expense);
    }


    public function getExpenseForEdit(Expense $expense)
    {
        // Format date for HTML <input type="date">
        $expense->formattedExpenseDate = Carbon::parse($expense->expenseDate)->format('Y-m-d');

        // Get categories for current POS
        $expenseCategories = ExpenseCategory::where('posid', auth()->user()->posid)->get();

        return [
            'expense' => $expense,
            'expenseCategories' => $expenseCategories
        ];
    }


    public function updateExpense(Request $request, Expense $expense)
    {
        $today = Carbon::today()->format('Y-m-d');
        $expenseCreatedOn = Carbon::parse($expense->created_at)->format('Y-m-d');

        if ($expenseCreatedOn != $today) {
            throw new \Exception('Expense created before today cannot be updated.');
        }

        // Validation
        $request->validate([
            'expenseTitle' => 'required|string|min:3|max:500',
            'expenseCategory' => 'required|exists:expense_categories,id',
            'expensedOn' => 'required|date|before_or_equal:today',
            'expenseAmount' => 'required|numeric|decimal:2|gt:0|max:1000000',
        ]);

        // Update the expense
        $expense->title = $request->expenseTitle;
        $expense->categoryId = $request->expenseCategory;
        $expense->expenseDate = $request->expensedOn;
        $expense->amount = $request->expenseAmount;
        $expense->updated_by = auth()->user()->id;
        $expense->save();

        return $this->transformExpense($expense);
    }

    public function deleteExpense(Expense $expense)
    {
        if (!$expense->delete()) {
            throw new \Exception('Something went wrong, please try later.');
        }

        return true;
    }


    private function applySorting($query, $column, $direction)
    {
        return match ((int)$column) {
            0 => $query->orderBy('id', $direction),
            1 => $query->orderBy('title', $direction),
            2 => $query
                    ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
                    ->orderBy('expense_categories.title', $direction)
                    ->select('expenses.*'),
            3 => $query->orderBy('expenseDate', $direction),
            default => $query->orderBy('id', 'desc'),
        };
    }


    private function transformExpense($expense)
    {
        $expense->formattedDate = formatDate($expense->created_at);
        $expense->formattedTime = formatTime($expense->created_at);
        $expense->formattedExpenseDate = formatDate($expense->expenseDate);

        $isToday = Carbon::parse($expense->created_at)->isToday();
        $expense->deletable = $isToday;
        $expense->editable = $isToday;

        if (session('accountInfo.currency') === 'BDT') {
            $expense->amount = str_replace(
                'BDT',
                'Tk',
                Number::currency($expense->amount, session('accountInfo.currency'))
            );
        }

        $expense->createdBy = $expense->creator->name ?? 'N/A';

        return $expense;
    }
    
}
