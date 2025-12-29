<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    /**
     * Get attendance data for a specific date and filters
     */
    public function getAttendanceData(Request $request)
    {
        try {
            $posId = auth()->user()->posid;
            $date = $request->input('date', \Carbon\Carbon::today()->format('Y-m-d'));
            $designationId = $request->input('designation_id', null);

            // Validate date is not in the future
            $selectedDate = \Carbon\Carbon::parse($date)->startOfDay();
            $today = \Carbon\Carbon::today()->startOfDay();
            
            if ($selectedDate->gt($today)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot select a future date.',
                ], 422);
            }

            // Get all employees for the POS
            $query = Employee::where('posid', $posId)
                ->where('status', 'Active')
                ->with('designation');

            if ($designationId) {
                $query->where('designation_id', $designationId);
            }

            $employees = $query->orderBy('name')->get();

            // Get existing attendance records for the date
            $attendances = Attendance::where('posid', $posId)
                ->where('attendance_date', $date)
                ->get()
                ->keyBy('employee_id');

            $pending = [];
            $completed = [];

            foreach ($employees as $employee) {
                $attendance = $attendances->get($employee->id);
                
                $employeeData = [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'designation' => $employee->designation ? $employee->designation->name : 'N/A',
                    'designation_id' => $employee->designation_id,
                ];

                if ($attendance && $attendance->status) {
                    // Completed
                    $employeeData['attendance_id'] = $attendance->id;
                    $employeeData['status'] = $attendance->status;
                    $employeeData['leave_type'] = $attendance->leave_type;
                    $employeeData['note'] = $attendance->note;
                    $employeeData['check_in_time'] = $attendance->check_in_time ? $attendance->check_in_time->format('Y-m-d H:i:s') : null;
                    $employeeData['check_out_time'] = $attendance->check_out_time ? $attendance->check_out_time->format('Y-m-d H:i:s') : null;
                    $employeeData['formatted_check_in_time'] = $attendance->check_in_time ? formatDateAndTime($attendance->check_in_time) : null;
                    $employeeData['formatted_check_out_time'] = $attendance->check_out_time ? formatDateAndTime($attendance->check_out_time) : null;
                    $completed[] = $employeeData;
                } else {
                    // Pending
                    if ($attendance) {
                        $employeeData['attendance_id'] = $attendance->id;
                    }
                    $pending[] = $employeeData;
                }
            }

            return response()->json([
                'status' => 'success',
                'pending' => $pending,
                'completed' => $completed,
                'total' => count($employees),
                'completed_count' => count($completed),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Save or update attendance
     */
    public function saveAttendance(Request $request)
    {
        try {
            $posId = auth()->user()->posid;
            
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'attendance_date' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        $selectedDate = \Carbon\Carbon::parse($value)->startOfDay();
                        $today = \Carbon\Carbon::today()->startOfDay();
                        
                        if ($selectedDate->gt($today)) {
                            $fail('The attendance date cannot be in the future.');
                        }
                    },
                ],
                'status' => 'required|in:Present,Absent,Leave,Off',
                'leave_type' => 'nullable|string|max:100',
                'note' => 'nullable|string|max:1000',
                'check_in_time' => 'nullable|date',
                'check_out_time' => [
                    'nullable',
                    'date',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value && $request->check_in_time) {
                            $checkIn = \Carbon\Carbon::parse($request->check_in_time);
                            $checkOut = \Carbon\Carbon::parse($value);
                            
                            if ($checkIn->gt($checkOut)) {
                                $fail('Check-in time cannot be after check-out time.');
                            }
                        }
                    },
                ],
            ]);

            // Verify employee belongs to POS
            $employee = Employee::where('id', $request->employee_id)
                ->where('posid', $posId)
                ->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found.',
                ], 404);
            }

            $existingAttendance = Attendance::where('posid', $posId)
                ->where('employee_id', $request->employee_id)
                ->where('attendance_date', $request->attendance_date)
                ->first();

            $attendance = Attendance::updateOrCreate(
                [
                    'posid' => $posId,
                    'employee_id' => $request->employee_id,
                    'attendance_date' => $request->attendance_date,
                ],
                [
                    'status' => $request->status,
                    'leave_type' => $request->status === 'Leave' ? $request->leave_type : null,
                    'note' => $request->note,
                    'check_in_time' => $request->check_in_time,
                    'check_out_time' => $request->check_out_time,
                    'created_by' => $existingAttendance ? $existingAttendance->created_by : auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]
            );

            $attendance->load(['employee.designation']);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance saved successfully.',
                'attendance' => [
                    'id' => $attendance->id,
                    'employee_id' => $attendance->employee_id,
                    'name' => $attendance->employee->name,
                    'designation' => $attendance->employee->designation ? $attendance->employee->designation->name : 'N/A',
                    'status' => $attendance->status,
                    'leave_type' => $attendance->leave_type,
                    'note' => $attendance->note,
                    'check_in_time' => $attendance->check_in_time ? $attendance->check_in_time->format('Y-m-d H:i:s') : null,
                    'check_out_time' => $attendance->check_out_time ? $attendance->check_out_time->format('Y-m-d H:i:s') : null,
                    'formatted_check_in_time' => $attendance->check_in_time ? formatDateAndTime($attendance->check_in_time) : null,
                    'formatted_check_out_time' => $attendance->check_out_time ? formatDateAndTime($attendance->check_out_time) : null,
                ],
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors(),
            ], 422);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark all visible employees as present
     */
    public function markAllPresent(Request $request)
    {
        try {
            $posId = auth()->user()->posid;
            
            $request->validate([
                'date' => 'required|date',
                'employee_ids' => 'required|array',
                'employee_ids.*' => 'exists:employees,id',
            ]);

            $date = $request->date;
            $employeeIds = $request->employee_ids;
            $userId = auth()->user()->id;

            // Default check-in and check-out times
            $checkInTime = $date . ' 09:00:00';
            $checkOutTime = $date . ' 18:00:00';

            DB::beginTransaction();

            foreach ($employeeIds as $employeeId) {
                // Verify employee belongs to POS
                $employee = Employee::where('id', $employeeId)
                    ->where('posid', $posId)
                    ->first();

                if (!$employee) {
                    continue;
                }

                $attendance = Attendance::where('posid', $posId)
                    ->where('employee_id', $employeeId)
                    ->where('attendance_date', $date)
                    ->first();

                if ($attendance) {
                    $attendance->status = 'Present';
                    $attendance->check_in_time = $checkInTime;
                    $attendance->check_out_time = $checkOutTime;
                    $attendance->leave_type = null;
                    $attendance->note = null;
                    $attendance->updated_by = $userId;
                    $attendance->save();
                } else {
                    Attendance::create([
                        'posid' => $posId,
                        'employee_id' => $employeeId,
                        'attendance_date' => $date,
                        'status' => 'Present',
                        'check_in_time' => $checkInTime,
                        'check_out_time' => $checkOutTime,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'All employees marked as present successfully.',
            ]);
        } catch (ValidationException $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => '',
                'errors' => $exception->validator->errors(),
            ], 422);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Get designations for filter dropdown
     */
    public function getDesignations()
    {
        try {
            $posId = auth()->user()->posid;
            $designations = EmployeeDesignation::where('posid', $posId)
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json([
                'status' => 'success',
                'designations' => $designations,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if today's attendance is pending
     */
    public function checkTodayAttendanceStatus()
    {
        try {
            $posId = auth()->user()->posid;
            $today = \Carbon\Carbon::today()->format('Y-m-d');

            // Get all active employees for the POS
            $totalEmployees = Employee::where('posid', $posId)
                ->where('status', 'Active')
                ->count();

            // Get existing attendance records for today
            $completedCount = Attendance::where('posid', $posId)
                ->where('attendance_date', $today)
                ->whereNotNull('status')
                ->count();

            $hasPending = $totalEmployees > 0 && $completedCount < $totalEmployees;
            $isCompleted = $totalEmployees > 0 && $completedCount >= $totalEmployees;

            return response()->json([
                'status' => 'success',
                'has_pending' => $hasPending,
                'is_completed' => $isCompleted,
                'total_employees' => $totalEmployees,
                'completed_count' => $completedCount,
                'today_date' => $today, // Return today's date for frontend comparison
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
