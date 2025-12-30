<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Services\Report\IReportService;
use App\Exports\Reports\CustomerReport;
use App\Models\Accountinfo;

class CustomerReportController extends Controller
{
    public function __construct(IReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function customerReportView()
    {
        return view('report.customer-report');
    }

    public function getCustomerReportData(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $customerType = $request->input('customer_type', 'all');
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $reportData = $this->reportService->getCustomerReportData(auth()->user()->POSID, $from, $to, $customerType, $start, $length, 'view');
        $reportData['draw'] = $request->input('draw');

        return response()->json($reportData);
    }

    public function downloadCustomerReport(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $customerType = $request->input('customer_type', 'all');
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $reportData = $this->reportService->getCustomerReportData(auth()->user()->POSID, $from, $to, $customerType, $start, $length, 'download');

        $reportData['customerData'] = $reportData['data'];
        $reportData["title"] = "Customer Report from $from to $to";
        $reportData["fromDate"] = $from;
        $reportData["toDate"] = $to;
        $reportData["customerType"] = $customerType;
        $reportData["POSID"] = auth()->user()->POSID ?? 'N/A';
        $reportData["companyName"] = Accountinfo::where('POSID', auth()->user()->POSID)->value('companyName') ?? 'N/A';
        $reportData['reportGenerationDateTime'] = formatTime(Carbon::now()).' '. formatDate(Carbon::now());

        if($request->input('format', 'pdf') === 'pdf'){
            $pdf = Pdf::loadView('report.customer-report-pdf', $reportData)->setPaper('a4', 'landscape');

            return $pdf->download('customer_report_'.$from.'_'.$to.'.pdf');

        }else{
            return Excel::download(new CustomerReport($reportData), 'customer_report_'. $from.'_'.$to.'.xlsx');
        }
    }
}

