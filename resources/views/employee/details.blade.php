@extends('layouts.main-layout')

@section('title', 'Employee Details')

@section('style')
<style>
.attendance-metric-card {
    border-left: 4px solid;
    transition: transform 0.2s;
}

.attendance-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.metric-present {
    border-left-color: #28a745;
}

.metric-absent {
    border-left-color: #dc3545;
}

.metric-leave {
    border-left-color: #ffc107;
}

.metric-service {
    border-left-color: #007bff;
}

/* Carousel theme colors */
#reviewsCarousel .carousel-indicators li {
    background-color: var(--thm-btn-bg, #21409a);
    opacity: 0.5;
}

#reviewsCarousel .carousel-indicators li.active {
    opacity: 1;
    background-color: var(--thm-btn-bg, #21409a);
}
</style>
@endsection

@section('content')
<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Employee Details</h3>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-info btn-sm" id="addReviewBtn">
                    <i class="fa-solid fa-plus"></i> Add Review
                </button>
                <button type="button" class="btn btn-warning btn-sm" id="viewAllLeavesBtn">
                    <i class="fa-solid fa-calendar-times"></i> View All Leaves
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="viewAllAbsenteesBtn">
                    <i class="fa-solid fa-user-slash"></i> View All Absentees
                </button>
                <a href="{{ route('employee.index') }}" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body p-1">
            <!-- Employee Ribbon -->
            <x-employee-ribbon :employee="$employee" />

            <!-- Attendance Metrics -->
            <div class="row mb-3">
                <div class="{{ $isBeautician ? 'col-lg-2 col-md-3' : 'col-md-3' }}">
                    <div class="card attendance-metric-card metric-present mb-2">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Present</h6>
                            <h3 class="mb-0 text-success">{{ $attendanceMetrics['total_present'] }}</h3>
                            <small class="text-muted">{{ $attendanceMetrics['present_percentage'] }}% of total {{ $attendanceMetrics['total_days'] }} working days</small>
                        </div>
                    </div>
                </div>
                <div class="{{ $isBeautician ? 'col-lg-2 col-md-3' : 'col-md-3' }}">
                    <div class="card attendance-metric-card metric-absent mb-2">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Absent</h6>
                            <h3 class="mb-0 text-danger">{{ $attendanceMetrics['total_absent'] }}</h3>
                            <small class="text-muted">{{ $attendanceMetrics['absent_percentage'] }}% of total {{ $attendanceMetrics['total_days'] }} working days</small>
                        </div>
                    </div>
                </div>
                <div class="{{ $isBeautician ? 'col-lg-2 col-md-3' : 'col-md-3' }}">
                    <div class="card attendance-metric-card metric-leave mb-2">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Leave</h6>
                            <h3 class="mb-0 text-warning">{{ $attendanceMetrics['total_leave'] }}</h3>
                            <small class="text-muted">{{ $attendanceMetrics['leave_percentage'] }}% of total {{ $attendanceMetrics['total_days'] }} working days</small>
                        </div>
                    </div>
                </div>
                <div class="{{ $isBeautician ? 'col-lg-3 col-md-3' : 'col-md-3' }}">
                    <div class="card attendance-metric-card metric-leave mb-2">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Reviews</h6>
                            <h3 class="mb-0 text-info">{{ $reviewMetrics['total_reviews'] }}</h3>
                            <small class="text-muted">Positive: {{ $reviewMetrics['positive_count'] }} ({{ $reviewMetrics['positive_percentage'] }}%) | Negative: {{ $reviewMetrics['negative_count'] }} ({{ $reviewMetrics['negative_percentage'] }}%) | Warning: {{ $reviewMetrics['warning_count'] }} ({{ $reviewMetrics['warning_percentage'] }}%)</small>
                        </div>
                    </div>
                </div>
                @if($isBeautician && $beauticianServiceMetrics)
                <div class="col-lg-3 col-md-3">
                    <div class="card attendance-metric-card metric-service mb-2">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Today's Services</h6>
                            <h3 class="mb-0 text-primary">{{ $beauticianServiceMetrics['today_services'] }}</h3>
                            <small class="text-muted">Total: {{ $beauticianServiceMetrics['total_services'] }} | Average: {{ $beauticianServiceMetrics['average_services_per_day'] }}/day</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Attendance History -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card border p-2">
                        <div class="card-header">
                            <h5 class="mb-0">Attendance History (Last 30 Days)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" id="attendanceHistoryTable">
                                    <thead class="thm-tbl-header-bg thm-tbl-header-text-color">
                                        <tr>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Check-in</th>
                                            <th class="text-center">Check-out</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($last30DaysAttendance && $last30DaysAttendance->count() > 0)
                                        @foreach($last30DaysAttendance as $attendance)
                                        <tr class="{{ isset($attendance->is_today) && $attendance->is_today ? 'table-info' : '' }}">
                                            <td class="text-center align-middle">
                                                {{ $attendance->formatted_date }}
                                                @if(isset($attendance->is_today) && $attendance->is_today)
                                                    <span class="badge badge-primary ml-1">Today</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($attendance->status === 'Present')
                                                    <span class="badge badge-success">Present</span>
                                                @elseif($attendance->status === 'Absent')
                                                    <span class="badge badge-danger">Absent</span>
                                                @elseif($attendance->status === 'Leave')
                                                    <span class="badge badge-warning">Leave</span>
                                                @elseif(isset($attendance->is_pending) && $attendance->is_pending)
                                                    <span class="badge badge-secondary">Pending</span>
                                                @else
                                                    <span class="badge badge-secondary">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">{{ $attendance->formatted_check_in }}</td>
                                            <td class="text-center align-middle">{{ $attendance->formatted_check_out }}</td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-attendance-history"
                                                    data-attendance-id="{{ $attendance->id ?? '' }}"
                                                    data-employee-id="{{ $employee->id }}"
                                                    data-attendance-date="{{ is_object($attendance->attendance_date) ? $attendance->attendance_date->format('Y-m-d') : $attendance->attendance_date }}"
                                                    data-status="{{ $attendance->status ?? '' }}"
                                                    data-leave-type="{{ $attendance->leave_type ?? '' }}"
                                                    data-note="{{ htmlspecialchars($attendance->note ?? '', ENT_QUOTES) }}"
                                                    data-check-in="{{ (isset($attendance->check_in_time) && $attendance->check_in_time) ? (is_object($attendance->check_in_time) ? $attendance->check_in_time->format('Y-m-d\TH:i') : $attendance->check_in_time) : '' }}"
                                                    data-check-out="{{ (isset($attendance->check_out_time) && $attendance->check_out_time) ? (is_object($attendance->check_out_time) ? $attendance->check_out_time->format('Y-m-d\TH:i') : $attendance->check_out_time) : '' }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="5" class="text-center">No attendance records found</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border p-2">
                        <div class="card-header">
                            <h5 class="mb-0">Employee Reviews</h5>
                        </div>
                        <div class="card-body p-0 mt-1">
                            @if($reviews && $reviews->count() > 0)
                            <div id="reviewsCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                                <div class="carousel-inner">
                                    @foreach($reviews as $index => $review)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <div class="review-card p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">{{ $review->title }}</h6>
                                                    <small class="text-muted">{{ $review->formatted_date }}</small>
                                                </div>
                                                <div>
                                                    @if($review->status === 'positive')
                                                        <span class="badge badge-success">Positive</span>
                                                    @elseif($review->status === 'negative')
                                                        <span class="badge badge-danger">Negative</span>
                                                    @elseif($review->status === 'warning')
                                                        <span class="badge badge-warning">Warning</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="review-details mb-3">
                                                <p class="mb-0">{{ $review->details ?? 'No details provided.' }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Created by: {{ $review->created_by_name }}</small>
                                                <div>
                                                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-review-btn" 
                                                        data-review-id="{{ $review->id }}"
                                                        data-review-date="{{ $review->review_date->format('Y-m-d') }}"
                                                        data-title="{{ htmlspecialchars($review->title, ENT_QUOTES) }}"
                                                        data-status="{{ $review->status }}"
                                                        data-details="{{ htmlspecialchars($review->details ?? '', ENT_QUOTES) }}">
                                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-review-btn" 
                                                        data-review-id="{{ $review->id }}">
                                                        <i class="fa-solid fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($reviews->count() > 1)
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color" id="prevReviewBtn">
                                        <i class="fa-solid fa-chevron-left"></i> Prev
                                    </button>
                                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color" id="nextReviewBtn">
                                        Next <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No reviews yet. Click "Add Review" to create one.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Attendance Modals -->
<div id="attendanceModalContainer">
    @include('attendance/modal')
</div>

<!-- Include Review Modal -->
@include('employee/review-modal', ['employee' => $employee])

<!-- Leaves Modal -->
<div class="modal fade" id="leavesModal" tabindex="-1" role="dialog" aria-labelledby="leavesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="leavesModalLabel">All Leaves</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover">
                        <thead class="thm-tbl-header-bg thm-tbl-header-text-color">
                            <tr>
                                <th class="text-center">Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Leave Type</th>
                                <th class="text-center">Note</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allLeaves as $leave)
                            <tr>
                                <td class="text-center align-middle">{{ $leave->formatted_date }}</td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-warning">Leave</span>
                                </td>
                                <td class="text-center align-middle">{{ $leave->leave_type ?? '-' }}</td>
                                <td class="text-center align-middle">{{ $leave->note ?? '-' }}</td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-leave-from-modal"
                                        data-attendance-id="{{ $leave->id }}"
                                        data-employee-id="{{ $employee->id }}"
                                        data-attendance-date="{{ $leave->attendance_date->format('Y-m-d') }}"
                                        data-status="{{ $leave->status }}"
                                        data-leave-type="{{ $leave->leave_type ?? '' }}"
                                        data-note="{{ $leave->note ?? '' }}"
                                        data-check-in="{{ $leave->check_in_time ? $leave->check_in_time->format('Y-m-d\TH:i') : '' }}"
                                        data-check-out="{{ $leave->check_out_time ? $leave->check_out_time->format('Y-m-d\TH:i') : '' }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No leave records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Absentees Modal -->
