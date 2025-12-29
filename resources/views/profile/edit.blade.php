@extends('layouts.main-layout')

@section('style')
<style>
.fa-chevron-down {
    transition: transform 0.3s ease;
}

.accordion .card-header .btn-link {
    text-decoration: none;
    color: #5a5c69 !important;
}

.accordion .card-header .btn-link:hover {
    text-decoration: none;
    color: #3a3b45 !important;
}

.accordion .card-header .btn-link:focus {
    text-decoration: none;
    box-shadow: none;
}
</style>
@endsection

@section('content')

<div class="row">
    {{-- Name & Email Card --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                Update Profile Info
            </div>
            <div class="card-body">
                <form id="profileInfoForm" autocomplete="off">
                    <div class="mb-3">
                        <label for="nameInput" class="form-label">Name</label>
                        <input type="text" id="nameInput" name="name" value="{{ old('name', $user->name) }}" class="form-control rounded" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="emailInput" class="form-label">Email</label>
                        <input readonly type="email" id="emailInput" name="email" value="{{ old('email', $user->email) }}" class="form-control rounded" required autocomplete="off">
                    </div>
                    <button id="updateInfoBtn" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">Update Info</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Password Change Card --}}
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                Change Password
            </div>
            <div class="card-body">
                <form id="passwordChangeForm" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label for="oldPasswordInput" class="form-label">Old Password</label>
                        <input type="password" id="oldPasswordInput" name="old_password" class="form-control rounded" required autocomplete="current-password">
                    </div>
                    <div class="mb-3">
                        <label for="newPasswordInput" class="form-label">New Password</label>
                        <input type="password" id="newPasswordInput" name="password" class="form-control rounded" required autocomplete="new-password">
                        <small class="form-text text-muted">
                            Password must be at least 8 characters with uppercase, lowercase, number, and special character.
                        </small>
                        <div id="newPassword-strength" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmNewPasswordInput" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirmNewPasswordInput" name="password_confirmation" class="form-control rounded" required autocomplete="new-password">
                        <div id="newPassword-match" class="mt-2"></div>
                    </div>
                    <button id="updatePasswordBtn" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Access Information Section --}}
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Access Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Role Information --}}
                    <div class="col-md-4">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Role</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->role->name ?? 'No Role Assigned' }}</div>
                                        @if($user->role && $user->role->description)
                                        <div class="text-xs text-gray-600 mt-1">{{ $user->role->description }}</div>
                                        @endif
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Access Rights Count --}}
                    <div class="col-md-4">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Permissions</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->accessRights()->count() }}</div>
                                        <div class="text-xs text-gray-600 mt-1">Access Rights Granted</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-key fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modules Count --}}
                    <div class="col-md-4">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Modules</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $groupedAccessRights->count() }}</div>
                                        <div class="text-xs text-gray-600 mt-1">Accessible Modules</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Access Rights Details --}}
                @if($groupedAccessRights->count() > 0)
                <h6 class="text-gray-800 mt-2">Detailed Access Rights</h6>

                <div class="accordion" id="accessRightsAccordion">
                    <div class="row">
                        @foreach($groupedAccessRights as $module => $accessRights)
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-header" id="heading{{ $loop->index }}">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-gray-800 font-weight-bold" type="button" data-toggle="collapse" data-target="#collapse{{ $loop->index }}"
                                            aria-expanded="false" aria-controls="collapse{{ $loop->index }}">
                                            <i class="fas fa-folder-open mr-2"></i>{{ $module }}
                                            <span class="badge badge-secondary ml-2">{{ $accessRights->count() }}</span>
                                            <i class="fas fa-chevron-down float-right mt-1"></i>
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapse{{ $loop->index }}" class="collapse" aria-labelledby="heading{{ $loop->index }}" data-parent="#accessRightsAccordion">
                                    <div class="card-body p-2">
                                        <div class="list-group list-group-flush">
                                            @foreach($accessRights as $accessRight)
                                            <div class="list-group-item border-0 px-2 py-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 text-sm font-weight-bold">{{ $accessRight->title }}</h6>
                                                        @if($accessRight->description)
                                                        <p class="mb-1 text-xs text-gray-600">{{ $accessRight->description }}</p>
                                                        @endif
                                                    </div>
                                                    <span class="badge badge-success badge-sm">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col-6 -->
                        @endforeach
                    </div> <!-- end row -->
                </div> <!-- end accordion -->


                @else
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-gray-600">No Access Rights Assigned</h5>
                    <p class="text-gray-500">Contact your administrator to assign access rights to your role.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

@vite(['resources/js/setup/profile-script.js'])

<script>
let profileUrls = {
    'updateInfo': "{{ route('setup.profile.updateInfo') }}",
    'updatePassword': "{{ route('setup.profile.updatePassword') }}"
};

$(document).ready(function() {

    $("#updateInfoBtn").click(function(event) {
        event.preventDefault();
        let data = WinPos.Common.getFormData('#profileInfoForm');
        WinPos.Profile.updateInfo(data);
    })
    $("#updatePasswordBtn").click(function(event) {
        event.preventDefault();
        let data = WinPos.Common.getFormData('#passwordChangeForm');
        WinPos.Profile.updatePassword(data);
    })

    // Accordion functionality for access rights
    $('#accessRightsAccordion').on('show.bs.collapse', function(e) {
        // Rotate chevron icon when expanding
        $(e.target).prev().find('.fa-chevron-down').css('transform', 'rotate(180deg)');
    });

    $('#accessRightsAccordion').on('hide.bs.collapse', function(e) {
        // Rotate chevron icon back when collapsing
        $(e.target).prev().find('.fa-chevron-down').css('transform', 'rotate(0deg)');
    });

    // Password strength validation for change password
    $('#newPasswordInput').on('input', function() {
        var password = $(this).val();
        var strength = WinPos.Common.checkPasswordStrength(password);
        WinPos.Common.displayPasswordStrength(strength, '#newPassword-strength');
    });

    // Password confirmation validation for change password
    $('#confirmNewPasswordInput').on('input', function() {
        var password = $('#newPasswordInput').val();
        var confirmPassword = $(this).val();
        WinPos.Common.checkPasswordMatch(password, confirmPassword, '#newPassword-match');
    });

    // Form submission validation for password change
    $('#passwordChangeForm').on('submit', function(e) {
        var password = $('#newPasswordInput').val();
        var confirmPassword = $('#confirmNewPasswordInput').val();

        if (!WinPos.Common.validatePasswordStrength(password)) {
            e.preventDefault();
            toastr.error('New password does not meet strength requirements.');
            return false;
        }

        if (password !== confirmPassword) {
            e.preventDefault();
            toastr.error('New passwords do not match.');
            return false;
        }
    });

}); // end jquery
</script>
@endsection