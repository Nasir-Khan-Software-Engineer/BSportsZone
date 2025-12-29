<?php
namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenseCategories = ExpenseCategory::with('creator')->where('posid', '=', auth()->user()->posid)->get();

        foreach ($expenseCategories as $expenseCategory) {
            $expenseCategory->formattedDate = formatDate($expenseCategory->created_at);
            $expenseCategory->formattedTime = formatTime($expenseCategory->created_at);
            $expenseCategory->createdBy = $expenseCategory->creator->name;
        }

        return view("utilities/expensesCategory/index", ['expenseCategories' => $expenseCategories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('utilities/expensesCategory/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'expenseCategoryTitle' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    Rule::unique('expense_categories', 'title')
                        ->where(fn($query) => $query->where('posid', auth()->user()->posid)),
                ],
            ]);

            $expenseCategory             = new ExpenseCategory;
            $expenseCategory->title      = $request->expenseCategoryTitle;
            $expenseCategory->posid      = auth()->user()->posid;
            $expenseCategory->created_by = auth()->user()->id;

            $expenseCategory->save();
            $expenseCategory->formattedDate = formatDate($expenseCategory->created_at);
            $expenseCategory->formattedTime = formatTime($expenseCategory->created_at);
            $expenseCategory->createdBy = $expenseCategory->creator->name;

            return response()->json(
                [
                    'status'          => 'success',
                    'message'         => 'Expense category updated successfully.',
                    'expenseCategory' => $expenseCategory,
                ]);
        } catch (ValidationException $exception) {
            return response()->json(
                [
                    'status'  => 'error',
                    'message' => '',
                    'errors'  => $exception->validator->errors(),
                ]
            );
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'status'  => 'error',
                    'message' => 'Something went wrong, please try later.',
                ]
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseCategory $category)
    {
        return view('utilities/expensesCategory/create', ['expenseCategory' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseCategory $category)
    {
        try {

            $request->validate([
                'expenseCategoryTitle' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    Rule::unique('expense_categories', 'title')
                        ->where(fn($query) => $query->where('posid', auth()->user()->posid))
                        ->ignore($category->id),
                ],
            ]);

            $category->title      = $request->expenseCategoryTitle;
            $category->updated_by = auth()->user()->id;

            $category->update();
            $category->formattedDate = formatDate($category->created_at);
            $category->formattedTime = formatTime($category->created_at);
            $category->createdBy = $category->creator->name;

            return response()->json(
                [
                    'status'          => 'success',
                    'message'         => 'Expense category updated successfully.',
                    'expenseCategory' => $category,
                ]);
        } catch (ValidationException $exception) {
            return response()->json(
                [
                    'status'  => 'error',
                    'message' => '',
                    'errors'  => $exception->validator->errors(),
                ]
            );
        } catch (Exception $exception) {
            return $exception;
            return response()->json(
                [
                    'status'  => 'error',
                    'message' => 'Something went wrong, please try later.',
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $category = ExpenseCategory::find($id);

            if ($category->expenses()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['This category has expense items.'],
                    ],
                ]);
            } else {
                if ($category->delete()) {
                    return response()->json(
                        [
                            'status'  => 'success',
                            'message' => 'Expense category deleted successfully.',
                        ]
                    );
                } else {
                    return response()->json(
                        [
                            'status'  => 'error',
                            'message' => 'Something went wrong, please try later - test.',
                        ]
                    );
                }
            }

        } catch (Exception $exception) {
            return $exception;
        }
    }
}
