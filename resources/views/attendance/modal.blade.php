<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="attendanceModalLabel">Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Preloader -->
                <div id="attendancePreloader" class="text-center py-5" style="display: none;">
                    <div class="spinner-border thm-text-color" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading attendance data...</p>
                </div>

                <!-- Top Section: Filters and All Present Button -->
                <div class="row mb-3" id="attendanceFilters">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="attendanceDate" class="mb-1">Date:</label>
                            <input type="date" class="form-control rounded" id="attendanceDate" name="attendanceDate" max="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="attendanceDesignationFilter" class="mb-1">Designation:</label>
                            <select class="form-control rounded" id="attendanceDesignationFilter" name="attendanceDesignationFilter">
                                <option value="">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="markAllPresentBtn" class="mb-1">Action:</label>
                            <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded w-100" id="markAllPresentBtn" style="height: calc(1.5em + 0.75rem + 2px); padding: 0.375rem 0.75rem;">
                                <i class="fa-solid fa-check-double"></i> All Present
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="attendanceTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                            Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">
                            Completed
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="attendanceTabContent">
                    <!-- Pending Tab -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                        <div class="attendance-table-container" style="max-height: 400px; overflow-y: scroll;">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thm-tbl-header-bg thm-tbl-header-text-color" style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th class="text-center" style="width: 8%;">ID</th>
                                        <th class="text-center" style="width: 25%;">Name</th>
                                        <th class="text-center" style="width: 20%;">Designation</th>
                                        <th class="text-center" style="width: 47%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Completed Tab -->
                    <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                        <div class="attendance-table-container" style="max-height: 400px; overflow-y: scroll;">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thm-tbl-header-bg thm-tbl-header-text-color" style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th class="text-center" style="width: 8%;">ID</th>
                                        <th class="text-center" style="width: 20%;">Name</th>
                                        <th class="text-center" style="width: 15%;">Designation</th>
                                        <th class="text-center" style="width: 12%;">Status</th>
                                        <th class="text-center" style="width: 20%;">Time</th>
                                        <th class="text-center" style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="completedTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <span id="attendanceCounter" class="text-muted">Completed: 0/0</span>
                </div>
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leave Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1" role="dialog" aria-labelledby="leaveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded">
            <form id="leaveForm">
                <div class="modal-header rounded">
                    <h5 class="modal-title" id="leaveModalLabel">Leave Details</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="leaveEmployeeId">
                    <input type="hidden" id="leaveAttendanceDate">
                    <input type="hidden" id="leaveAttendanceId">
                    
                    <div class="form-group">
                        <label for="leaveType">Leave Type:<span class="text-danger">*</span></label>
                        <select class="form-control rounded" id="leaveType" name="leaveType" required>
                            <option value="">Select Leave Type</option>
                            <option value="Annual">Annual</option>
                            <option value="Sick">Sick</option>
                            <option value="Casual">Casual</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="leaveNote">Note:</label>
                        <textarea class="form-control rounded" id="leaveNote" name="leaveNote" rows="3" placeholder="Enter any additional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </button>
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="saveLeaveBtn">
                        <i class="fa-solid fa-floppy-disk"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded">
            <form id="editAttendanceForm">
                <div class="modal-header rounded">
                    <h5 class="modal-title" id="editAttendanceModalLabel">Edit Attendance</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editEmployeeId">
                    <input type="hidden" id="editAttendanceDate">
                    <input type="hidden" id="editAttendanceId">
                    
                    <div class="form-group">
                        <label for="editStatus">Status:<span class="text-danger">*</span></label>
                        <select class="form-control rounded" id="editStatus" name="editStatus" required>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Leave">Leave</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="editLeaveTypeGroup" style="display: none;">
                        <label for="editLeaveType">Leave Type:</label>
                        <select class="form-control rounded" id="editLeaveType" name="editLeaveType">
                            <option value="">Select Leave Type</option>
                            <option value="Annual">Annual</option>
                            <option value="Sick">Sick</option>
                            <option value="Casual">Casual</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="editCheckInTime">Check In Time:</label>
                        <input type="datetime-local" class="form-control rounded" id="editCheckInTime" name="editCheckInTime">
                        <small class="form-text text-muted">Check-in time must be before check-out time</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="editCheckOutTime">Check Out Time:</label>
                        <input type="datetime-local" class="form-control rounded" id="editCheckOutTime" name="editCheckOutTime">
                        <small class="form-text text-muted">Check-out time must be after check-in time</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="editNote">Note:</label>
                        <textarea class="form-control rounded" id="editNote" name="editNote" rows="3" placeholder="Enter any additional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </button>
                    <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="saveEditAttendanceBtn">
                        <i class="fa-solid fa-floppy-disk"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

