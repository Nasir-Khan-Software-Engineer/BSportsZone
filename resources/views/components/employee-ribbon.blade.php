<div class="card border mb-1" id="employeeRibbon">
    <div class="card-body d-flex flex-wrap align-items-center p-2">
        <!-- Left Side: Employee Image -->
        <div class="mr-4">
            <img src="{{ asset('images/default-profile-picture.png') }}" alt="Employee" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
        </div>

        <!-- Right Side: Employee Info -->
        <div class="flex-grow-1">
            <div class="d-flex flex-wrap justify-content-between">
                <!-- Section 1: Basic Info (3 items) -->
                <div class="employee-section mr-4 mb-2">
                    <p class="mb-1"><strong>Name:</strong> {{ $employee->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Designation:</strong> {{ $employee->designationName ?? '-' }}</p>
                    <p class="mb-1"><strong>Job Title:</strong> {{ $employee->job_title ?? '-' }}</p>
                </div>

                <!-- Section 2: Personal Info (3 items) -->
                <div class="employee-section mr-4 mb-2">
                    <p class="mb-1"><strong>Phone:</strong> 
                        @if(hasAccess('show_phone'))
                            {{ $employee->phone ?? '-' }}
                        @else
                            {{ maskPhoneNumber($employee->phone ?? '-') }}
                        @endif
                    </p>
                    <p class="mb-1"><strong>Date of Birth:</strong> {{ $employee->formattedDob ?? '-' }}</p>
                    <p class="mb-1"><strong>Gender:</strong> {{ $employee->gender ?? '-' }}</p>
                </div>

                <!-- Section 3: Status & Hire Date (2 items) -->
                <div class="employee-section mb-2">
                    <p class="mb-1">
                        <strong>Status:</strong> 
                        <span class="badge {{ $employee->status === 'Active' ? 'badge-success' : 'badge-secondary' }}">
                            {{ $employee->status ?? '-' }}
                        </span>
                    </p>
                    <p class="mb-1"><strong>Hire Date:</strong> {{ $employee->formattedHireDate ?? '-' }}</p>
                </div>
            </div>
            
            <!-- Note Section (if exists) -->
            @if(!empty($employee->note))
            <div class="mt-2">
                <p class="mb-0">
                    <strong>Note:</strong> 
                    <span data-toggle="tooltip" data-placement="top" title="{{ $employee->note }}" style="cursor: help;">
                        {{ Str::limit($employee->note, 50) }}
                    </span>
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

