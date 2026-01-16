@extends('layouts.main-layout')


@section('title', 'Dashboard')

@section('style')
<style>
.custom-bg {
    background-color: #ecf1ff !important;
}

.pie-chart-card-header{
    background-image: linear-gradient(270deg, #5e1d66 10%, #3a4973 100%);
}

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}

.metric-card {
    transition: transform 0.2s ease-in-out;
}

.metric-card:hover {
    transform: translateY(-2px);
}

</style>
@endsection

@section('content')
<div class="view-container mb-2">

    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Dashboard</h3>
        </div>
        <div class="card-body p-3">

            <!-- Row 0: Today's Metrics -->
            <div class="row g-3 mb-1">
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-primary">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Today's Customer</h6>
                                <h3 id="todaysCustomer" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-success">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Today's Orders</h6>
                                <h3 id="todaysOrders" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-success fs-2">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-warning">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Today's Inprogress Orders</h6>
                                <h3 id="todaysInprogressOrders" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-warning fs-2">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-info">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Today's Completed Orders</h6>
                                <h3 id="todaysCompletedOrders" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-info fs-2">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-danger">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Today's Return Orders</h6>
                                <h3 id="todaysReturnOrders" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-danger fs-2">
                                <i class="fas fa-undo"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-success">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Today's Sold Amount</h6>
                                <h3 id="todaysSoldAmount" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-success fs-2">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 1: Orders Metrics -->
            <div class="row g-3 mb-1">
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-primary">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Orders</h6>
                                <h3 id="totalOrders" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-warning">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Pending Orders</h6>
                                <h3 id="pendingOrders" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-warning fs-2">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-info">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">InProgress Order</h6>
                                <h3 id="inProgressOrder" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-info fs-2">
                                <i class="fas fa-spinner"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-danger">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Canceled Order</h6>
                                <h3 id="canceledOrder" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-danger fs-2">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-success">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Completed Order</h6>
                                <h3 id="completedOrder" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-success fs-2">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-danger">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Return Order</h6>
                                <h3 id="returnOrder" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-danger fs-2">
                                <i class="fas fa-arrow-left"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Products Metrics -->
            <div class="row g-3 mb-1">
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-primary">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Products</h6>
                                <h3 id="totalProducts" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-warning">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Unpublished Products</h6>
                                <h3 id="unpublishedProducts" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-warning fs-2">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-danger">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Low Stock Products</h6>
                                <h3 id="lowStockProducts" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-danger fs-2">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-success">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Sellable Stock</h6>
                                <h3 id="sellableStock" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-success fs-2">
                                <i class="fas fa-store"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-info">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Warehouse Stock</h6>
                                <h3 id="warehouseStock" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-info fs-2">
                                <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-danger">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Return Qty</h6>
                                <h3 id="returnQty" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-danger fs-2">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 4: Expenses & Sales Metrics -->
            <div class="row g-3 mb-1">
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-danger">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Expenses</h6>
                                <h3 id="totalExpenses" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-danger fs-2">
                                <i class="fas fa-money-bill-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-warning">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Purchases Cost</h6>
                                <h3 id="totalPurchasesCost" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-warning fs-2">
                                <i class="fas fa-shopping-basket"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-info">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Ad Cost</h6>
                                <h3 id="totalAdCost" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-info fs-2">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-primary">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Delivery Cost</h6>
                                <h3 id="totalDeliveryCost" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-primary fs-2">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-success">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Sold Qty</h6>
                                <h3 id="totalSoldQty" class="mb-0">⏳</h3>
                            </div>
                            <div class="text-success fs-2">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="card shadow-sm py-0 custom-bg border-left-success">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle text-muted mb-2">Total Sold Amount</h6>
                                <h3 id="totalSoldAmount" class="mb-0">TK. ⏳</h3>
                            </div>
                            <div class="text-success fs-2">
                                <i class="fas fa-dollar-sign"></i>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
@vite(['resources/js/common-chart.js'])



<script>
$(document).ready(function() {
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

}


</script>
@endsection
