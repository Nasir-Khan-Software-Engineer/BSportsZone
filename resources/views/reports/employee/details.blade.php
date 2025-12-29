@extends('layouts.main-layout')
@section('style')
@endsection
@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <div class="d-flex gap-2 align-items-center">
                <h3>Employee Report</h3>
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
                        <div class="input-group input-group-sm">
                            <select id="designationId" class="form-control form-control-sm">
                                <option value="all">All Designations</option>
                                @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                @endforeach
                            </select>
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
            <!-- Employee Report Table -->
            <div class="row">
                <div class="col-12">
                    <div id="detailReportDiv">
                        <table class="table table-bordered" id="reportTable">
                            <thead>
                                <tr>
                                    <th class="text-left align-middle" style="width: 12%;" scope="col">Employee Name</th>
                                    <th class="text-left align-middle" style="width: 10%;" scope="col">Designation</th>
                                    <th class="text-left align-middle" style="width: 8%;" scope="col">Phone</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">
                                        Total Working Days
                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1" id="workingDaysInfoBtn" 
                                            data-toggle="tooltip" 
                                            data-placement="top"
                                            data-html="true"
                                            title=""
                                            style="font-size: 12px; vertical-align: middle;">
                                            <i class="fa fa-info-circle"></i>
                                        </button>
                                    </th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Present</th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Absent</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Total Leave</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Total Review</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Positive Review</th>
                                    <th class="text-right align-middle" style="width: 8%;" scope="col">Warning Review</th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Negative Review</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
@vite(['resources/js/report-script.js', 'resources/js/reports/employee-report-script.js'])
<script>
let employeeReportUrls = {
    'datatable': "{{route('reports.employee.details.data')}}",
    'download': "{{route('reports.employee.details.download')}}"
};

$(document).ready(function() {
    let today = new Date();
    let lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

    $("#fromDate").val(lastMonth.toISOString().split('T')[0]);
    $("#toDate").val(today.toISOString().split('T')[0]);

    // Initialize tooltip with HTML support for working days info
    const workingDaysTooltipContent =
        '<div style="text-align: left; padding: 6px; max-width: 350px;">' +
            '<div style="font-size: 11px;">' +
                '<strong>Working Days Calculation:</strong> Only days with attendance activity (Present, Absent, or Leave) are counted as working days. Days with no attendance records are considered holidays and excluded from the calculation.' +
            '</div>' +
        '</div>';

    const $workingDaysBtn = $('#workingDaysInfoBtn');
    if ($workingDaysBtn.length) {
        // Remove any existing tooltip
        $workingDaysBtn.tooltip('dispose');
        
        // Set title attribute
        $workingDaysBtn.attr('title', workingDaysTooltipContent);
        
        // Initialize tooltip with HTML support
        $workingDaysBtn.tooltip({
            html: true,
            placement: 'top',
            container: 'body'
        });
    }

    WinPos.Datatable.initDataTable("#reportTable", WinPos.Report.employee.datatableConfiguration());

    $('#filterReport').on('click', function() {
        WinPos.Datatable.getDataTable().ajax.reload(false, true);
    });

    $('#downloadPdf').on('click', function() {
        var params = getFilterParams();
        window.location = employeeReportUrls.download + '?format=pdf&from_date=' + params.from_date +
            '&to_date=' + params.to_date + '&designation_id=' + params.designation_id;
    });

    $('#downloadExcel').on('click', function() {
        var params = getFilterParams();
        window.location = employeeReportUrls.download + '?format=excel&from_date=' + params.from_date +
            '&to_date=' + params.to_date + '&designation_id=' + params.designation_id;
    });
});

function getFilterParams() {
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var designationId = $('#designationId').val();

    return {
        from_date: fromDate,
        to_date: toDate,
        designation_id: designationId
    };
}
</script>
@endsection

