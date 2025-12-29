    @extends('layouts.main-layout')

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Employee List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchEmployee" placeholder="Search Employee">
                <div class="vr mx-1"></div>
                <div class="text-right d-flex gap-2">
                    <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="attendanceBtn" data-toggle="modal"><i class="fa-solid fa-calendar-check"></i> Attendance</button>
                    <button class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" id="createEmployeeBtn" data-toggle="modal"><i class="fa-solid fa-plus"></i> New Employee</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="employeeTable">
            <thead>
                <tr>
                    <th scope="col" class="text-center" style="width: 5%;">Id</th>
                    <th scope="col" class="text-center" style="width: 15%;">Name</th>
                    <th scope="col" class="text-center" style="width: 10%;">Dob</th>
                    <th scope="col" class="text-center" style="width: 8%;">Gender</th>
                    <th scope="col" class="text-center" style="width: 12%;">Designation</th>
                    <th scope="col" class="text-center" style="width: 10%;">Phone</th>
                    <th scope="col" class="text-center" style="width: 10%;">Hire Date</th>
                    <th scope="col" class="text-center" style="width: 8%;">Status</th>
                    <th scope="col" class="text-center" style="width: 12%;">Today's Attendance</th>
                    <th scope="col" class="text-center" style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td class="text-center align-middle">{{$employee->id}}</td>
                    <td class="align-middle text-center">{{$employee->name}}</td>
                    <td class="text-center align-middle">{{$employee->formattedDob}}</td>
                    <td class="text-center align-middle">{{$employee->gender}}</td>
                    <td class="text-center align-middle">{{$employee->designationName}}</td>
                    <td class="text-center align-middle">{{$employee->formattedPhone ?? '-'}}</td>
                    <td class="text-center align-middle">{{$employee->formattedHireDate}}</td>
                    <td class="text-center align-middle">
                        <span class="badge {{$employee->status === 'Active' ? 'badge-success' : 'badge-secondary'}}">
                            {{$employee->status}}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        @if(isset($employee->todayAttendanceStatus))
                            @if($employee->todayAttendanceStatus === 'Pending')
                                <span class="badge badge-secondary">Pending</span>
                            @elseif($employee->todayAttendanceStatus === 'Present')
                                <span class="badge badge-success">Present</span>
                            @elseif($employee->todayAttendanceStatus === 'Absent')
                                <span class="badge badge-danger">Absent</span>
                            @elseif($employee->todayAttendanceStatus === 'Leave')
                                <span class="badge badge-warning">Leave</span>
                            @endif
                            <button type="button" 
                                class="btn btn-sm btn-link p-0 ml-1 edit-today-attendance" 
                                data-employee-id="{{ $employee->id }}"
                                data-attendance-id="{{ $employee->todayAttendanceId ?? '' }}"
                                data-attendance-date="{{ date('Y-m-d') }}"
                                data-status="{{ $employee->todayAttendanceStatus === 'Pending' ? '' : $employee->todayAttendanceStatus }}"
                                title="Edit Today's Attendance">
                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                            </button>
                        @else
                            <span class="badge badge-secondary">-</span>
                        @endif
                    </td>
                    <td class="text-center align-middle">
                        <a href="{{ route('employee.details', ['employee' => $employee->id]) }}" class='btn btn-sm thm-btn-bg thm-btn-text-color'><i class='fa-solid fa-eye'></i></a>
                        <button data-id="{{$employee->id}}" 
                                data-name="{{$employee->name}}" 
                                data-phone="{{$employee->phone}}"
                                data-dob="{{$employee->date_of_birth->format('Y-m-d')}}"
                                data-gender="{{$employee->gender}}"
                                data-designation="{{$employee->designation_id}}"
                                data-job-title="{{$employee->job_title}}"
                                data-hire-date="{{$employee->hire_date->format('Y-m-d')}}"
                                data-status="{{$employee->status}}"
                                data-note="{{htmlspecialchars($employee->note ?? '', ENT_QUOTES)}}"
                                class='btn btn-sm thm-btn-bg thm-btn-text-color edit-employee'><i class='fa-solid fa-pen-to-square'></i></button>
                        <button data-id="{{$employee->id}}" class='btn btn-sm thm-btn-bg thm-btn-text-color delete-employee'><i class='fa-solid fa-trash'></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

<div id="createEmployeeModalContainer">
    @include('employee/create', ['designations' => $designations])
</div>

<div id="attendanceModalContainer">
    @include('attendance/modal')
</div>

@endsection
@section('style')
<style>
/* Attendance Modal Styles */
.attendance-table-container {
    overflow-y: scroll !important;
}

.attendance-table-container::-webkit-scrollbar {
    width: 8px;
    display: block;
}

.attendance-table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.attendance-table-container::-webkit-scrollbar-thumb {
    background: #5e1d66;
    border-radius: 4px;
}

