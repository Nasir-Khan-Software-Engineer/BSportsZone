<?php
namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Category\ICategoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function __construct(ICategoryService $iCategoryService)
    {
        $this->categoryService = $iCategoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories(auth()->user()->posid);

        foreach ($categories as $category) {
            $category->formattedDate = formatDate($category->created_at);
            $category->formattedTime = formatTime($category->created_at);
        }

        return view("product/category/index", ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'categoryName' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    Rule::unique('category', 'name') // 👈 specify DB column
                        ->where('posid', auth()->user()->posid),
                ],
            ]);

            $category             = new Category;
            $category->posid      = auth()->user()->posid;
            $category->name       = ucwords($request->categoryName);
            $category->icon       = '';
            $category->created_by = auth()->user()->id;
            $category->save();

            $category->createdBy      = auth()->user()->name;
            $category->formattedDate = formatDate($category->created_at);
            $category->formattedTime = formatTime($category->created_at);

            return response()->json(
                [
                    'status'   => 'success',
                    'message'  => 'Category created successfully.',
                    'category' => $category,
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
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => [
                        'Exception' => $exception->getMessage(),
                    ],
                ]
            );
        }
    }
    public function update(Request $request, Category $category)
    {
        try {

            $request->validate([
                'categoryName' => [
                    'required',
                    'string',
                    'min:3',
                    'max:100',
                    Rule::unique('category', 'name') // 👈 tell Laravel to check 'name' column
                        ->where(fn($query) => $query->where('posid', auth()->user()->posid))
                        ->ignore($category->id), // 👈 exclude current row when updating
                ],
                'categoryID'   => 'required',
            ]);

            $category->name       = ucwords($request->categoryName);
            $category->updated_by = auth()->user()->id;
            $category->update();

            $category->createdBy     = $category->creator->name;
            $category->formattedDate = formatDate($category->created_at);
            $category->formattedTime = formatTime($category->created_at);

            return response()->json(
                [
                    'status'   => 'success',
                    'message'  => 'Category updated successfully.',
                    'category' => $category,
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
            return response()->json(
                [
                    'status'  => 'error',
                    'message' => 'Something went wrong, please try later.',
                ]
            );
        }
    }

    public function destroy(string $id)
    {
        try {

            $category = Category::Where('posid', auth()->user()->posid)->where('id', $id)->first();

            if ($category->products()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['This category has service items.'],
                    ],
                ]);
            } else {
                $categoryCount = $this->categoryService->deleteCategory(auth()->user()->posid, $id);

                if ($categoryCount > 0) {
                    return response()->json(
                        [
                            'status'  => 'success',
                            'message' => 'Category deleted successfully.',
                        ]
                    );
                } else {
                    return response()->json(
                        [
                            'status'  => 'error',
                            'message' => 'Something went wrong, please try later.',
                        ]
                    );
                }
            }

        } catch (Exception $exception) {
            return $exception;
        }
    }
}