<div class="modal fade" id="absenteesModal" tabindex="-1" role="dialog" aria-labelledby="absenteesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="absenteesModalLabel">All Absentees</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover">
                        <thead class="thm-tbl-header-bg thm-tbl-header-text-color">
                            <tr>
                                <th class="text-center">Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Note</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allAbsences as $absence)
                            <tr>
                                <td class="text-center align-middle">{{ $absence->formatted_date }}</td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-danger">Absent</span>
                                </td>
                                <td class="text-center align-middle">{{ $absence->note ?? '-' }}</td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-absence-from-modal"
                                        data-attendance-id="{{ $absence->id }}"
                                        data-employee-id="{{ $employee->id }}"
                                        data-attendance-date="{{ $absence->attendance_date->format('Y-m-d') }}"
                                        data-status="{{ $absence->status }}"
                                        data-leave-type="{{ $absence->leave_type ?? '' }}"
                                        data-note="{{ $absence->note ?? '' }}"
                                        data-check-in="{{ $absence->check_in_time ? $absence->check_in_time->format('Y-m-d\TH:i') : '' }}"
                                        data-check-out="{{ $absence->check_out_time ? $absence->check_out_time->format('Y-m-d\TH:i') : '' }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No absence records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('url-scripts')
<script>
// Employee details page specific URLs
var EmployeeDetailsUrls = {
    'saveAttendance': "{{ route('attendance.save') }}",
    'employeeId': {{ $employee->id }},
    'storeReview': "{{ route('employee.review.store') }}",
    'updateReview': "{{ route('employee.review.update', ['review' => 'reviewid']) }}",
    'deleteReview': "{{ route('employee.review.destroy', ['review' => 'reviewid']) }}"
};
</script>
@endpush

