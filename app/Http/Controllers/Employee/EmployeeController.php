<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use App\Models\Attendance;
use App\Models\EmployeeReview;
use App\Models\Purchase_items;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index()
    {
        $posId = auth()->user()->posid;
        
        $employees = Employee::where('posid', $posId)
            ->with(['designation', 'creator'])
            ->orderBy('id', 'desc')
            ->get();

        $designations = EmployeeDesignation::where('posid', $posId)
            ->orderBy('name')
            ->get();

        // Get today's attendance for all employees
        $today = Carbon::today()->format('Y-m-d');
        $todayAttendances = Attendance::where('posid', $posId)
            ->where('attendance_date', $today)
            ->get()
            ->keyBy('employee_id');

        foreach ($employees as $employee) {
            $employee->formattedDate = formatDate($employee->created_at);
            $employee->formattedTime = formatTime($employee->created_at);
            $employee->userName = $employee->creator ? $employee->creator->name : 'N/A';
            $employee->designationName = $employee->designation ? $employee->designation->name : 'N/A';
            $employee->formattedDob = formatDate($employee->date_of_birth);
            $employee->formattedHireDate = formatDate($employee->hire_date);
            
            // Phone masking
            if (!hasAccess('show_phone')) {
                $employee->formattedPhone = maskPhoneNumber($employee->phone ?? '');
            } else {
                $employee->formattedPhone = $employee->phone ?? '-';
            }
            
            // Get today's attendance using helper function
            $todayAttendance = $this->getTodayAttendance($employee->id, $posId);
            if ($todayAttendance) {
                $employee->todayAttendanceStatus = $todayAttendance->status ?? 'Pending';
                $employee->todayAttendanceId = $todayAttendance->id;
            } else {
                $employee->todayAttendanceStatus = 'Pending';
                $employee->todayAttendanceId = null;
            }
        }

        return view("employee/index", [
            'employees' => $employees,
            'designations' => $designations
        ]);
    }

    /**
     * Get today's attendance for an employee
     * 
     * @param int $employeeId
     * @param int $posId
     * @return Attendance|null
     */
    private function getTodayAttendance($employeeId, $posId)
    {
        $today = Carbon::today()->format('Y-m-d');
        
        return Attendance::where('posid', $posId)
            ->where('employee_id', $employeeId)
            ->where('attendance_date', $today)
            ->first();
    }

    public function create()
    {
        $posId = auth()->user()->posid;
        $designations = EmployeeDesignation::where('posid', $posId)
            ->orderBy('name')
            ->get();
        return view("employee/create", ['designations' => $designations]);
    }

    public function store(Request $request)
    {
        try {
            $posId = auth()->user()->posid;
            
            $request->validate([
                'employeeName' => 'required|string|min:2|max:100',
                'phone' => 'required|string|max:20',
                'dateOfBirth' => 'required|date',
                'gender' => 'required|in:Male,Female,Other',
                'designationId' => [
                    'required',
                    'exists:employee_designations,id',
                    function ($attribute, $value, $fail) use ($posId) {
                        $designation = EmployeeDesignation::where('id', $value)
                            ->where('posid', $posId)
                            ->first();
                        if (!$designation) {
                            $fail('The selected designation is invalid.');
                        }
                    },
                ],
                'jobTitle' => 'required|string|min:2|max:100',
                'hireDate' => 'required|date',
                'status' => 'required|in:Active,Inactive',
                'note' => 'nullable|string|max:1000',
            ]);

            $employee = new Employee;
            $employee->posid = $posId;
            $employee->name = ucwords($request->employeeName);
            $employee->phone = $request->phone;
            $employee->date_of_birth = $request->dateOfBirth;
            $employee->gender = $request->gender;
            $employee->designation_id = $request->designationId;
            $employee->job_title = ucwords($request->jobTitle);
            $employee->hire_date = $request->hireDate;
            $employee->status = $request->status;
            $employee->note = $request->note;
            $employee->created_by = auth()->user()->id;
            $employee->save();

            $employee->load(['designation', 'creator']);
            $employee->createdBy = auth()->user()->name;
            $employee->formattedDate = formatDate($employee->created_at);
            $employee->formattedTime = formatTime($employee->created_at);
            $employee->designationName = $employee->designation ? $employee->designation->name : 'N/A';
            $employee->formattedDob = formatDate($employee->date_of_birth);
            $employee->formattedHireDate = formatDate($employee->hire_date);
            $employee->note = $employee->note ?? '';
            
            // Phone masking
            if (!hasAccess('show_phone')) {
                $employee->formattedPhone = maskPhoneNumber($employee->phone ?? '');
            } else {
                $employee->formattedPhone = $employee->phone ?? '-';
            }

            // Get today's attendance status
            
            $employee->todayAttendanceStatus = 'Pending';
            $employee->todayAttendanceId = null;
            

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Employee created successfully.',
                    'employee' => $employee,
                ]
            );
        } catch (ValidationException $exception) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => '',
                    'errors' => $exception->validator->errors(),
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

    public function edit(Employee $employee)
    {
        $posId = auth()->user()->posid;
        
        // Ensure employee belongs to current posid
        if ($employee->posid != $posId) {
            abort(403, 'Unauthorized access.');
        }
        
        $designations = EmployeeDesignation::where('posid', $posId)
            ->orderBy('name')
            ->get();
        return view("employee/create", [
            'designations' => $designations,
            'employee' => $employee
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        try {
            $posId = auth()->user()->posid;
            
            // Ensure employee belongs to current posid
            if ($employee->posid != $posId) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Unauthorized access.',
                    ]
                );
            }
            
            $request->validate([
                'employeeName' => 'required|string|min:2|max:100',
                'phone' => 'required|string|max:20',
                'dateOfBirth' => 'required|date',
                'gender' => 'required|in:Male,Female,Other',
                'designationId' => [
                    'required',
                    'exists:employee_designations,id',
                    function ($attribute, $value, $fail) use ($posId) {
                        $designation = EmployeeDesignation::where('id', $value)
                            ->where('posid', $posId)
                            ->first();
                        if (!$designation) {
                            $fail('The selected designation is invalid.');
                        }
                    },
                ],
                'jobTitle' => 'required|string|min:2|max:100',
                'hireDate' => 'required|date',
                'status' => 'required|in:Active,Inactive',
                'note' => 'nullable|string|max:1000',
                'employeeID' => 'required',
            ]);

            $employee->name = ucwords($request->employeeName);
            $employee->phone = $request->phone;
            $employee->date_of_birth = $request->dateOfBirth;
            $employee->gender = $request->gender;
            $employee->designation_id = $request->designationId;
            $employee->job_title = ucwords($request->jobTitle);
            $employee->hire_date = $request->hireDate;
            $employee->status = $request->status;
            $employee->note = $request->note;
            $employee->updated_by = auth()->user()->id;
            $employee->update();

            $employee->load(['designation', 'creator']);
            $employee->createdBy = $employee->creator ? $employee->creator->name : 'N/A';
            $employee->formattedDate = formatDate($employee->created_at);
            $employee->formattedTime = formatTime($employee->created_at);
            $employee->designationName = $employee->designation ? $employee->designation->name : 'N/A';
            $employee->formattedDob = formatDate($employee->date_of_birth);
            $employee->formattedHireDate = formatDate($employee->hire_date);
            $employee->note = $employee->note ?? '';
            
            // Phone masking
            if (!hasAccess('show_phone')) {
                $employee->formattedPhone = maskPhoneNumber($employee->phone ?? '');
            } else {
                $employee->formattedPhone = $employee->phone ?? '-';
            }

            // Get today's attendance status
            $todayAttendance = $this->getTodayAttendance($employee->id, $posId);
            if ($todayAttendance) {
                $employee->todayAttendanceStatus = $todayAttendance->status ?? 'Pending';
                $employee->todayAttendanceId = $todayAttendance->id;
            } else {
                $employee->todayAttendanceStatus = 'Pending';
                $employee->todayAttendanceId = null;
            }

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Employee updated successfully.',
                    'employee' => $employee,
                ]
            );
        } catch (ValidationException $exception) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => '',
                    'errors' => $exception->validator->errors(),
                ]
            );
        } catch (Exception $exception) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Something went wrong, please try later.',
                ]
            );
        }
    }

    public function show(Employee $employee)
    {
        $posId = auth()->user()->posid;
        
        // Ensure employee belongs to current posid
        if ($employee->posid != $posId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access.',
            ], 403);
        }
        
        $employee->load(['designation', 'creator', 'updater']);
        
        $employee->formattedDate = formatDate($employee->created_at);
        $employee->formattedTime = formatTime($employee->created_at);
        $employee->formattedUpdatedDate = $employee->updated_at ? formatDate($employee->updated_at) : 'N/A';
        $employee->formattedUpdatedTime = $employee->updated_at ? formatTime($employee->updated_at) : 'N/A';
        $employee->createdByName = $employee->creator ? $employee->creator->name : 'N/A';
        $employee->updatedByName = $employee->updater ? $employee->updater->name : 'N/A';
        $employee->designationName = $employee->designation ? $employee->designation->name : 'N/A';
        $employee->formattedDob = formatDate($employee->date_of_birth);
        $employee->formattedHireDate = formatDate($employee->hire_date);
        
        // Phone masking
        if (!hasAccess('show_phone')) {
            $employee->formattedPhone = maskPhoneNumber($employee->phone ?? '');
        } else {
            $employee->formattedPhone = $employee->phone ?? '-';
        }

        return response()->json([
            'status' => 'success',
            'employee' => $employee,
        ]);
    }

    public function details(Employee $employee)
    {
        $posId = auth()->user()->posid;
        
        // Ensure employee belongs to current posid
        if ($employee->posid != $posId) {
            abort(403, 'Unauthorized access.');
        }
        
        $employee->load(['designation', 'creator', 'updater']);
        
        // Format employee data
        $employee->formattedDate = formatDate($employee->created_at);
        $employee->formattedTime = formatTime($employee->created_at);
        $employee->formattedUpdatedDate = $employee->updated_at ? formatDate($employee->updated_at) : null;
        $employee->formattedUpdatedTime = $employee->updated_at ? formatTime($employee->updated_at) : null;
        $employee->createdByName = $employee->creator ? $employee->creator->name : 'N/A';
        $employee->updatedByName = $employee->updater ? $employee->updater->name : 'N/A';
        $employee->designationName = $employee->designation ? $employee->designation->name : 'N/A';
        $employee->formattedDob = formatDate($employee->date_of_birth);
        $employee->formattedHireDate = formatDate($employee->hire_date);
        
        // Phone masking
        if (!hasAccess('show_phone')) {
            $employee->formattedPhone = maskPhoneNumber($employee->phone ?? '');
        } else {
            $employee->formattedPhone = $employee->phone ?? '-';
        }

        // Calculate attendance metrics (all-time)
        $attendances = Attendance::where('posid', $posId)
            ->where('employee_id', $employee->id)
            ->whereNotNull('status')
            ->get();

        $totalPresent = $attendances->where('status', 'Present')->count();
        $totalAbsent = $attendances->where('status', 'Absent')->count();
        $totalLeave = $attendances->where('status', 'Leave')->count();
        $totalDays = $attendances->count();

        $presentPercentage = $totalDays > 0 ? round(($totalPresent / $totalDays) * 100, 2) : 0;
        $absentPercentage = $totalDays > 0 ? round(($totalAbsent / $totalDays) * 100, 2) : 0;
        $leavePercentage = $totalDays > 0 ? round(($totalLeave / $totalDays) * 100, 2) : 0;

        $attendanceMetrics = [
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'total_leave' => $totalLeave,
            'total_days' => $totalDays,
            'present_percentage' => $presentPercentage,
            'absent_percentage' => $absentPercentage,
            'leave_percentage' => $leavePercentage,
        ];

        // Get last 30 days attendance
        $thirtyDaysAgo = Carbon::now()->subDays(30)->startOfDay();
        $today = Carbon::today();
        $last30DaysAttendance = Attendance::where('posid', $posId)
            ->where('employee_id', $employee->id)
            ->where('attendance_date', '>=', $thirtyDaysAgo)
            ->orderBy('attendance_date', 'desc')
            ->get();

        // Check if today's attendance exists
        $todayAttendance = $last30DaysAttendance->first(function($attendance) use ($today) {
            return $attendance->attendance_date->format('Y-m-d') === $today->format('Y-m-d');
        });
        $hasTodayAttendance = $todayAttendance !== null;

        // If today's attendance doesn't exist, create a pending demo row
        if (!$hasTodayAttendance) {
            $pendingAttendance = (object) [
                'id' => null, // No ID since it doesn't exist in DB
                'employee_id' => $employee->id,
                'attendance_date' => $today,
                'status' => null, // Pending
                'leave_type' => null,
                'note' => null,
                'check_in_time' => null,
                'check_out_time' => null,
                'formatted_date' => formatDate($today),
                'formatted_check_in' => '-',
                'formatted_check_out' => '-',
                'is_today' => true,
                'is_pending' => true,
            ];
            
            // Add to collection at the beginning (most recent)
            $last30DaysAttendance->prepend($pendingAttendance);
        }

        foreach ($last30DaysAttendance as $attendance) {
            if (!isset($attendance->formatted_date)) {
                $attendance->formatted_date = formatDate($attendance->attendance_date);
            }
            if (!isset($attendance->formatted_check_in)) {
                $attendance->formatted_check_in = $attendance->check_in_time ? formatDateAndTime($attendance->check_in_time) : '-';
            }
            if (!isset($attendance->formatted_check_out)) {
                $attendance->formatted_check_out = $attendance->check_out_time ? formatDateAndTime($attendance->check_out_time) : '-';
            }
            
            // Mark today's attendance
            if (isset($attendance->is_today) && $attendance->is_today) {
                // Already marked as today
            } elseif ($attendance->attendance_date instanceof \Carbon\Carbon && $attendance->attendance_date->isToday()) {
                $attendance->is_today = true;
            } elseif (is_string($attendance->attendance_date) && $attendance->attendance_date === $today->format('Y-m-d')) {
                $attendance->is_today = true;
            }
        }

        // Get all leaves
        $allLeaves = Attendance::where('posid', $posId)
            ->where('employee_id', $employee->id)
            ->where('status', 'Leave')
            ->orderBy('attendance_date', 'desc')
            ->get();

        foreach ($allLeaves as $leave) {
            $leave->formatted_date = formatDate($leave->attendance_date);
        }

        // Get all absences
        $allAbsences = Attendance::where('posid', $posId)
            ->where('employee_id', $employee->id)
            ->where('status', 'Absent')
            ->orderBy('attendance_date', 'desc')
            ->get();

        foreach ($allAbsences as $absence) {
            $absence->formatted_date = formatDate($absence->attendance_date);
        }

        // Get employee reviews
        $reviews = EmployeeReview::where('posid', $posId)
            ->where('employee_id', $employee->id)
            ->orderBy('review_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($reviews as $review) {
            $review->formatted_date = formatDate($review->review_date);
            $review->created_by_name = $review->creator ? $review->creator->name : 'N/A';
        }

        // Calculate review metrics
        $totalReviews = $reviews->count();
        $positiveReviews = $reviews->where('status', 'positive')->count();
        $negativeReviews = $reviews->where('status', 'negative')->count();
        $warningReviews = $reviews->where('status', 'warning')->count();

        $positivePercentage = $totalReviews > 0 ? round(($positiveReviews / $totalReviews) * 100, 2) : 0;
        $negativePercentage = $totalReviews > 0 ? round(($negativeReviews / $totalReviews) * 100, 2) : 0;
        $warningPercentage = $totalReviews > 0 ? round(($warningReviews / $totalReviews) * 100, 2) : 0;

        $reviewMetrics = [
            'total_reviews' => $totalReviews,
            'positive_count' => $positiveReviews,
            'negative_count' => $negativeReviews,
            'warning_count' => $warningReviews,
            'positive_percentage' => $positivePercentage,
            'negative_percentage' => $negativePercentage,
            'warning_percentage' => $warningPercentage,
        ];

        // Calculate beautician service metrics (only for beauticians)
        $beauticianServiceMetrics = null;
        $isBeautician = $employee->designation && $employee->designation->name === 'Beautician';
        
        if ($isBeautician) {
            $today = Carbon::today();
            
            // Get today's service count
            $todayServicesCount = Purchase_items::where('posid', $posId)
                ->where('beautician_id', $employee->id)
                ->whereDate('created_at', $today)
                ->count();
            
            // Get all services for this beautician
            $allServices = Purchase_items::where('posid', $posId)
                ->where('beautician_id', $employee->id)
                ->get();
            
            // Calculate total services
            $totalServices = $allServices->count();
            
            // Calculate number of unique days with services
            // $uniqueServiceDays = $allServices->groupBy(function($service) {
            //     return Carbon::parse($service->created_at)->format('Y-m-d');
            // })->count();
            
            // Calculate average services per day
            $averageServicesPerDay = $totalPresent > 0 ? round($totalServices / $totalPresent, 2) : 0;
            
            $beauticianServiceMetrics = [
                'today_services' => $todayServicesCount,
                'average_services_per_day' => $averageServicesPerDay,
                'total_services' => $totalServices,
            ];
        }

        return view('employee/details', [
            'employee' => $employee,
            'attendanceMetrics' => $attendanceMetrics,
            'last30DaysAttendance' => $last30DaysAttendance,
            'allLeaves' => $allLeaves,
            'allAbsences' => $allAbsences,
            'reviews' => $reviews,
            'reviewMetrics' => $reviewMetrics,
            'beauticianServiceMetrics' => $beauticianServiceMetrics,
            'isBeautician' => $isBeautician,
        ]);
    }

    public function destroy(string $id)
    {
        try {
            $posId = auth()->user()->posid;
            
            $employee = Employee::where('id', $id)
                ->where('posid', $posId)
                ->first();

            if (!$employee) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Employee not found.',
                    ]
                );
            }

            $employee->delete();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Employee deleted successfully.',
                ]
            );
        } catch (Exception $exception) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Something went wrong, please try later.',
                ]
            );
        }
    }
}
