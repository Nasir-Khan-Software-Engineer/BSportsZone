<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Services\Report\IReportService;
use App\Exports\Reports\RevenueReport;
use App\Models\Accountinfo;

class RevenueReportController extends Controller
{
    public function __construct(IReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function revenueReportView()
    {
        return view('report.revenue');
    }

    public function getRevenueReportData(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $reportData = $this->reportService->getRevenueReportData(auth()->user()->posid, $from, $to, $start, $length, 'view');
        $reportData['draw'] = $request->input('draw');

        return response()->json($reportData);
    }

    public function downloadRevenueReport(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $reportData = $this->reportService->getRevenueReportData(auth()->user()->posid, $from, $to, $start, $length, 'download');

        $reportData['revenueData'] = $reportData['data'];
        $reportData["title"] = "Revenue Report from $from to $to";
        $reportData["fromDate"] = $from;
        $reportData["toDate"] = $to;
        $reportData["posid"] = auth()->user()->posid ?? 'N/A';
        $reportData["companyName"] = Accountinfo::where('posid', auth()->user()->posid)->value('companyName') ?? 'N/A';
        $reportData['reportGenerationDateTime'] = formatTime(Carbon::now()).' '. formatDate(Carbon::now());

        if($request->input('format', 'pdf') === 'pdf'){
            $pdf = Pdf::loadView('report.revenue-pdf', $reportData)->setPaper('a4', 'landscape');

            return $pdf->download('revenue_report_'.$from.'_'.$to.'.pdf');

        }else{
            return Excel::download(new RevenueReport($reportData), 'revenue_report_'. $from.'_'.$to.'.xlsx');
        }
    }
}

