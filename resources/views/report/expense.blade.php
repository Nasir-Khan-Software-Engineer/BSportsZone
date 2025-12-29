@extends('layouts.main-layout')
@section('style')
@endsection
@section('content')


<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <div class="d-flex gap-2 align-items-center">
                <h3>Expense Report</h3>
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
                                    <th class="text-left align-middle" style="width: 8%;" scope="col">ID</th>
                                    <th class="text-center align-middle" style="width: 12%;" scope="col">Date</th>
                                    <th class="text-left align-middle" style="width: 20%;" scope="col">Title</th>
                                    <th class="text-left align-middle" style="width: 12%;" scope="col">Category</th>
                                    <th class="text-left align-middle" style="width: 12%;" scope="col">Created By</th>
                                    <th class="text-center align-middle" style="width: 12%;" scope="col">Created At</th>
                                    <th class="text-right align-middle" style="width: 12%;" scope="col">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-right">Total</th>
                                    <th id="totalAmount">0</th>
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
@vite(['resources/js/report-script.js', 'resources/js/reports/expense-report-script.js'])
<script>
let expenseReportUrls = {
    'datatable': "{{route('reports.expense.details.data')}}",
    'download': "{{route('reports.expense.details.download')}}"
};

$(document).ready(function() {
    let today = new Date();
    let lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

    $("#fromDate").val(lastMonth.toISOString().split('T')[0]);
    $("#toDate").val(today.toISOString().split('T')[0]);

    WinPos.Datatable.initDataTable("#reportTable", WinPos.Report.expense.datatableConfiguration());

    $('#filterReport').on('click', function() {
        WinPos.Datatable.getDataTable().ajax.reload(false, true);
    });

    $('#downloadPdf').on('click', function() {
        var params = getFilterParams();
        window.location = expenseReportUrls.download + '?format=pdf&from_date=' + params.from_date +
            '&to_date=' + params.to_date;
    });

    $('#downloadExcel').on('click', function() {
        var params = getFilterParams();
        window.location = expenseReportUrls.download + '?format=excel&from_date=' + params.from_date +
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
