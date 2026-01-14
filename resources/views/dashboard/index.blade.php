@extends('layouts.main-layout')


@section('title', 'Dashboard')

@section('style')
<style>
.custom-bg {
    background-color: #ecf1ff !important;
}

.filter-btnbtn-primary:not(:disabled):not(.disabled):active,
.filter-btn .thm-btn-bg thm-btn-text-color:not(:disabled):not(.disabled).active {
    color: #fff;
    background-color: #21409a;
    border-color: #000000;
}

.filter-button-group{
    background-color: rgb(94 29 102 / 20%);
}
.filter-button-group .btn.active{
    border: 0px;
    background-color: #5e1d66;
    color: #fff;
}
.btn:focus, .btn.focus {
    outline: none !important;
    box-shadow: none !important;
}

.pie-chart-card-header{
    background-image: linear-gradient(270deg, #5e1d66 10%, #3a4973 100%);
}

</style>
@endsection

@section('content')
<div class="view-container mb-2">

    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Dashboard</h3>
            <div class="d-flex gap-2">
                <div class="btn-group btn-group-toggle flex-wrap filter-button-group" data-toggle="buttons">
                    <label class="btn active btn-sm">
                        <input type="radio" name="options" id="filter_today" autocomplete="off" checked> Today
                    </label>
                    <label class="btn btn-sm">
                        <input type="radio" name="options" id="filter_yesterday" autocomplete="off"> Yesterday
                    </label>
                    <label class="btn btn-sm">
                        <input type="radio" name="options" id="filter_thisWeek" autocomplete="off"> This Week
                    </label>
                    <label class="btn btn-sm">
                        <input type="radio" name="options" id="filter_lastWeek" autocomplete="off"> Last Week
                    </label>
                    <label class="btn btn-sm">
                        <input type="radio" name="options" id="filter_thisMonth" autocomplete="off"> This Month
                    </label>
                    <label class="btn btn-sm">
                        <input type="radio" name="options" id="filter_lastMonth" autocomplete="off"> Last Month
                    </label>
                </div>
            </div>
        </div>
        <div class="card-body p-1">

            <div class="row g-3">
                <!-- Total Expense -->
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-2 custom-bg">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted  mb-2">Total Expense</h6>
                                <h3 id="totalExpense" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-danger fs-2">
                                <i class="bi bi-credit-card-2-back"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-2 custom-bg">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Customers</h6>
                                <h3 id="totalCustomers" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-info fs-2">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Customer -->
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-2 custom-bg">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Number of Sales</h6>
                                <h3 id="totalNumberOfSales" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-success fs-2">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Sales Amount -->
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-2 custom-bg">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Sales Amount</h6>
                                <h3 id="totalSalesAmount" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Discount Amount -->
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-2 custom-bg">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Discount Amount</h6>
                                <h3 id="discountAmount" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Discount Amount -->
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-2 custom-bg">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Adjusted Amount</h6>
                                <h3 id="adjustmentAmount" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-2">
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center text-white pie-chart-card-header">
                            <h5 class="mb-0">Total Sales Amount</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentTypePieChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center text-white pie-chart-card-header">
                            <h5 class="mb-0">Wallet Payment Amount</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="walletPaymentPieChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center text-white pie-chart-card-header">
                            <h5 class="mb-0">Customer (New vs Returning)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="customerDistributionPieChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Top 5 Tables Section -->
            <div class="row mt-3">
                <!-- Top 5 Services -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center text-white pie-chart-card-header">
                            <h5 class="mb-0">Top Services - 12 Months</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">Service Name</th>
                                            <th class="border-0 text-end">Total Sales Count</th>
                                        </tr>
                                    </thead>
                                    <tbody id="topServicesTable">
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">⏳ Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center text-white pie-chart-card-header">
                            <h5 class="mb-0">Sales vs Expenses - 12 Months</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesExpenseChart" width="500" height="150">⏳ Loading...</canvas>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
@vite(['resources/js/common-chart.js'])

<script>
$(document).ready(function() {
    fetchDashboardData('today');
    fetchDashboardFixedMetricsData("Last 12 Months");

    $('input[name="options"]').on('change', function() {
        const filterValue = $(this).attr('id').replace('filter_', '');
        fetchDashboardData(filterValue);
    });

    // Auto-open attendance modal on dashboard if attendance is pending
    checkAndAutoOpenAttendanceModal();
});

// Function to check and auto-open attendance modal
function checkAndAutoOpenAttendanceModal() {
    // Check if attendance is already completed today (localStorage flag)
    if(WinPos.Attendance && WinPos.Attendance.isAttendanceCompletedToday()) {
        // Still verify with server to update icon
        if(WinPos.Attendance.checkTodayAttendanceStatus) {
            WinPos.Attendance.checkTodayAttendanceStatus();
        }
        return; // Don't auto-open if already completed
    }

    // Check today's attendance status from server
    if(AttendanceUrls && AttendanceUrls.checkTodayStatus) {
        WinPos.Common.getAjaxCall(AttendanceUrls.checkTodayStatus, function(response) {
            if(response.status === 'success') {
                // Only proceed if there are employees
                if(response.total_employees === 0) {
                    return;
                }

                // If attendance is completed, update flag and icon, don't open
                if(response.is_completed) {
                    if(WinPos.Attendance) {
                        // Use server's today_date for consistency
                        if(response.today_date) {
                            WinPos.Attendance.setAttendanceCompletedFlag(response.today_date);
                        }
                        WinPos.Attendance.updateAttendanceButtonIcon(true);
                    }
                    return;
                }

                // Only auto-open if there are pending employees
                if(response.has_pending === true && response.total_employees > 0) {
                    // Small delay to ensure page is fully loaded
                    setTimeout(function() {
                        if(WinPos.Attendance && $('#attendanceModal').length) {
                            // Use server's today_date to ensure consistency
                            var todayDate = response.today_date || null;
                            WinPos.Attendance.initModal(todayDate);
                            $('#attendanceModal').modal('show');
                        }
                    }, 500);
                } else {
                    // No pending, update icon
                    if(WinPos.Attendance) {
                        WinPos.Attendance.updateAttendanceButtonIcon(false);
                    }
                }
            }
        });
    }
}


function fetchDashboardData(filterValue) {
    $.ajax({
        url: '/admin/dashboard/filter/data',
        method: 'POST',
        data: JSON.stringify({
            filter: filterValue
        }),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // Update numbers
            $('#totalExpense').text(response.totalExpense);
            $('#totalCustomers').text(response.totalCustomers);
            $('#totalNumberOfSales').text(response.totalNumberOfSales);
            $('#totalSalesAmount').text(response.totalSalesAmount);
            $('#discountAmount').text(response.discountAmount);
            $('#adjustmentAmount').text(response.adjustmentAmount);

            let sales = response.sales;
            let totalInWallet = Number(sales.bkash) + Number(sales.nagad) + Number(sales.rocket);

            WinPos.CommonChart.pieChart(
                "paymentTypePieChart",
                ['Cash', 'Card', 'Wallet'],
                ['#4CAF50', '#2196F3', '#00BCD4'],
                [sales.cash, sales.card, totalInWallet],
            );

            WinPos.CommonChart.pieChart(
                "walletPaymentPieChart",
                ['bKash', 'Nagad', 'Rocket'],
                ['#eb4f9aff', '#f77044ff', '#a947cfff'], // Softer/pastel brand colors
                [Number(sales.bkash), Number(sales.nagad), Number(sales.rocket)]
            );

            WinPos.CommonChart.pieChart(
                "customerDistributionPieChart",
                ['New Customers', 'Returning Customers'],
                ['#f83dc9ff', '#aa2155ff'], // lighter Blue, lighter Green
                [Number(response.newCustomers), Number(response.returningCustomers)],
                'number'
            );
        },
        error: function(xhr) {
            alert('Error loading data');
            console.error(xhr);
        }
    });
}

