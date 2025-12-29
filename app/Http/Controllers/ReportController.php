<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchases;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Number;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function salesReportView()
    {
        return view('report.sales');
    }

    public function getSalesReport(Request $request)
    {
        $type = $request->input('report_type', 'detail');
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 9);

        $query = Purchases::where('posid', auth()->user()->posid);
        $totalRecord = $query->count();

        if ($from) {
            (clone $query)->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $query->with(['customer', 'payments', 'createdByUser']);

        $totalFilteredRecord = $query->count();
        $totalAmount = (clone $query)->sum('total_amount');
        $totalPayable = (clone $query)->sum('total_payable_amount');
        $totalDiscount = (clone $query)->sum('discount_amount');
        $totalPaid = (clone $query)->get()->pluck('payments')->flatten()->sum('paid_amount');

        $sales = (clone $query)->orderBy('created_at', 'desc')->skip($start)->take($length)->get();

        $sales->transform(function ($sale) {
            $sale->formattedDate = formatDate($sale->created_at);
            $sale->formattedTime = formatTime($sale->created_at);

            $sale->total_amount = str_replace('BDT', 'Tk.', Number::currency($sale->total_amount, 'BDT'));
            $sale->total_payable_amount = str_replace('BDT', 'Tk.', Number::currency($sale->total_payable_amount, 'BDT'));
            $sale->paidAmount = str_replace('BDT', 'Tk.', Number::currency($sale->payments->sum('paid_amount'), 'BDT'));
            $sale->discount_amount = str_replace('BDT', 'Tk.', Number::currency($sale->discount_amount, 'BDT'));
            
            return $sale;
        });
        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $sales->toArray(),
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'totals' => [
                'totalAmount' => $totalAmount,
                'totalPayable' => $totalPayable,
                'totalDiscountAmount' => $totalDiscount,
                'totalPaid' => $totalPaid
            ]
        ]);
    }

    public function downloadSalesReportPdf(Request $request)
    {
        $type = $request->input('report_type', 'detail');
        $from = $request->input('from_date', Carbon::now()->format('Y-m-d'));
        $to = $request->input('to_date', Carbon::now()->format('Y-m-d'));

        $query = Purchases::where('posid', auth()->user()->posid);
        $totalRecord = $query->count();

        if ($from) {
            (clone $query)->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $query->with(['customer', 'payments', 'createdByUser']);

        $totalFilteredRecord = $query->count();
        $totalAmount = (clone $query)->sum('total_amount');
        $totalPayable = (clone $query)->sum('total_payable_amount');
        $totalDiscount = (clone $query)->sum('discount_amount');
        $totalPaid = (clone $query)->get()->pluck('payments')->flatten()->sum('paid_amount');

        $sales = (clone $query)->orderBy('created_at', 'desc')->get();

        $sales->transform(function ($sale) {
            $sale->formattedDate = formatDate($sale->created_at);
            $sale->formattedTime = formatTime($sale->created_at);

             $sale->total_amount = str_replace('BDT', 'Tk.', Number::currency($sale->total_amount, 'BDT'));
            $sale->total_payable_amount = str_replace('BDT', 'Tk.', Number::currency($sale->total_payable_amount, 'BDT'));
            $sale->paidAmount = str_replace('BDT', 'Tk.', Number::currency($sale->payments->sum('paid_amount'), 'BDT'));
            $sale->discount_amount = str_replace('BDT', 'Tk.', Number::currency($sale->discount_amount, 'BDT'));

            return $sale;
        });

        $data = [
            'title' => 'Monthly Report',
            'user' => 'John Doe',
            'sales' => $sales,
            'totals' => [
                'totalAmount' => $totalAmount,
                'totalPayable' => $totalPayable,
                'totalDiscountAmount' => $totalDiscount,
                'totalPaid' => $totalPaid
            ]
        ];

        $pdf = Pdf::loadView('report.sales-pdf', $data);

        return $pdf->download('sales_report_'.$from.'_'.$to.'.pdf');
    }

    public function downloadSalesReportExcel(Request $request)
    {
        // Implement Excel download logic here
        // ...existing code...
        return response()->json(['status' => 'success', 'message' => 'Excel download not implemented']);
    }
}
