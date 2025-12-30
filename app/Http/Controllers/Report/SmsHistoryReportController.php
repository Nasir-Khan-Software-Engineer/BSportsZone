<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\SmsHistory;
use App\Models\Accountinfo;
use App\Exports\Reports\SmsHistoryReport;

class SmsHistoryReportController extends Controller
{
    public function smsHistoryView()
    {
        return view('reports.sms.history');
    }

    public function getSmsHistoryData(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $posId = auth()->user()->POSID;

        // Build query - only date range filter (no source filter per PRD)
        $query = SmsHistory::where('POSID', $posId)
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);

        $totalRecord = $query->count();
        $totalFilteredRecord = $totalRecord;

        // Calculate totals from ALL data (not just paginated)
        $totalSmsCount = $query->sum('sms_count');
        $totalMessageLength = $query->sum('message_length');
        $unitCost = 45; // poysa (45 poysa per SMS)
        $totalCost = ($totalSmsCount * $unitCost) / 100; // Convert poysa to taka (100 poysa = 1 taka)

        // Get paginated data
        $smsHistories = $query->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        // Process data
        $smsData = $smsHistories->map(function($sms) use ($unitCost) {
            // Phone masking for to_number
            $toNumber = $sms->to_number ?? '';
            if (!hasAccess('show_phone')) {
                $formattedToNumber = maskPhoneNumber($toNumber);
            } else {
                $formattedToNumber = $toNumber;
            }

            // Calculate total cost for this row (in taka)
            $rowTotalCost = ($sms->sms_count * $unitCost) / 100;

            return [
                'date_time' => formatDate($sms->created_at) . ' ' . formatTime($sms->created_at),
                'source' => $sms->source ?? '-',
                'from_number' => $sms->from_number ?? '-',
                'to_number' => $formattedToNumber,
                'message_length' => $sms->message_length ?? 0,
                'sms_count' => $sms->sms_count ?? 0,
                'unit_cost' => $unitCost . ' poysa',
                'total_cost' => number_format($rowTotalCost, 2) . ' taka',
            ];
        });

        return response()->json([
            'data' => $smsData,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'draw' => $request->input('draw', 1),
            'totals' => [
                'totalSmsCount' => $totalSmsCount,
                'totalMessageLength' => $totalMessageLength,
                'totalCost' => number_format($totalCost, 2) . ' taka',
            ]
        ]);
    }

    public function downloadSmsHistoryReport(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));

        $posId = auth()->user()->POSID;

        // Build query
        $query = SmsHistory::where('POSID', $posId)
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);

        // Get all data for export
        $smsHistories = $query->orderBy('created_at', 'desc')->get();

        // Calculate totals
        $totalSmsCount = $smsHistories->sum('sms_count');
        $totalMessageLength = $smsHistories->sum('message_length');
        $unitCost = 45; // poysa (45 poysa per SMS)
        $totalCost = ($totalSmsCount * $unitCost) / 100; // Convert poysa to taka (100 poysa = 1 taka)

        // Process data
        $smsData = $smsHistories->map(function($sms) use ($unitCost) {
            // Phone masking for to_number
            $toNumber = $sms->to_number ?? '';
            if (!hasAccess('show_phone')) {
                $formattedToNumber = maskPhoneNumber($toNumber);
            } else {
                $formattedToNumber = $toNumber;
            }

            // Calculate total cost for this row (in taka)
            $rowTotalCost = ($sms->sms_count * $unitCost) / 100;

            return [
                'date_time' => formatDate($sms->created_at) . ' ' . formatTime($sms->created_at),
                'source' => $sms->source ?? '-',
                'from_number' => $sms->from_number ?? '-',
                'to_number' => $formattedToNumber,
                'message_length' => $sms->message_length ?? 0,
                'sms_count' => $sms->sms_count ?? 0,
                'unit_cost' => $unitCost . ' poysa',
                'total_cost' => number_format($rowTotalCost, 2) . ' taka',
            ];
        });

        $reportData = [
            'smsData' => $smsData,
            'title' => "SMS History Report from $from to $to",
            'fromDate' => $from,
            'toDate' => $to,
            'totalSmsCount' => $totalSmsCount,
            'totalMessageLength' => $totalMessageLength,
            'totalCost' => number_format($totalCost, 2) . ' taka',
            'POSID' => $posId ?? 'N/A',
            'companyName' => Accountinfo::where('POSID', $posId)->value('companyName') ?? 'N/A',
            'reportGenerationDateTime' => formatTime(Carbon::now()) . ' ' . formatDate(Carbon::now()),
        ];

        if ($request->input('format', 'pdf') === 'pdf') {
            $pdf = Pdf::loadView('reports.sms.history-pdf', $reportData)->setPaper('a4', 'landscape');
            return $pdf->download('sms_history_report_' . $from . '_' . $to . '.pdf');
        } else {
            return Excel::download(new SmsHistoryReport($reportData), 'sms_history_report_' . $from . '_' . $to . '.xlsx');
        }
    }
}

