<?php

namespace App\Http\Controllers\Utilities;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Services\Expense\IExpenseService;

class ExpenseController extends Controller
{

     public function __construct(IExpenseService $iExpenseService,){
        $this->expenseService = $iExpenseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("utilities/expenses/index");
    }

    public function datatable(Request $request)
    {
        $POSID = auth()->user()->POSID;

        $allExpense = $this->expenseService->getExpenseListWithPagination($request, $POSID);

        return response()->json($allExpense);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $expenseCategories = ExpenseCategory::where('POSID', '=', auth()->user()->POSID)->get();
        return view('utilities/expenses/create',['expenseCategories' => $expenseCategories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $expense = $this->expenseService->storeExpense($request);

            return response()->json([
                'status' => 'success',
                'message' => 'Expense created successfully.',
                'expense' => $expense
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $e->validator->errors()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong, please try later.'
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $data = $this->expenseService->getExpenseForEdit($expense);

        return view('utilities/expenses/create', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        try {
            $expense = $this->expenseService->updateExpense($request, $expense);

            return response()->json([
                'status' => 'success',
                'message' => 'Expense updated successfully.',
                'expense' => $expense
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $e->validator->errors()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?? 'Something went wrong, please try later.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        try {
            $this->expenseService->deleteExpense($expense);

            return response()->json([
                'status' => 'success',
                'message' => 'Expense deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?? 'Something went wrong, please try later.'
            ]);
        }
    }
}