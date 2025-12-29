<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Services\Report\IReportService;
use App\Exports\Reports\ExpenseDetailsReport;
use App\Models\Accountinfo;

class ExpenseReportController extends Controller
{
    public function __construct(IReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function expenseReportDetailsView()
    {
        return view('report.expense');
    }

    public function getExpenseReportDetailsData(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $expenseData = $this->reportService->getExpenseDetailsReportData(auth()->user()->posid, $from, $to, $start, $length, 'view');
        $expenseData['draw'] = $request->input('draw');

        return response()->json($expenseData);
    }

    public function downloadExpenseReportDetails(Request $request)
    {
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $expenseData = $this->reportService->getExpenseDetailsReportData(auth()->user()->posid, $from, $to, $start, $length, 'download');

        $expenseData['expenses'] = $expenseData['data'];
        $expenseData["title"] = "Expense Report from $from to $to";
        $expenseData["fromDate"] = $from;
        $expenseData["toDate"] = $to;
        $expenseData["posid"] = auth()->user()->posid ?? 'N/A';
        $expenseData["companyName"] = Accountinfo::where('posid', auth()->user()->posid)->value('companyName') ?? 'N/A';
        $expenseData['reportGenerationDateTime'] = formatTime(Carbon::now()).' '. formatDate(Carbon::now());

        if($request->input('format', 'pdf') === 'pdf'){
            $pdf = Pdf::loadView('report.expense-pdf', $expenseData)->setPaper('a4', 'landscape');

            return $pdf->download('expense_report_'.$from.'_'.$to.'.pdf');

        }else{
            return Excel::download(new ExpenseDetailsReport($expenseData), 'expense_report_'. $from.'_'.$to.'.xlsx');
        }
    }
}
