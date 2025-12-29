WinPos.Attendance = (function(Urls){
    var currentDate = '';
    var currentDesignationId = '';
    var pendingEmployees = [];
    var completedEmployees = [];
    var scrollToEmployeeId = null; // Store employee ID to scroll to after data loads
    
    // localStorage key for attendance completion flag
    var ATTENDANCE_COMPLETED_KEY = 'attendance_completed_today';

    // Initialize attendance modal
    var initModal = function(forceDate, employeeIdToScroll) {
        // Store employee ID to scroll to after data loads
        scrollToEmployeeId = employeeIdToScroll || null;
        
        // Set default date to today - use provided date or calculate from browser
        var todayStr;
        if(forceDate) {
            todayStr = forceDate;
            // Always set if forceDate is provided
            $('#attendanceDate').val(todayStr);
            currentDate = todayStr;
        } else {
            var today = new Date();
            var year = today.getFullYear();
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var day = String(today.getDate()).padStart(2, '0');
            todayStr = year + '-' + month + '-' + day;
            
            // Only set if not already set
            if(!$('#attendanceDate').val()) {
                $('#attendanceDate').val(todayStr);
            }
            currentDate = $('#attendanceDate').val() || todayStr;
        }

        // Load designations
        loadDesignations();

        // Load attendance data
        loadAttendanceData();
    }

    // Load designations for filter
    var loadDesignations = function() {
        WinPos.Common.getAjaxCall(Urls.getDesignations, function(response) {
            if(response.status === 'success') {
                var select = $('#attendanceDesignationFilter');
                select.empty();
                select.append('<option value="">All</option>');
                
                response.designations.forEach(function(designation) {
                    select.append('<option value="' + designation.id + '">' + designation.name + '</option>');
                });
            }
        });
    }

    // Load attendance data
    var loadAttendanceData = function() {
        var date = $('#attendanceDate').val();
        var designationId = $('#attendanceDesignationFilter').val() || '';

        if(!date) {
            toastr.error('Please select a date');
            return;
        }

        // Validate date is not in the future
        var selectedDate = new Date(date);
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);

        if(selectedDate > today) {
            toastr.error('Cannot select a future date');
            $('#attendanceDate').val(today.toISOString().split('T')[0]);
            return;
        }

        currentDate = date;
        currentDesignationId = designationId;

        // Show preloader
        $('#attendancePreloader').show();
        $('#attendanceFilters').hide();
        $('#attendanceTabContent').hide();

        var params = {
            date: date,
            designation_id: designationId || null
        };

        WinPos.Common.getAjaxCall(Urls.getAttendanceData + '?' + $.param(params), function(response) {
            // Hide preloader
            $('#attendancePreloader').hide();
            $('#attendanceFilters').show();
            $('#attendanceTabContent').show();

            if(response.status === 'success') {
                pendingEmployees = response.pending || [];
                completedEmployees = response.completed || [];

                renderPendingTable();
                renderCompletedTable();
                updateCounter(response.completed_count, response.total);

                // Check if today's attendance is completed
                var today = new Date();
                var year = today.getFullYear();
                var month = String(today.getMonth() + 1).padStart(2, '0');
                var day = String(today.getDate()).padStart(2, '0');
                var todayStr = year + '-' + month + '-' + day;
                
                if(date === todayStr && pendingEmployees.length === 0 && completedEmployees.length > 0 && response.total > 0) {
                    // All attendance completed for today - set localStorage flag
                    setAttendanceCompletedFlag(todayStr);
                    updateAttendanceButtonIcon(true);
                } else if(date === todayStr) {
                    // Still has pending - clear flag and remove icon
                    clearAttendanceCompletedFlag();
                    updateAttendanceButtonIcon(false);
                }

                // Auto-switch to Completed tab if all are completed
                if(pendingEmployees.length === 0 && completedEmployees.length > 0) {
                    $('#completed-tab').tab('show');
                } else {
                    $('#pending-tab').tab('show');
                }
                
                // Scroll to specific employee if requested
                if(scrollToEmployeeId) {
                    scrollToEmployee(scrollToEmployeeId);
                    scrollToEmployeeId = null; // Reset after scrolling
                }
            } else {
                toastr.error('Failed to load attendance data');
            }
        });
    }
    
    // Scroll to specific employee in the attendance table
    var scrollToEmployee = function(employeeId) {
        // Wait a bit for tab switching animation to complete
        setTimeout(function() {
            var $employeeRow = $('#pendingTableBody tr[data-employee-id="' + employeeId + '"]');
            if ($employeeRow.length === 0) {
                $employeeRow = $('#completedTableBody tr[data-employee-id="' + employeeId + '"]');
            }
            if ($employeeRow.length > 0) {
                // Scroll to the row
                $employeeRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Highlight the row
                $employeeRow.addClass('table-active');
                setTimeout(function() {
                    $employeeRow.removeClass('table-active');
                }, 2000);
            }
        }, 300);
    }

    // Render pending table
    var renderPendingTable = function() {
        var tbody = $('#pendingTableBody');
        tbody.empty();

        if(pendingEmployees.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center">No pending employees</td></tr>');
            return;
        }

        pendingEmployees.forEach(function(employee) {
            var row = '<tr data-employee-id="' + employee.id + '">' +
                '<td class="text-center align-middle">' + employee.id + '</td>' +
                '<td class="text-center align-middle">' + employee.name + '</td>' +
                '<td class="text-center align-middle">' + employee.designation + '</td>' +
                '<td class="text-center align-middle">' +
                '<div class="btn-group" role="group">' +
                '<button type="button" class="btn btn-success mark-status" data-status="Present" data-employee-id="' + employee.id + '" style="padding: 8px 16px; font-size: 14px;">Present</button>' +
                '<button type="button" class="btn btn-danger mark-status" data-status="Absent" data-employee-id="' + employee.id + '" style="padding: 8px 16px; font-size: 14px;">Absent</button>' +
                '<button type="button" class="btn btn-warning mark-status" data-status="Leave" data-employee-id="' + employee.id + '" style="padding: 8px 16px; font-size: 14px;">Leave</button>' +
                '</div>' +
                '</td>' +
                '</tr>';
            tbody.append(row);
        });
    }

    // Render completed table
    var renderCompletedTable = function() {
        var tbody = $('#completedTableBody');
        tbody.empty();

        if(completedEmployees.length === 0) {
            tbody.append('<tr><td colspan="6" class="text-center">No completed attendance</td></tr>');
            return;
        }

        completedEmployees.forEach(function(employee) {
            var statusBadge = getStatusBadge(employee.status);
            var timeInfo = '';
            
            // Use formatted times from backend if available, otherwise format on frontend
            if(employee.formatted_check_in_time && employee.formatted_check_out_time) {
                timeInfo = employee.formatted_check_in_time + '<br>' + employee.formatted_check_out_time;
            } else if(employee.formatted_check_in_time) {
                timeInfo = employee.formatted_check_in_time;
            } else if(employee.check_in_time && employee.check_out_time) {
                var checkIn = formatDateTime(employee.check_in_time);
                var checkOut = formatDateTime(employee.check_out_time);
                timeInfo = checkIn + '<br>' + checkOut;
            } else if(employee.check_in_time) {
                timeInfo = formatDateTime(employee.check_in_time);
            } else {
                timeInfo = '-';
            }

            var row = '<tr data-employee-id="' + employee.id + '" class="table-secondary">' +
                '<td class="text-center align-middle">' + employee.id + '</td>' +
                '<td class="text-center align-middle">' + employee.name + '</td>' +
                '<td class="text-center align-middle">' + employee.designation + '</td>' +
                '<td class="text-center align-middle">' + statusBadge + '</td>' +
                '<td class="text-center align-middle">' + timeInfo + '</td>' +
                '<td class="text-center align-middle">' +
                '<button type="button" class="btn btn-sm thm-btn-bg thm-btn-text-color edit-attendance" ' +
                'data-employee-id="' + employee.id + '" ' +
                'data-attendance-id="' + (employee.attendance_id || '') + '">' +
                '<i class="fa-solid fa-pen-to-square"></i> Edit</button>' +
                '</td>' +
                '</tr>';
            tbody.append(row);
        });
    }

    // Get status badge HTML
    var getStatusBadge = function(status) {
        var badges = {
            'Present': '<span class="badge badge-success">Present</span>',
            'Absent': '<span class="badge badge-danger">Absent</span>',
            'Leave': '<span class="badge badge-warning">Leave</span>',
            'Off': '<span class="badge badge-secondary">Off</span>'
        };
        return badges[status] || status;
    }

    // Format datetime for display
    var formatDateTime = function(dateTimeString) {
        if(!dateTimeString) return '-';
        var date = new Date(dateTimeString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
    }

    // Update counter
    var updateCounter = function(completed, total) {
        $('#attendanceCounter').text('Completed: ' + completed + '/' + total);
    }

    // Mark attendance status
    var markStatus = function(employeeId, status) {
        if(status === 'Leave') {
            // Open leave modal
            $('#leaveEmployeeId').val(employeeId);
            $('#leaveAttendanceDate').val(currentDate);
            $('#leaveType').val('');
            $('#leaveNote').val('');
            $('#leaveAttendanceId').val('');
            
            // Find if attendance already exists
            var employee = pendingEmployees.find(function(emp) {
                return emp.id == employeeId;
            });
            if(employee && employee.attendance_id) {
                $('#leaveAttendanceId').val(employee.attendance_id);
            }
            
            $('#leaveModal').modal('show');
        } else {
            // Save directly
            saveAttendance(employeeId, status, null, null, null, null);
        }
    }

    // Save attendance
    var saveAttendance = function(employeeId, status, leaveType, note, checkInTime, checkOutTime) {
        var data = {
            employee_id: employeeId,
            attendance_date: currentDate,
            status: status,
            leave_type: leaveType,
            note: note,
            check_in_time: checkInTime,
            check_out_time: checkOutTime
        };

        // Set default times for Present status
        if(status === 'Present' && !checkInTime && !checkOutTime) {
            var dateStr = currentDate;
            data.check_in_time = dateStr + ' 09:00:00';
            data.check_out_time = dateStr + ' 18:00:00';
        }

        // Find and animate the row before saving (only if in pending tab)
        var row = $('#pendingTableBody tr[data-employee-id="' + employeeId + '"]');
        var shouldAnimate = row.length > 0;

        if(shouldAnimate) {
            row.fadeOut(500);
        }

        WinPos.Common.postAjaxCall(Urls.saveAttendance, JSON.stringify(data), function(response) {
            if(response.status === 'success') {
                toastr.success(response.message);
                
                if(shouldAnimate) {
                    // Wait for animation to complete, then reload
                    setTimeout(function() {
                        loadAttendanceData();
                    }, 500);
                } else {
                    loadAttendanceData(); // Reload data immediately if no animation
                }
            } else {
                WinPos.Common.showValidationErrors(response.errors);
                // Show row again if error occurred
                if(shouldAnimate) {
                    row.fadeIn(300);
                }
            }
        });
    }

    // Mark all present
    var markAllPresent = function() {
        if(pendingEmployees.length === 0) {
            toastr.warning('No pending employees to mark');
            return;
        }

        var employeeIds = pendingEmployees.map(function(emp) {
            return emp.id;
        });

        var message = 'Are you sure you want to mark all the employees showing below as present?\n\n';
        message += 'Total employees: ' + employeeIds.length;
        
        if(!confirm(message)) {
            return;
        }

        var data = {
            date: currentDate,
            employee_ids: employeeIds
        };

        WinPos.Common.postAjaxCall(Urls.markAllPresent, JSON.stringify(data), function(response) {
            if(response.status === 'success') {
                toastr.success(response.message);
                loadAttendanceData(); // Reload data
            } else {
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    // Edit attendance
    var editAttendance = function(employeeId, attendanceId) {
        var employee = completedEmployees.find(function(emp) {
            return emp.id == employeeId;
        });

        if(!employee) {
            toastr.error('Employee not found');
            return;
        }

        $('#editEmployeeId').val(employeeId);
        $('#editAttendanceDate').val(currentDate);
        $('#editAttendanceId').val(attendanceId || '');
        $('#editStatus').val(employee.status);
        $('#editLeaveType').val(employee.leave_type || '');
        $('#editNote').val(employee.note || '');

        // Show/hide leave type based on status
        if(employee.status === 'Leave') {
            $('#editLeaveTypeGroup').show();
        } else {
            $('#editLeaveTypeGroup').hide();
        }

        // Set check-in and check-out times
        // Convert to local time for datetime-local input
        if(employee.check_in_time) {
            var checkInDate = new Date(employee.check_in_time);
            // Get local date-time string in format YYYY-MM-DDTHH:mm
            var year = checkInDate.getFullYear();
            var month = String(checkInDate.getMonth() + 1).padStart(2, '0');
            var day = String(checkInDate.getDate()).padStart(2, '0');
            var hours = String(checkInDate.getHours()).padStart(2, '0');
            var minutes = String(checkInDate.getMinutes()).padStart(2, '0');
            $('#editCheckInTime').val(year + '-' + month + '-' + day + 'T' + hours + ':' + minutes);
        } else {
            $('#editCheckInTime').val('');
        }

        if(employee.check_out_time) {
            var checkOutDate = new Date(employee.check_out_time);
            // Get local date-time string in format YYYY-MM-DDTHH:mm
            var year = checkOutDate.getFullYear();
            var month = String(checkOutDate.getMonth() + 1).padStart(2, '0');
            var day = String(checkOutDate.getDate()).padStart(2, '0');
            var hours = String(checkOutDate.getHours()).padStart(2, '0');
            var minutes = String(checkOutDate.getMinutes()).padStart(2, '0');
            $('#editCheckOutTime').val(year + '-' + month + '-' + day + 'T' + hours + ':' + minutes);
        } else {
            $('#editCheckOutTime').val('');
        }

        $('#editAttendanceModal').modal('show');
    }

    // Save leave
    var saveLeave = function() {
        var leaveType = $('#leaveType').val();
        if(!leaveType) {
            toastr.error('Please select a leave type');
            $('#leaveType').addClass('is-invalid');
            return;
        }

        $('#leaveType').removeClass('is-invalid');

        var employeeId = $('#leaveEmployeeId').val();
        var note = $('#leaveNote').val();

        saveAttendance(employeeId, 'Leave', leaveType, note, null, null);
        $('#leaveModal').modal('hide');
    }

    // Save edit attendance
    var saveEditAttendance = function() {
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

        $('#editLeaveType').removeClass('is-invalid');

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

        // Remove validation errors if validation passes
        $('#editCheckInTime').removeClass('is-invalid');
        $('#editCheckOutTime').removeClass('is-invalid');

        var employeeId = $('#editEmployeeId').val();

        // Convert datetime-local to datetime format (YYYY-MM-DD HH:mm:ss)
        // datetime-local format is YYYY-MM-DDTHH:mm, we need YYYY-MM-DD HH:mm:ss
        var checkIn = null;
        var checkOut = null;
        
        if(checkInTime) {
            // Convert from YYYY-MM-DDTHH:mm to YYYY-MM-DD HH:mm:ss
            checkIn = checkInTime.replace('T', ' ') + ':00';
        }
        
        if(checkOutTime) {
            // Convert from YYYY-MM-DDTHH:mm to YYYY-MM-DD HH:mm:ss
            checkOut = checkOutTime.replace('T', ' ') + ':00';
        }

        saveAttendance(employeeId, status, leaveType, note, checkIn, checkOut);
        $('#editAttendanceModal').modal('hide');
    }

    // localStorage management functions
    var setAttendanceCompletedFlag = function(dateStr) {
        // Use provided date or get today's date
        if(!dateStr) {
            var today = new Date();
            var year = today.getFullYear();
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var day = String(today.getDate()).padStart(2, '0');
            dateStr = year + '-' + month + '-' + day;
        }
        localStorage.setItem(ATTENDANCE_COMPLETED_KEY, dateStr);
    }

    var clearAttendanceCompletedFlag = function() {
        localStorage.removeItem(ATTENDANCE_COMPLETED_KEY);
    }

    var getAttendanceCompletedFlag = function() {
        return localStorage.getItem(ATTENDANCE_COMPLETED_KEY);
    }

    var isAttendanceCompletedToday = function() {
        var today = new Date();
        var year = today.getFullYear();
        var month = String(today.getMonth() + 1).padStart(2, '0');
        var day = String(today.getDate()).padStart(2, '0');
        var todayStr = year + '-' + month + '-' + day;
        var flagDate = getAttendanceCompletedFlag();
        return flagDate === todayStr;
    }

    // Update attendance button icon based on completion status
    var updateAttendanceButtonIcon = function(isCompleted) {
        var $btn = $('#attendanceBtnGlobal');
        var $icon = $btn.find('i');
        
        if(isCompleted) {
            // Add check icon if not already present
            if($btn.find('.attendance-check-icon').length === 0) {
                $icon.after('<i class="fa-solid fa-check attendance-check-icon" style="font-size: 0.7em; margin-left: 3px; color: #28a745;"></i>');
            }
        } else {
            // Remove check icon if present
            $btn.find('.attendance-check-icon').remove();
        }
    }

    // Check today's attendance status and update icon
    var checkTodayAttendanceStatus = function() {
        WinPos.Common.getAjaxCall(Urls.checkTodayStatus, function(response) {
            if(response.status === 'success') {
                // Use server's today_date for consistency
                var serverToday = response.today_date || null;
                
                if(response.is_completed && response.total_employees > 0) {
                    setAttendanceCompletedFlag(serverToday);
                    updateAttendanceButtonIcon(true);
                } else {
                    clearAttendanceCompletedFlag();
                    updateAttendanceButtonIcon(false);
                }
            }
        });
    }

    // Initialize attendance button icon on page load
    var initAttendanceButtonIcon = function() {
        if(isAttendanceCompletedToday()) {
            // Verify with server
            checkTodayAttendanceStatus();
        } else {
            updateAttendanceButtonIcon(false);
        }
    }

    return {
        initModal: initModal,
        loadAttendanceData: loadAttendanceData,
        markAllPresent: markAllPresent,
        markStatus: markStatus,
        editAttendance: editAttendance,
        saveLeave: saveLeave,
        saveEditAttendance: saveEditAttendance,
        clearAttendanceCompletedFlag: clearAttendanceCompletedFlag,
        setAttendanceCompletedFlag: setAttendanceCompletedFlag,
        isAttendanceCompletedToday: isAttendanceCompletedToday,
        checkTodayAttendanceStatus: checkTodayAttendanceStatus,
        initAttendanceButtonIcon: initAttendanceButtonIcon,
        updateAttendanceButtonIcon: updateAttendanceButtonIcon,
        scrollToEmployee: scrollToEmployee
    }
})(AttendanceUrls);

// Event handlers
$(document).ready(function() {
    // Initialize attendance button icon
    if(WinPos.Attendance && WinPos.Attendance.initAttendanceButtonIcon) {
        WinPos.Attendance.initAttendanceButtonIcon();
    }

    // Open attendance modal (from employee page)
    $('#attendanceBtn').on('click', function() {
        WinPos.Attendance.initModal();
        $('#attendanceModal').modal('show');
    });

    // Open attendance modal (from global layout)
    $('#attendanceBtnGlobal').on('click', function() {
        WinPos.Attendance.initModal();
        $('#attendanceModal').modal('show');
    });

    // Date change
    $('#attendanceDate').on('change', function() {
        var selectedDate = $(this).val();
        if(selectedDate) {
            var dateObj = new Date(selectedDate);
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            dateObj.setHours(0, 0, 0, 0);
            
            if(dateObj > today) {
                toastr.error('Cannot select a future date');
                $(this).val(today.toISOString().split('T')[0]);
                return;
            }
        }
        WinPos.Attendance.loadAttendanceData();
    });

    // Designation filter change
    $('#attendanceDesignationFilter').on('change', function() {
        WinPos.Attendance.loadAttendanceData();
    });

    // Mark all present button
    $('#markAllPresentBtn').on('click', function() {
        WinPos.Attendance.markAllPresent();
    });

    // Mark status buttons (delegated event)
    $(document).on('click', '.mark-status', function() {
        var employeeId = $(this).data('employee-id');
        var status = $(this).data('status');
        WinPos.Attendance.markStatus(employeeId, status);
    });

    // Edit attendance button (delegated event)
    $(document).on('click', '.edit-attendance', function() {
        var employeeId = $(this).data('employee-id');
        var attendanceId = $(this).data('attendance-id');
        WinPos.Attendance.editAttendance(employeeId, attendanceId);
    });

    // Save leave
    $('#saveLeaveBtn').on('click', function() {
        WinPos.Attendance.saveLeave();
    });

    // Save edit attendance
    $('#saveEditAttendanceBtn').on('click', function() {
        WinPos.Attendance.saveEditAttendance();
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

        // Remove previous validation errors
        $('#editCheckInTime').removeClass('is-invalid');
        $('#editCheckOutTime').removeClass('is-invalid');

        // Validate if both times are provided
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

    // Close modals and reload data
    $('#leaveModal, #editAttendanceModal').on('hidden.bs.modal', function() {
        // Clear form
        $(this).find('form')[0].reset();
    });
});

