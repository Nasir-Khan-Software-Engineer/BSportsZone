@extends('layouts.main-layout')
@section('style')
@endsection
@section('content')


<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <div class="d-flex gap-2 align-items-center">
                <h3>Sales Report</h3>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <!-- Filter Section -->
                <div class="d-flex gap-2 align-items-center">
                    <div class="form-group mb-0">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" id="fromDate" class="form-control form-control-sm"
                                placeholder="Select date range">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" id="toDate" class="form-control form-control-sm"
                                placeholder="Select date range">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button id="filterReport" class="btn thm-btn-bg thm-btn-text-color btn-sm">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Vertical Divider -->
                <div class="vr mx-3"></div>

                <!-- Download Section -->
                <div class="d-flex gap-2 align-items-center">
                    <button id="downloadPdf" class="btn thm-btn-bg thm-btn-text-color btn-sm">
                        <i class="fa fa-file-pdf mr-1"></i> PDF Download
                    </button>
                    <button id="downloadExcel" class="btn thm-btn-bg thm-btn-text-color btn-sm">
                        <i class="fa fa-file-excel mr-1"></i> Excel Download
                    </button>
                </div>
            </div>


        </div>
        <div class="card-body p-1">
            <div class="row">
                <div class="col-12">
                    <div id="detailReportDiv">
                        <table class="table table-bordered" id="reportTable">
                            <thead>
                                <tr>
                                    <th class="text-left align-middle" style="width: 20%;" scope="col">Invoice No.</th>
                                    <th class="text-left align-middle" style="width: 10%;" scope="col">Customer</th>
                                    <th class="text-left align-middle" style="width: 10%;" scope="col">Phone</th>
                                    <th class="text-center align-middle" style="width: 10%;" scope="col">Date</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Total Amt</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Discount Amt</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Adjustment Amt
                                    </th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Payable Amt</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Paid Amt</th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Sales By</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th id="totalAmount">0</th>
                                    <th id="totalDiscount">0</th>
                                    <th id="totalAdjustmentAmt">0</th>
                                    <th id="totalPayable">0</th>
                                    <th id="totalPaid">0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="summaryReportDiv" style="display:none;">
                        <div class="card">
                            <div class="card-header bg-primary text-white py-2 px-3">
                                <h5 class="mb-0">Sales Summary Report</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="border rounded p-3 text-center">
                                            <h6 class="font-weight-bold">Total Sales Amount</h6>
                                            <div class="h4" id="summaryTotalAmount">0</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="border rounded p-3 text-center">
                                            <h6 class="font-weight-bold">Total Payable Amount</h6>
                                            <div class="h4" id="summaryTotalPayable">0</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="border rounded p-3 text-center">
                                            <h6 class="font-weight-bold">Total Paid Amount</h6>
                                            <div class="h4" id="summaryTotalPaid">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-3">
                                        <div class="border rounded p-3">
                                            <h6 class="font-weight-bold mb-2">Additional Summary (e.g. by Customer, by
                                                Date, etc.)</h6>
                                            <div id="additionalSummary"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection
@section('script')
@vite(['resources/js/report-script.js', 'resources/js/reports/sales-report-script.js'])
<script>
let salesReportUrls = {
    'datatable': "{{route('reports.sales.details.data')}}",
    'download': "{{route('reports.sales.details.download')}}"
};

$(document).ready(function() {
    let today = new Date();
    let lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

    $("#fromDate").val(lastMonth.toISOString().split('T')[0]);
    $("#toDate").val(today.toISOString().split('T')[0]);

    WinPos.Datatable.initDataTable("#reportTable", WinPos.Report.sales.datatableConfiguration());

    $('#filterReport').on('click', function() {
        WinPos.Datatable.getDataTable().ajax.reload(false, true);
    });

    $('#downloadPdf').on('click', function() {
        var params = getFilterParams();
        window.location = salesReportUrls.download + '?format=pdf&from_date=' + params.from_date +
            '&to_date=' + params.to_date;
    });

    $('#downloadExcel').on('click', function() {
        var params = getFilterParams();
        window.location = salesReportUrls.download + '?format=excel&from_date=' + params.from_date +
            '&to_date=' + params.to_date;
    });
});

function getFilterParams() {
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();

    return {
        from_date: fromDate,
        to_date: toDate
    };
}
</script>
@endsection