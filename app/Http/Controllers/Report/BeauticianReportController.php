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
use App\Models\Purchase_items;
use App\Exports\Reports\BeauticianReport;

class BeauticianReportController extends Controller
{
    public function beauticianReportView()
    {
        return view('reports.beautician.details');
    }

    public function getBeauticianReportData(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $posId = auth()->user()->posid;

        // Get all unique attendance dates in the date range (working days = days with any attendance activity)
        // $workingDays = Attendance::where('posid', $posId)
        //     ->whereBetween('attendance_date', [$from, $to])
        //     ->distinct()
        //     ->pluck('attendance_date')
        //     ->map(function($date) {
        //         return Carbon::parse($date)->format('Y-m-d');
        //     })
        //     ->unique()
        //     ->count();

        // Get beautician designation
        $beauticianDesignation = EmployeeDesignation::where('posid', $posId)
            ->where('name', 'Beautician')
            ->first();

        // Get beauticians only
        $beauticiansQuery = Employee::where('posid', $posId);

        if ($beauticianDesignation) {
            $beauticiansQuery->where('designation_id', $beauticianDesignation->id);
        } else {
            // If no beautician designation exists, return empty result
            $beauticiansQuery->whereRaw('1 = 0');
        }

        $beauticians = $beauticiansQuery->get();

        // Process each beautician
        $beauticianData = $beauticians->map(function($beautician) use ($from, $to, $posId) {
            // Get attendance records within date range
            $attendances = Attendance::where('posid', $posId)
                ->where('employee_id', $beautician->id)
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
            $reviews = EmployeeReview::where('posid', $posId)
                ->where('employee_id', $beautician->id)
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

            // Get total services within date range
            $totalServices = Purchase_items::where('posid', $posId)
                ->where('beautician_id', $beautician->id)
                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                ->count();

            // Calculate average services per day based on total working days
            $avgServicesPerDay = $workingDays > 0 ? round($totalServices / $workingDays, 2) : 0;

            // Phone masking
            $phone = $beautician->phone ?? '';
            if (!hasAccess('show_phone')) {
                $formattedPhone = maskPhoneNumber($phone);
            } else {
                $formattedPhone = $phone;
            }

            return [
                'employee_name' => $beautician->name ?? '-',
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
                'total_services' => $totalServices,
                'avg_services_per_day' => $avgServicesPerDay,
            ];
        });

        // Sort by employee name
        $beauticianData = $beauticianData->sortBy('employee_name')->values();

        $totalRecord = $beauticianData->count();
        $totalFilteredRecord = $totalRecord;

        // Pagination
        $summaryData = $beauticianData->slice($start, $length)->values();

        return response()->json([
            'data' => $summaryData,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'draw' => $request->input('draw', 1),
        ]);
    }

    public function downloadBeauticianReport(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));

        $posId = auth()->user()->posid;

        // Get all unique attendance dates in the date range (working days = days with any attendance activity)
        $workingDays = Attendance::where('posid', $posId)
            ->whereBetween('attendance_date', [$from, $to])
            ->distinct()
            ->pluck('attendance_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->count();

        // Get beautician designation
        $beauticianDesignation = EmployeeDesignation::where('posid', $posId)
            ->where('name', 'Beautician')
            ->first();

        // Get beauticians only
        $beauticiansQuery = Employee::where('posid', $posId);

        if ($beauticianDesignation) {
            $beauticiansQuery->where('designation_id', $beauticianDesignation->id);
        } else {
            // If no beautician designation exists, return empty result
            $beauticiansQuery->whereRaw('1 = 0');
        }

        $beauticians = $beauticiansQuery->get();

        // Process each beautician (same logic as getBeauticianReportData)
        $beauticianData = $beauticians->map(function($beautician) use ($from, $to, $posId, $workingDays) {
            // Get attendance records within date range
            $attendances = Attendance::where('posid', $posId)
                ->where('employee_id', $beautician->id)
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
            $reviews = EmployeeReview::where('posid', $posId)
                ->where('employee_id', $beautician->id)
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

            // Get total services within date range
            $totalServices = Purchase_items::where('posid', $posId)
                ->where('beautician_id', $beautician->id)
                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                ->count();

            // Calculate average services per day based on total working days
            $avgServicesPerDay = $workingDays > 0 ? round($totalServices / $workingDays, 2) : 0;

            // Phone masking
            $phone = $beautician->phone ?? '';
            if (!hasAccess('show_phone')) {
                $formattedPhone = maskPhoneNumber($phone);
            } else {
                $formattedPhone = $phone;
            }

            return [
                'employee_name' => $beautician->name ?? '-',
                'phone' => $formattedPhone ?: '-',
                'total_working_days' => $workingDays,
                'present_display' => $presentPercentage . '% (' . $presentCount . ')',
                'absent_display' => $absentPercentage . '% (' . $absentCount . ')',
                'total_leave' => $leaveCount,
                'total_review' => $totalReview,
                'positive_display' => $positivePercentage . '% (' . $positiveCount . ')',
                'warning_display' => $warningPercentage . '% (' . $warningCount . ')',
                'negative_display' => $negativePercentage . '% (' . $negativeCount . ')',
                'total_services' => $totalServices,
                'avg_services_per_day' => $avgServicesPerDay,
            ];
        });

        // Sort by employee name
        $beauticianData = $beauticianData->sortBy('employee_name')->values();

        $reportData = [
            'beauticianData' => $beauticianData,
            'title' => "Beautician Performance Report from $from to $to",
            'fromDate' => $from,
            'toDate' => $to,
            'posid' => $posId ?? 'N/A',
            'companyName' => Accountinfo::where('posid', $posId)->value('companyName') ?? 'N/A',
            'reportGenerationDateTime' => formatTime(Carbon::now()) . ' ' . formatDate(Carbon::now()),
        ];

        if ($request->input('format', 'pdf') === 'pdf') {
            $pdf = Pdf::loadView('reports.beautician.details-pdf', $reportData)->setPaper('a4', 'landscape');
            return $pdf->download('beautician_performance_report_' . $from . '_' . $to . '.pdf');
        } else {
            return Excel::download(new BeauticianReport($reportData), 'beautician_performance_report_' . $from . '_' . $to . '.xlsx');
        }
    }
}

