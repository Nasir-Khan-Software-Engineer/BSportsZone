<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeReview;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EmployeeReviewController extends Controller
{
    /**
     * Store a newly created review
     */
    public function store(Request $request)
    {
        try {
            $posId = auth()->user()->POSID;
            
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'review_date' => 'required|date',
                'title' => 'required|string|min:2|max:255',
                'status' => 'required|in:positive,negative,warning',
                'details' => 'nullable|string|max:5000',
            ]);

            // Ensure employee belongs to current POSID
            $employee = Employee::where('id', $request->employee_id)
                ->where('POSID', $posId)
                ->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found or unauthorized.',
                ], 404);
            }

            $review = new EmployeeReview();
            $review->POSID = $posId;
            $review->employee_id = $request->employee_id;
            $review->review_date = $request->review_date;
            $review->title = $request->title;
            $review->status = $request->status;
            $review->details = $request->details;
            $review->created_by = auth()->id();
            $review->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Review created successfully.',
                'review' => $this->formatReview($review),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create review: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, EmployeeReview $review)
    {
        try {
            $posId = auth()->user()->POSID;
            
            // Ensure review belongs to current POSID
            if ($review->POSID != $posId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.',
                ], 403);
            }

            $request->validate([
                'review_date' => 'required|date',
                'title' => 'required|string|min:2|max:255',
                'status' => 'required|in:positive,negative,warning',
                'details' => 'nullable|string|max:5000',
            ]);

            $review->review_date = $request->review_date;
            $review->title = $request->title;
            $review->status = $request->status;
            $review->details = $request->details;
            $review->updated_by = auth()->id();
            $review->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Review updated successfully.',
                'review' => $this->formatReview($review),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update review: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete (soft delete) a review
     */
    public function destroy(EmployeeReview $review)
    {
        try {
            $posId = auth()->user()->POSID;
            
            // Ensure review belongs to current POSID
            if ($review->POSID != $posId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.',
                ], 403);
            }

            $review->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Review deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete review: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format review for response
     */
    private function formatReview(EmployeeReview $review)
    {
        $review->formatted_date = formatDate($review->review_date);
        $review->created_by_name = $review->creator ? $review->creator->name : 'N/A';
        $review->updated_by_name = $review->updater ? $review->updater->name : 'N/A';
        
        return $review;
    }
}
