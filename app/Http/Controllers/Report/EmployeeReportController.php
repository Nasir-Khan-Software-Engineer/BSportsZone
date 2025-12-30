<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use App\Models\Attendance;
use App\Models\EmployeeReview;
use App\Models\Accountinfo;
use App\Exports\Reports\EmployeeReport;

class EmployeeReportController extends Controller
{
    public function employeeReportView()
    {
        $designations = EmployeeDesignation::where('POSID', auth()->user()->POSID)->get();
        return view('reports.employee.details', compact('designations'));
    }

    public function getEmployeeReportData(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $designationId = $request->input('designation_id', 'all');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $posId = auth()->user()->POSID;

        // Get all unique attendance dates in the date range (working days = days with any attendance activity)
        // $workingDays = Attendance::where('POSID', $posId)
        //     ->whereBetween('attendance_date', [$from, $to])
        //     ->distinct()
        //     ->pluck('attendance_date')
        //     ->map(function($date) {
        //         return Carbon::parse($date)->format('Y-m-d');
        //     })
        //     ->unique()
        //     ->count();

        // Get employees filtered by designation
        $employeesQuery = Employee::where('POSID', $posId)
            ->with('designation');

        if ($designationId !== 'all') {
            $employeesQuery->where('designation_id', $designationId);
        }

        $employees = $employeesQuery->get();

        // Process each employee
        $employeeData = $employees->map(function($employee) use ($from, $to, $posId) {
            // Get attendance records within date range
            $attendances = Attendance::where('POSID', $posId)
                ->where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$from, $to])
                ->get();

            // Count attendance statuses
            $presentCount = $attendances->where('status', 'Present')->count();
            $absentCount = $attendances->where('status', 'Absent')->count();
            $leaveCount = $attendances->where('status', 'Leave')->count();

            $workingDays = $presentCount + $absentCount + $leaveCount;

            // Calculate percentages
            $presentPercentage = $workingDays > 0 ? round(($presentCount / $workingDays) * 100, 2) : 0;
            $absentPercentage = $workingDays > 0 ? round(($absentCount / $workingDays) * 100, 2) : 0;

            // Get reviews within date range
            $reviews = EmployeeReview::where('POSID', $posId)
                ->where('employee_id', $employee->id)
                ->whereBetween('review_date', [$from, $to])
                ->get();

            $positiveCount = $reviews->where('status', 'positive')->count();
            $warningCount = $reviews->where('status', 'warning')->count();
            $negativeCount = $reviews->where('status', 'negative')->count();
            $totalReview = $positiveCount + $warningCount + $negativeCount;

            // Calculate review percentages
            $positivePercentage = $totalReview > 0 ? round(($positiveCount / $totalReview) * 100, 2) : 0;
            $warningPercentage = $totalReview > 0 ? round(($warningCount / $totalReview) * 100, 2) : 0;
            $negativePercentage = $totalReview > 0 ? round(($negativeCount / $totalReview) * 100, 2) : 0;

            // Phone masking
            $phone = $employee->phone ?? '';
            if (!hasAccess('show_phone')) {
                $formattedPhone = maskPhoneNumber($phone);
            } else {
                $formattedPhone = $phone;
            }

            return [
                'employee_name' => $employee->name ?? '-',
                'designation' => $employee->designation->name ?? '-',
                'phone' => $formattedPhone ?: '-',
                'total_working_days' => $workingDays,
                'present' => $presentCount,
                'present_percentage' => $presentPercentage,
                'present_display' => $presentPercentage . '% (' . $presentCount . ')',
                'absent' => $absentCount,
                'absent_percentage' => $absentPercentage,
                'absent_display' => $absentPercentage . '% (' . $absentCount . ')',
                'total_leave' => $leaveCount,
                'total_review' => $totalReview,
                'positive_review' => $positiveCount,
                'positive_percentage' => $positivePercentage,
                'positive_display' => $positivePercentage . '% (' . $positiveCount . ')',
                'warning_review' => $warningCount,
                'warning_percentage' => $warningPercentage,
                'warning_display' => $warningPercentage . '% (' . $warningCount . ')',
                'negative_review' => $negativeCount,
                'negative_percentage' => $negativePercentage,
                'negative_display' => $negativePercentage . '% (' . $negativeCount . ')',
            ];
        });

        // Sort by designation ascending, then by employee name
        $employeeData = $employeeData->sortBy(function($item) {
            return $item['designation'] . '|' . $item['employee_name'];
        })->values();

        $totalRecord = $employeeData->count();
        $totalFilteredRecord = $totalRecord;

        // Pagination
        $summaryData = $employeeData->slice($start, $length)->values();

        return response()->json([
            'data' => $summaryData,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'draw' => $request->input('draw', 1),
        ]);
    }

    public function downloadEmployeeReport(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $designationId = $request->input('designation_id', 'all');

        $posId = auth()->user()->POSID;

        // Get all unique attendance dates in the date range (working days = days with any attendance activity)
        $workingDays = Attendance::where('POSID', $posId)
            ->whereBetween('attendance_date', [$from, $to])
            ->distinct()
            ->pluck('attendance_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->count();

        // Get employees filtered by designation
        $employeesQuery = Employee::where('POSID', $posId)
            ->with('designation');

        if ($designationId !== 'all') {
            $employeesQuery->where('designation_id', $designationId);
        }

        $employees = $employeesQuery->get();

        // Process each employee (same logic as getEmployeeReportData)
        $employeeData = $employees->map(function($employee) use ($from, $to, $posId, $workingDays) {
            // Get attendance records within date range
            $attendances = Attendance::where('POSID', $posId)
                ->where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$from, $to])
                ->get();

            // Count attendance statuses
            $presentCount = $attendances->where('status', 'Present')->count();
            $absentCount = $attendances->where('status', 'Absent')->count();
            $leaveCount = $attendances->where('status', 'Leave')->count();

            // Calculate percentages
            $presentPercentage = $workingDays > 0 ? round(($presentCount / $workingDays) * 100, 2) : 0;
            $absentPercentage = $workingDays > 0 ? round(($absentCount / $workingDays) * 100, 2) : 0;

            // Get reviews within date range
            $reviews = EmployeeReview::where('POSID', $posId)
                ->where('employee_id', $employee->id)
                ->whereBetween('review_date', [$from, $to])
                ->get();

            $positiveCount = $reviews->where('status', 'positive')->count();
            $warningCount = $reviews->where('status', 'warning')->count();
            $negativeCount = $reviews->where('status', 'negative')->count();
            $totalReview = $positiveCount + $warningCount + $negativeCount;

            // Calculate review percentages
            $positivePercentage = $totalReview > 0 ? round(($positiveCount / $totalReview) * 100, 2) : 0;
            $warningPercentage = $totalReview > 0 ? round(($warningCount / $totalReview) * 100, 2) : 0;
            $negativePercentage = $totalReview > 0 ? round(($negativeCount / $totalReview) * 100, 2) : 0;

            // Phone masking
            $phone = $employee->phone ?? '';
            if (!hasAccess('show_phone')) {
                $formattedPhone = maskPhoneNumber($phone);
            } else {
                $formattedPhone = $phone;
            }

            return [
                'employee_name' => $employee->name ?? '-',
                'designation' => $employee->designation->name ?? '-',
                'phone' => $formattedPhone ?: '-',
                'total_working_days' => $workingDays,
                'present_display' => $presentPercentage . '% (' . $presentCount . ')',
                'absent_display' => $absentPercentage . '% (' . $absentCount . ')',
                'total_leave' => $leaveCount,
                'total_review' => $totalReview,
                'positive_display' => $positivePercentage . '% (' . $positiveCount . ')',
                'warning_display' => $warningPercentage . '% (' . $warningCount . ')',
                'negative_display' => $negativePercentage . '% (' . $negativeCount . ')',
            ];
        });

        // Sort by designation ascending, then by employee name
        $employeeData = $employeeData->sortBy(function($item) {
            return $item['designation'] . '|' . $item['employee_name'];
        })->values();

        $designationName = 'All';
        if ($designationId !== 'all') {
            $designation = EmployeeDesignation::find($designationId);
            $designationName = $designation ? $designation->name : 'All';
        }

        $reportData = [
            'employeeData' => $employeeData,
            'title' => "Employee Report from $from to $to",
            'fromDate' => $from,
            'toDate' => $to,
            'designationName' => $designationName,
            'POSID' => $posId ?? 'N/A',
            'companyName' => Accountinfo::where('POSID', $posId)->value('companyName') ?? 'N/A',
            'reportGenerationDateTime' => formatTime(Carbon::now()) . ' ' . formatDate(Carbon::now()),
        ];

        if ($request->input('format', 'pdf') === 'pdf') {
            $pdf = Pdf::loadView('reports.employee.details-pdf', $reportData)->setPaper('a4', 'landscape');
            return $pdf->download('employee_report_' . $from . '_' . $to . '.pdf');
        } else {
            return Excel::download(new EmployeeReport($reportData), 'employee_report_' . $from . '_' . $to . '.xlsx');
        }
    }
}

