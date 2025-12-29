WinPos.Employee = (function(Urls){
    var validateEmployee = function (formData, type, callback){

        let employeeName = $('#createEmployeeForm #employeeName').val().trim();
        let phone = $('#createEmployeeForm #phone').val().trim();
        let dateOfBirth = $('#createEmployeeForm #dateOfBirth').val().trim();
        let gender = $('#createEmployeeForm #gender').val().trim();
        let designationId = $('#createEmployeeForm #designationId').val().trim();
        let jobTitle = $('#createEmployeeForm #jobTitle').val().trim();
        let hireDate = $('#createEmployeeForm #hireDate').val().trim();
        let status = $('#createEmployeeForm #status').val().trim();

        // Remove previous validation errors
        $('#createEmployeeForm .is-invalid').removeClass('is-invalid');

        // Validate employee name
        if(employeeName.length < 2 || employeeName.length > 100){
            toastr.error("Employee name must be between 2 to 100 characters");
            $('#createEmployeeForm #employeeName').addClass('is-invalid');
            return false;
        }

        // Validate phone
        if(!phone){
            toastr.error("Phone number is required");
            $('#createEmployeeForm #phone').addClass('is-invalid');
            return false;
        }

        if(phone.length > 20){
            toastr.error("Phone number must not exceed 20 characters");
            $('#createEmployeeForm #phone').addClass('is-invalid');
            return false;
        }

        // Validate date of birth
        if(!dateOfBirth){
            toastr.error("Date of birth is required");
            $('#createEmployeeForm #dateOfBirth').addClass('is-invalid');
            return false;
        }

        // Validate gender
        if(!gender){
            toastr.error("Gender is required");
            $('#createEmployeeForm #gender').addClass('is-invalid');
            return false;
        }

        // Validate designation
        if(!designationId){
            toastr.error("Designation is required");
            $('#createEmployeeForm #designationId').addClass('is-invalid');
            return false;
        }

        // Validate job title
        if(jobTitle.length < 2 || jobTitle.length > 100){
            toastr.error("Job title must be between 2 to 100 characters");
            $('#createEmployeeForm #jobTitle').addClass('is-invalid');
            return false;
        }

        // Validate hire date
        if(!hireDate){
            toastr.error("Hire date is required");
            $('#createEmployeeForm #hireDate').addClass('is-invalid');
            return false;
        }

        // Validate status
        if(!status){
            toastr.error("Status is required");
            $('#createEmployeeForm #status').addClass('is-invalid');
            return false;
        }

        if(type === 'create'){
            save(formData, callback);
        }else{
            let id = $('#createEmployeeForm #employeeID').val().trim();
            if(id === "" || id === "0"){
                toastr.error("Something went wrong. Please try again.");
                return false;
            }

            update(formData, id, callback);
        }
    }

    var save = function (formData, callback){
        WinPos.Common.postAjaxCall(Urls.saveEmployee, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.addNewRow(prepareEmployeeRow(response.employee), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                
                // Clear attendance completed flag when new employee is created
                if(WinPos.Attendance && WinPos.Attendance.clearAttendanceCompletedFlag) {
                    WinPos.Attendance.clearAttendanceCompletedFlag();
                    // Update attendance button icon
                    if(WinPos.Attendance.checkTodayAttendanceStatus) {
                        WinPos.Attendance.checkTodayAttendanceStatus();
                    }
                }
                
                callback();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var update = function (formData, id, callback){
        WinPos.Common.putAjaxCallPost(Urls.updateEmployee.replace('employeeid', id), JSON.stringify(formData), function(response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.updateNewRow(prepareEmployeeRow(response.employee), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                callback();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        })
    }

    var deleteEmployee = function (id){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteEmployee.replace('employeeid', id), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        })
    }

    var viewEmployee = function (id){
        // Redirect to employee details page instead of showing modal
        window.location.href = '/employee/' + id + '/details';
    }

    var prepareEmployeeRow = function(data){
        let statusBadge = data.status === 'Active' 
            ? '<span class="badge badge-success">Active</span>' 
            : '<span class="badge badge-secondary">Inactive</span>';

        // Format dates for HTML date input (YYYY-MM-DD)
        let dobFormatted = '';
        if(data.date_of_birth) {
            let dobDate = new Date(data.date_of_birth);
            dobFormatted = dobDate.getFullYear() + '-' + 
                String(dobDate.getMonth() + 1).padStart(2, '0') + '-' + 
                String(dobDate.getDate()).padStart(2, '0');
        }
        
        let hireDateFormatted = '';
        if(data.hire_date) {
            let hireDateObj = new Date(data.hire_date);
            hireDateFormatted = hireDateObj.getFullYear() + '-' + 
                String(hireDateObj.getMonth() + 1).padStart(2, '0') + '-' + 
                String(hireDateObj.getDate()).padStart(2, '0');
        }

        // Phone masking
        let phoneDisplay = data.formattedPhone || data.phone || '-';

        // Today's Attendance status
        let attendanceStatus = '';
        let attendanceStatusValue = data.todayAttendanceStatus || 'Pending';
        let attendanceId = data.todayAttendanceId || '';
        let todayDate = new Date().toISOString().split('T')[0];
        
        if (attendanceStatusValue === 'Pending') {
            attendanceStatus = '<span class="badge badge-secondary">Pending</span>';
        } else if (attendanceStatusValue === 'Present') {
            attendanceStatus = '<span class="badge badge-success">Present</span>';
        } else if (attendanceStatusValue === 'Absent') {
            attendanceStatus = '<span class="badge badge-danger">Absent</span>';
        } else if (attendanceStatusValue === 'Leave') {
            attendanceStatus = '<span class="badge badge-warning">Leave</span>';
        } else {
            attendanceStatus = '<span class="badge badge-secondary">-</span>';
        }
        
        attendanceStatus += ' <button type="button" ' +
            'class="btn btn-sm btn-link p-0 ml-1 edit-today-attendance" ' +
            'data-employee-id="' + data.id + '" ' +
            'data-attendance-id="' + attendanceId + '" ' +
            'data-attendance-date="' + todayDate + '" ' +
            'data-status="' + (attendanceStatusValue === 'Pending' ? '' : attendanceStatusValue) + '" ' +
            'title="Edit Today\'s Attendance">' +
            '<i class="fa-solid fa-pen-to-square text-primary"></i></button>';

        let actionButtons = '<button data-id="' + data.id + '" ' +
            'class="btn btn-sm thm-btn-bg thm-btn-text-color view-employee">' +
            '<i class="fa-solid fa-eye"></i></button> ' +
            '<button data-id="' + data.id + '" ' +
            'data-name="' + data.name + '" ' +
            'data-phone="' + (data.phone || '') + '" ' +
            'data-dob="' + dobFormatted + '" ' +
            'data-gender="' + data.gender + '" ' +
            'data-designation="' + data.designation_id + '" ' +
            'data-job-title="' + data.job_title + '" ' +
            'data-hire-date="' + hireDateFormatted + '" ' +
            'data-status="' + data.status + '" ' +
            'data-note="' + (data.note || '').replace(/"/g, '&quot;') + '" ' +
            'class="btn btn-sm thm-btn-bg thm-btn-text-color edit-employee">' +
            '<i class="fa-solid fa-pen-to-square"></i></button> ' +
            '<button data-id="' + data.id + '" ' +
            'class="btn btn-sm thm-btn-bg thm-btn-text-color delete-employee">' +
            '<i class="fa-solid fa-trash"></i></button>';

        return [
            data.id,
            data.name,
            data.formattedDob,
            data.gender,
            data.designationName,
            phoneDisplay,
            data.formattedHireDate,
            statusBadge,
            attendanceStatus,
            actionButtons
        ];
    }

    var applyCssToNewlyAddedRow = function(row){
        let columns = $(row).find('td');

        columns.each(function(index){
            let col = $(this);
            col.addClass('text-center');
            col.addClass('align-middle');
        });
    }

    return {
        saveEmployee: validateEmployee,
        deleteEmployee: deleteEmployee,
        viewEmployee: viewEmployee
    }
})(EmployeeUrls);

