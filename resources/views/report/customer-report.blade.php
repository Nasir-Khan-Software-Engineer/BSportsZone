@extends('layouts.main-layout')
@section('style')
@endsection
@section('content')


<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <div class="d-flex gap-2 align-items-center">
                <h3>Customer Report</h3>
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
                            <select id="customerType" class="form-control form-control-sm">
                                <option value="all">All Customer</option>
                                <option value="New Customer">New Customer</option>
                                <option value="Regular Customer">Regular Customer</option>
                                <option value="Returning Customer">Returning Customer</option>
                                <option value="Old Customer">Old Customer</option>
                                <option value="Inactive Customer">Inactive Customer</option>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="customerTypeInfoBtn" 
                                    data-toggle="tooltip" 
                                    data-placement="top"
                                    data-html="true"
                                    title="">
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
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
            <!-- Customer Report Table -->
            <div class="row">
                <div class="col-12">
                    <div id="detailReportDiv">
                        <table class="table table-bordered" id="reportTable">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" style="width: 5%;" scope="col">ID</th>
                                    <th class="text-left align-middle" style="width: 15%;" scope="col">Name</th>
                                    <th class="text-left align-middle" style="width: 10%;" scope="col">Phone</th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Total Sales</th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Total Quantity</th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Total Spending</th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Total Discount Amount</th>
                                    <th class="text-right align-middle" style="width: 10%;" scope="col">Total Adjustment Amount</th>
                                    <th class="text-center align-middle" style="width: 10%;" scope="col">Last Visited Date</th>
                                    <th class="text-center align-middle" style="width: 10%;" scope="col">Type</th>
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
@vite(['resources/js/report-script.js', 'resources/js/reports/customer-report-script.js'])
<script>
let customerReportUrls = {
    'datatable': "{{route('reports.customer.details.data')}}",
    'download': "{{route('reports.customer.details.download')}}"
};

$(document).ready(function() {
    let today = new Date();
    let lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

    $("#fromDate").val(lastMonth.toISOString().split('T')[0]);
    $("#toDate").val(today.toISOString().split('T')[0]);

    // Initialize tooltip with HTML support for customer type info
     const tooltipContent =
     '<div style="text-align: left; padding: 6px; max-width: 350px;">' +
         '<div style="margin-bottom: 6px; font-size: 11px;">' +
             '<strong>New Customer:</strong> A customer who has taken exactly one service in their lifetime, and that service was taken within the last three months.' +
         '</div>' +
         '<div style="margin-bottom: 6px; font-size: 11px;">' +
             '<strong>Regular Customer:</strong> A customer who has taken multiple services and has taken at least one service within the last three months.' +
         '</div>' +
         '<div style="margin-bottom: 6px; font-size: 11px;">' +
             '<strong>Returning Customer:</strong> A customer who has taken multiple services in their lifetime.' +
         '</div>' +
         '<div style="margin-bottom: 6px; font-size: 11px;">' +
             '<strong>Old Customer:</strong> A customer who has taken at least one service in their lifetime but has not taken any services within the last three months.' +
         '</div>' +
         '<div style="font-size: 11px;">' +
             '<strong>Inactive Customer:</strong> A customer who has not taken any services in their lifetime.' +
         '</div>' +
     '</div>';

    
    const $btn = $('#customerTypeInfoBtn');
    if ($btn.length) {
        // Remove any existing tooltip
        $btn.tooltip('dispose');
        
        // Set title attribute
        $btn.attr('title', tooltipContent);
        
        // Initialize tooltip with HTML support
        $btn.tooltip({
            html: true,
            placement: 'top',
            container: 'body'
        });
    }

    WinPos.Datatable.initDataTable("#reportTable", WinPos.Report.customer.datatableConfiguration());

    $('#filterReport').on('click', function() {
        WinPos.Datatable.getDataTable().ajax.reload(false, true);
    });

    $('#downloadPdf').on('click', function() {
        var params = getFilterParams();
        window.location = customerReportUrls.download + '?format=pdf&from_date=' + params.from_date +
            '&to_date=' + params.to_date + '&customer_type=' + params.customer_type;
    });

    $('#downloadExcel').on('click', function() {
        var params = getFilterParams();
        window.location = customerReportUrls.download + '?format=excel&from_date=' + params.from_date +
            '&to_date=' + params.to_date + '&customer_type=' + params.customer_type;
    });
});

function getFilterParams() {
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    var customerType = $('#customerType').val();

    return {
        from_date: fromDate,
        to_date: toDate,
        customer_type: customerType
    };
}
</script>
@endsection