function fetchDashboardFixedMetricsData(filterValue) {
    $.ajax({
        url: '/admin/dashboard/fixed/metrics',
        method: 'POST',
        data: JSON.stringify({
            filter: filterValue
        }),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            updateServicesTable(response.topServices);
            renderSalesExpenseChart(response.monthlySalesAndExpense.original);
        },
        error: function(xhr) {
            alert('Error loading fixed metrics data');
            console.error(xhr);
        }
    });
}

// Function to update Top 5 Services table
function updateServicesTable(data) {
    const tbody = $('#topServicesTable');
    tbody.empty();
    
    if (data && data.length > 0) {
        data.forEach(function(item, index) {
            const row = `
                <tr>
                    <td class="border-0">${item.service_name || 'N/A'}</td>
                    <td class="border-0 text-end fw-bold">${item.total_count || '0'}</td>
                </tr>
            `;
            tbody.append(row);
        });
    } else {
        tbody.append('<tr><td colspan="2" class="text-center text-muted py-3">No data available</td></tr>');
    }
}


function renderSalesExpenseChart(data) {
    const ctx = document.getElementById("salesExpenseChart").getContext("2d");

    // Convert strings → numbers
    const expenseData = data.expense.map(Number);
    const salesData   = data.sales.map(Number);

    // Destroy previous chart
    if (window.salesExpenseChart instanceof Chart) {
        window.salesExpenseChart.destroy();
    }

    window.salesExpenseChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: "Total Expense",
                    data: expenseData,
                    backgroundColor: "rgba(255, 99, 132, 0.6)"
                },
                {
                    label: "Total Sales",
                    data: salesData,
                    backgroundColor: "rgba(54, 162, 235, 0.6)"
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

</script>
@endsection