@section('script')
<script>
$(document).ready(function() {
    // Initialize DataTable for attendance history
    // For DOM-sourced tables, we don't need to specify columns - DataTables will auto-detect
    WinPos.Datatable.initDataTable('#attendanceHistoryTable', {
        order: [[0, 'desc']],
        pageLength: 9
    });

    // Initialize tooltips for note hover
    $('[data-toggle="tooltip"]').tooltip();

    // View All Leaves button
    $('#viewAllLeavesBtn').on('click', function() {
        $('#leavesModal').modal('show');
    });

    // View All Absentees button
    $('#viewAllAbsenteesBtn').on('click', function() {
        $('#absenteesModal').modal('show');
    });

    // Edit attendance from history table
    $(document).on('click', '.edit-attendance-history', function() {
        var $btn = $(this);
        openEditAttendanceModal(
            $btn.data('employee-id'),
            $btn.data('attendance-id'),
            $btn.data('attendance-date'),
            $btn.data('status'),
            $btn.data('leave-type'),
            $btn.data('note'),
            $btn.data('check-in'),
            $btn.data('check-out')
        );
    });

    // Edit leave from leaves modal
    $(document).on('click', '.edit-leave-from-modal', function() {
        var $btn = $(this);
        $('#leavesModal').modal('hide');
        openEditAttendanceModal(
            $btn.data('employee-id'),
            $btn.data('attendance-id'),
            $btn.data('attendance-date'),
            $btn.data('status'),
            $btn.data('leave-type'),
            $btn.data('note'),
            $btn.data('check-in'),
            $btn.data('check-out')
        );
    });

    // Edit absence from absentees modal
    $(document).on('click', '.edit-absence-from-modal', function() {
        var $btn = $(this);
        $('#absenteesModal').modal('hide');
        openEditAttendanceModal(
            $btn.data('employee-id'),
            $btn.data('attendance-id'),
            $btn.data('attendance-date'),
            $btn.data('status'),
            $btn.data('leave-type'),
            $btn.data('note'),
            $btn.data('check-in'),
            $btn.data('check-out')
        );
    });

    // Function to open edit attendance modal
    function openEditAttendanceModal(employeeId, attendanceId, attendanceDate, status, leaveType, note, checkIn, checkOut) {
        $('#editEmployeeId').val(employeeId);
        $('#editAttendanceDate').val(attendanceDate);
        $('#editAttendanceId').val(attendanceId || '');
        $('#editStatus').val(status);
        $('#editLeaveType').val(leaveType || '');
        $('#editNote').val(note || '');

        // Show/hide leave type based on status
        if(status === 'Leave') {
            $('#editLeaveTypeGroup').show();
        } else {
            $('#editLeaveTypeGroup').hide();
            $('#editLeaveType').val('');
        }

        // Set check-in and check-out times
        $('#editCheckInTime').val(checkIn || '');
        $('#editCheckOutTime').val(checkOut || '');

        $('#editAttendanceModal').modal('show');
    }

    // Handle save edit attendance - refresh page after save
    $('#saveEditAttendanceBtn').on('click', function() {
        var status = $('#editStatus').val();
        var leaveType = $('#editLeaveType').val();
        var note = $('#editNote').val();
        var checkInTime = $('#editCheckInTime').val();
        var checkOutTime = $('#editCheckOutTime').val();

        if(status === 'Leave' && !leaveType) {
            toastr.error('Please select a leave type');
            $('#editLeaveType').addClass('is-invalid');
            return;
        }

        // Validate check-in and check-out times
        if(checkInTime && checkOutTime) {
            var checkIn = new Date(checkInTime);
            var checkOut = new Date(checkOutTime);

            if(checkIn > checkOut) {
                toastr.error('Check-in time cannot be after check-out time');
                $('#editCheckInTime').addClass('is-invalid');
                $('#editCheckOutTime').addClass('is-invalid');
                return;
            }
        }

        $('#editLeaveType').removeClass('is-invalid');
        $('#editCheckInTime').removeClass('is-invalid');
        $('#editCheckOutTime').removeClass('is-invalid');

        var employeeId = $('#editEmployeeId').val();
        var attendanceDate = $('#editAttendanceDate').val();

        // Convert datetime-local to datetime format
        var checkIn = checkInTime ? checkInTime.replace('T', ' ') + ':00' : null;
        var checkOut = checkOutTime ? checkOutTime.replace('T', ' ') + ':00' : null;

        var data = {
            employee_id: employeeId,
            attendance_date: attendanceDate,
            status: status,
            leave_type: leaveType,
            note: note,
            check_in_time: checkIn,
            check_out_time: checkOut
        };

        WinPos.Common.postAjaxCall(EmployeeDetailsUrls.saveAttendance, JSON.stringify(data), function(response) {
            if(response.status === 'success') {
                toastr.success(response.message);
                $('#editAttendanceModal').modal('hide');
                // Reload page to refresh data
                location.reload();
            } else {
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    });

    // Show/hide leave type on status change in edit modal
    $('#editStatus').on('change', function() {
        if($(this).val() === 'Leave') {
            $('#editLeaveTypeGroup').show();
        } else {
            $('#editLeaveTypeGroup').hide();
            $('#editLeaveType').val('');
        }
    });

    // Real-time validation for check-in and check-out times
    $('#editCheckInTime, #editCheckOutTime').on('change', function() {
        var checkInTime = $('#editCheckInTime').val();
        var checkOutTime = $('#editCheckOutTime').val();

        $('#editCheckInTime').removeClass('is-invalid');
        $('#editCheckOutTime').removeClass('is-invalid');

        if(checkInTime && checkOutTime) {
            var checkIn = new Date(checkInTime);
            var checkOut = new Date(checkOutTime);

            if(checkIn > checkOut) {
                toastr.warning('Check-in time cannot be after check-out time');
                $('#editCheckInTime').addClass('is-invalid');
                $('#editCheckOutTime').addClass('is-invalid');
            }
        }
    });

    // Review Carousel Navigation
    $('#prevReviewBtn').on('click', function() {
        $('#reviewsCarousel').carousel('prev');
    });

    $('#nextReviewBtn').on('click', function() {
        $('#reviewsCarousel').carousel('next');
    });

    // Review Management
    // Add Review button
    $('#addReviewBtn').on('click', function() {
        $('#reviewModalLabel').text('Add Review');
        $('#reviewForm')[0].reset();
        $('#reviewId').val('');
        $('#reviewDate').val(new Date().toISOString().split('T')[0]);
        $('#reviewModal').modal('show');
    });

    // Edit Review button
    $(document).on('click', '.edit-review-btn', function() {
        var $btn = $(this);
        $('#reviewModalLabel').text('Edit Review');
        $('#reviewId').val($btn.data('review-id'));
        $('#reviewDate').val($btn.data('review-date'));
        $('#reviewTitle').val($btn.data('title'));
        $('#reviewStatus').val($btn.data('status'));
        $('#reviewDetails').val($btn.data('details'));
        $('#reviewModal').modal('show');
    });

    // Save Review
    $('#saveReviewBtn').on('click', function() {
        var reviewId = $('#reviewId').val();
        var employeeId = $('#reviewEmployeeId').val();
        var reviewDate = $('#reviewDate').val();
        var title = $('#reviewTitle').val();
        var status = $('#reviewStatus').val();
        var details = $('#reviewDetails').val();

        // Validation
        if(!reviewDate || !title || !status) {
            toastr.error('Please fill in all required fields');
            return;
        }

        var data = {
            employee_id: employeeId,
            review_date: reviewDate,
            title: title,
            status: status,
            details: details
        };

        if(reviewId) {
            // Update existing review
            var url = EmployeeDetailsUrls.updateReview.replace('reviewid', reviewId);
            WinPos.Common.putAjaxCallPost(url, JSON.stringify(data), function(response) {
                if(response.status === 'success') {
                    toastr.success(response.message);
                    $('#reviewModal').modal('hide');
                    // Reload page to refresh data
                    location.reload();
                } else {
                    WinPos.Common.showValidationErrors(response.errors);
                }
            });
        } else {
            // Create new review
            WinPos.Common.postAjaxCall(EmployeeDetailsUrls.storeReview, JSON.stringify(data), function(response) {
                if(response.status === 'success') {
                    toastr.success(response.message);
                    $('#reviewModal').modal('hide');
                    // Reload page to refresh data
                    location.reload();
                } else {
                    WinPos.Common.showValidationErrors(response.errors);
                }
            });
        }
    });

    // Delete Review
    $(document).on('click', '.delete-review-btn', function() {
        var $btn = $(this);
        var reviewId = $btn.data('review-id');

        if(confirm('Are you sure you want to delete this review?')) {
            var url = EmployeeDetailsUrls.deleteReview.replace('reviewid', reviewId);
            
            WinPos.Common.deleteAjaxCallPost(url, function(response) {
                if(response.status === 'success') {
                    toastr.success(response.message);
                    // Reload page to refresh data
                    location.reload();
                } else {
                    toastr.error(response.message || 'Failed to delete review');
                }
            });
        }
    });
});
</script>
@endsection
