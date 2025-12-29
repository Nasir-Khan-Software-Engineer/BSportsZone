<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Purchases;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Services\Report\IReportService;
use App\Exports\Reports\SalesDetailsReport;
use App\Models\Accountinfo;

class SalesReportController extends Controller
{
    public function __construct(IReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function salesReportDetailsView()
    {
        return view('report.sales');
    }

    public function getSalesReportDetailsData(Request $request)
    {
        $type = $request->input('report_type', 'detail');
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $salesData = $this->reportService->getSalesDetailsReportData(auth()->user()->posid, $from, $to, $start, $length, 'view');
        $sales = $salesData['draw'] = $request->input('draw');

        return response()->json($salesData);
    }

    public function downloadSalesReportDetails(Request $request)
    {
        $type = $request->input('report_type', 'detail');
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $salesData = $this->reportService->getSalesDetailsReportData(auth()->user()->posid, $from, $to, $start, $length, 'download');

        $salesData['sales'] = $salesData['data'];
        $salesData["title"] = "Sales Report from $from to $to";
        $salesData["fromDate"] = $from;
        $salesData["toDate"] = $to;
        $salesData["posid"] = auth()->user()->posid ?? 'N/A';
        $salesData["companyName"] = Accountinfo::where('posid', auth()->user()->posid)->value('companyName') ?? 'N/A';

        if($request->input('format', 'pdf') === 'pdf'){
            $pdf = Pdf::loadView('report.sales-pdf', $salesData)->setPaper('a4', 'landscape');

            return $pdf->download('sales_report_'.$from.'_'.$to.'.pdf');

        }else{
            return Excel::download(new SalesDetailsReport($salesData), 'sales_report_'. $from.'_'.$to.'.xlsx');
        }
    }
}
