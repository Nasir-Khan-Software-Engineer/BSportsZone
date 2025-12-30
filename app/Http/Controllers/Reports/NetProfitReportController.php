<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Services\Report\IReportService;
use App\Exports\Reports\NetProfitReport;
use App\Models\Accountinfo;

class NetProfitReportController extends Controller
{
    public function __construct(IReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function netProfitReportView()
    {
        return view('report.net-profit');
    }

    public function getNetProfitReportData(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $reportData = $this->reportService->getNetProfitReportData(auth()->user()->POSID, $from, $to, $start, $length, 'view');
        $reportData['draw'] = $request->input('draw');

        return response()->json($reportData);
    }

    public function downloadNetProfitReport(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $reportData = $this->reportService->getNetProfitReportData(auth()->user()->POSID, $from, $to, $start, $length, 'download');

        $reportData['netProfitData'] = $reportData['data'];

        $reportData["title"] = "Net Profit Report from $from to $to";
        $reportData["fromDate"] = $from;
        $reportData["toDate"] = $to;
        $reportData["POSID"] = auth()->user()->POSID ?? 'N/A';
        $reportData["companyName"] = Accountinfo::where('POSID', auth()->user()->POSID)->value('companyName') ?? 'N/A';
        $reportData['reportGenerationDateTime'] = formatTime(Carbon::now()).' '. formatDate(Carbon::now());

        if($request->input('format', 'pdf') === 'pdf'){
            $pdf = Pdf::loadView('report.net-profit-pdf', $reportData)->setPaper('a4', 'landscape');

            return $pdf->download('net_profit_report_'.$from.'_'.$to.'.pdf');

        }else{
            return Excel::download(new NetProfitReport($reportData), 'net_profit_report_'. $from.'_'.$to.'.xlsx');
        }
    }
}

