<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Services\Report\IReportService;
use App\Exports\Reports\DiscountAdjustmentReport;
use App\Models\Accountinfo;

class DiscountAdjustmentReportController extends Controller
{
    public function __construct(IReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function discountAdjustmentReportView()
    {
        return view('report.discount-adjustment');
    }

    public function getDiscountAdjustmentReportData(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $reportData = $this->reportService->getDiscountAdjustmentReportData(auth()->user()->POSID, $from, $to, $start, $length, 'view');
        $reportData['draw'] = $request->input('draw');

        return response()->json($reportData);
    }

    public function downloadDiscountAdjustmentReport(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $reportData = $this->reportService->getDiscountAdjustmentReportData(auth()->user()->POSID, $from, $to, $start, $length, 'download');

        $reportData['summaryData'] = $reportData['data'];
        $reportData["title"] = "Discount & Adjustment Summary Report from $from to $to";
        $reportData["fromDate"] = $from;
        $reportData["toDate"] = $to;
        $reportData["POSID"] = auth()->user()->POSID ?? 'N/A';
        $reportData["companyName"] = Accountinfo::where('POSID', auth()->user()->POSID)->value('companyName') ?? 'N/A';
        $reportData['reportGenerationDateTime'] = formatTime(Carbon::now()).' '. formatDate(Carbon::now());

        if($request->input('format', 'pdf') === 'pdf'){
            $pdf = Pdf::loadView('report.discount-adjustment-pdf', $reportData)->setPaper('a4', 'landscape');

            return $pdf->download('discount_adjustment_report_'.$from.'_'.$to.'.pdf');

        }else{
            return Excel::download(new DiscountAdjustmentReport($reportData), 'discount_adjustment_report_'. $from.'_'.$to.'.xlsx');
        }
    }
}