.attendance-table-container::-webkit-scrollbar-thumb:hover {
    background: #7b3b84;
}

/* Always show scrollbar */
.attendance-table-container {
    scrollbar-width: thin;
    scrollbar-color: #5e1d66 #f1f1f1;
}
</style>
@endsection

@push('url-scripts')
<script>
// Declare EmployeeUrls before scripts load
var EmployeeUrls = {
    'getEmployees': "{{ route('employee.index') }}",
    'saveEmployee': "{{ route('employee.store') }}",
    'updateEmployee': "{{ route('employee.update', ['employee' => 'employeeid']) }}",
    'deleteEmployee': "{{ route('employee.destroy', ['employee' => 'employeeid']) }}",
    'editEmployee': "{{ route('employee.edit', ['employee' => 'employeeid']) }}"
};
</script>
@endpush

@push('vite-scripts')
@vite(['resources/js/employee/employee-script.js'])
@endpush

@section('script')
<script>
// AttendanceUrls is already declared in main-layout.blade.php, so we don't need to redeclare it here

$(document).ready(function() {
    WinPos.Datatable.initDataTable('#employeeTable', {
        order: [
            [0, 'desc']
        ],
        columns: [{
                type: 'num',
                orderable: true
            },
            {
                type: 'string',
                orderable: true
            },
            {
                type: 'date',
                orderable: true
            },
            {
                type: 'string',
                orderable: true
            },
            {
                type: 'string',
                orderable: true
            },
            {
                type: 'string',
                orderable: false
            },
            {
                type: 'date',
                orderable: true
            },
            {
                type: 'string',
                orderable: true
            },
            {
                type: 'date',
                orderable: true
            },
            {
                type: 'string',
                orderable: false
            },
        ]
    });

    $("#searchEmployee").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    });

    // Edit today's attendance
    $(document).on('click', '.edit-today-attendance', function() {
        var $btn = $(this);
        var employeeId = $btn.data('employee-id');
        var attendanceDate = $btn.data('attendance-date');
        
        // Open attendance modal with today's date and pass employee ID to scroll to
        $('#attendanceModal').modal('show');
        
        // Initialize modal and load attendance data for today, passing employee ID to scroll to
        WinPos.Attendance.initModal(attendanceDate, employeeId);
    });

    $('#createEmployeeModal').on('shown.bs.modal', function() {
        $("#employeeName").focus();
    })

    $("#createEmployeeBtn").on('click', function() {
        $("#createEmployeeModalLabel").html("Create New Employee")
        $("#saveUpdateEmployee").attr('data-type', 'create').html('<i class="fa-solid fa-floppy-disk"></i> Create');
        $("#employeeName").val("");
        $("#employeeID").val("");
        $("#dateOfBirth").val("");
        $("#gender").val("Male");
        $("#designationId").val("");
        $("#jobTitle").val("");
        $("#hireDate").val("");
        $("#status").val("Active");
        $("#note").val("");
        $("[name='_method']").val("POST");

        $("#createEmployeeModal").modal('toggle');
    });

    $('#employeeTable').on("click", ".edit-employee", function() {
        WinPos.Datatable.selectRow(this);

        $("#createEmployeeModalLabel").text("Update Employee | Employee ID: " + $(this).attr('data-id'))
        $("#employeeName").val($(this).attr('data-name'));
        $("#phone").val($(this).attr('data-phone'));
        $("#employeeID").val($(this).attr('data-id'));
        $("#dateOfBirth").val($(this).attr('data-dob'));
        $("#hireDate").val($(this).attr('data-hire-date'));
        $("#gender").val($(this).attr('data-gender'));
        $("#designationId").val($(this).attr('data-designation'));
        $("#jobTitle").val($(this).attr('data-job-title'));
        $("#status").val($(this).attr('data-status'));
        $("#note").val($(this).attr('data-note') || '');
        $("#saveUpdateEmployee").attr('data-type', 'update').html('<i class="fa-solid fa-floppy-disk"></i> Update');

        $("#createEmployeeModal").modal('show');
        $("[name='_method']").val("PUT");
    });

    $('#employeeTable').on("click", ".view-employee", function() {
        WinPos.Datatable.selectRow(this);
        WinPos.Employee.viewEmployee($(this).attr('data-id'));
    });

    $("#saveUpdateEmployee").on('click', function(event) {
        event.preventDefault();

        WinPos.Employee.saveEmployee(
            WinPos.Common.getFormData("#createEmployeeForm"),

            $("#saveUpdateEmployee").attr('data-type'),

            function() {
                $('#createEmployeeModal').modal('hide');
            });
    });

    $('#employeeTable').on("click", ".delete-employee", function() {
        WinPos.Datatable.selectRow(this);
        if (confirm("Are you sure you want to delete this employee?\nClick OK to continue or Cancel.")) {
            WinPos.Employee.deleteEmployee($(this).attr('data-id'));
        }
    });
});
</script>
@endsection

