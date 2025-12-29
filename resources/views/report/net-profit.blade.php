@extends('layouts.main-layout')
@section('style')
@endsection
@section('content')


<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <div class="d-flex gap-2 align-items-center">
                <h3>Net Profit Report</h3>
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
            <!-- Net Profit Table -->
            <div class="row">
                <div class="col-12">
                    <div id="detailReportDiv">
                        <table class="table table-bordered" id="reportTable">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" style="width: 20%;" scope="col">Date</th>
                                    <th class="text-right align-middle" style="width: 20%;" scope="col">Total Sales Revenue</th>
                                    <th class="text-right align-middle" style="width: 20%;" scope="col">Total Expense</th>
                                    <th class="text-right align-middle" style="width: 20%;" scope="col">Net Profit</th>
                                    <th class="text-right align-middle" style="width: 20%;" scope="col">Profit Margin (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-right">Total</th>
                                    <th id="totalSalesRevenue" class="text-right">0</th>
                                    <th id="totalExpenses" class="text-right">0</th>
                                    <th id="totalNetProfit" class="text-right">0</th>
                                    <th id="totalProfitMargin" class="text-right">0%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection
@section('script')
@vite(['resources/js/report-script.js', 'resources/js/reports/net-profit-report-script.js'])
<script>
let netProfitReportUrls = {
    'datatable': "{{route('reports.net-profit.details.data')}}",
    'download': "{{route('reports.net-profit.details.download')}}"
};

$(document).ready(function() {
    let today = new Date();
    let lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

    $("#fromDate").val(lastMonth.toISOString().split('T')[0]);
    $("#toDate").val(today.toISOString().split('T')[0]);

    WinPos.Datatable.initDataTable("#reportTable", WinPos.Report.netProfit.datatableConfiguration());

    $('#filterReport').on('click', function() {
        WinPos.Datatable.getDataTable().ajax.reload(false, true);
    });

    $('#downloadPdf').on('click', function() {
        var params = getFilterParams();
        window.location = netProfitReportUrls.download + '?format=pdf&from_date=' + params.from_date +
            '&to_date=' + params.to_date;
    });

    $('#downloadExcel').on('click', function() {
        var params = getFilterParams();
        window.location = netProfitReportUrls.download + '?format=excel&from_date=' + params.from_date +
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

