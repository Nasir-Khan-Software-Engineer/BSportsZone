@extends('layouts.main-layout')

@section('title', 'Create User')

@section('style')
@vite(['resources/css/setup/user-setup-style.css'])
@endsection

@section('content')
<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Create User</h3>
            <a href="{{ route('setup.user.index') }}" class="btn thm-btn-bg thm-btn-text-color"> <i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>

        <div class="card-body p-3">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if(session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                    @endif
                    <form id="createUserForm" action="{{ route('setup.user.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name">Name <span class="text-danger required-star">*</span></label>
                            <input type="text" name="name" id="name" class="form-control rounded @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="role_id">Role <span class="text-danger required-star">*</span></label>
                            <select name="role_id" id="role_id" class="form-control rounded @error('role_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('role_id') == '' ? 'selected' : '' }}>Select a role</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email <span class="text-danger required-star">*</span></label>
                            <input type="email" name="email" id="email" class="form-control rounded @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="phoneNumber">Phone Number <span class="text-danger required-star">*</span></label>
                            <input required autocomplete="off" type="text" name="phone" id="phoneNumber" class="form-control rounded @error('phone') is-invalid @enderror" placeholder="Phone Number" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password <span class="text-danger required-star">*</span></label>
                            <input autocomplete="new-password" type="password" name="password" id="password" class="form-control rounded @error('password') is-invalid @enderror" placeholder="Password" required>
                            <small class="form-text text-muted">
                                Password must be at least 8 characters with uppercase, lowercase, number, and special character.
                            </small>
                            <div id="password-strength" class="mt-2"></div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation">Confirm Password <span class="text-danger required-star">*</span></label>
                            <input autocomplete="new-password" type="password" name="password_confirmation" id="password_confirmation" class="form-control rounded @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password" required>
                            <div id="password-match" class="mt-2"></div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-right">
                            <button class="btn thm-btn-bg thm-btn-text-color" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@vite(['resources/js/setup/user-setup-script.js'])
<script>
$(document).ready(function() {
    WinPos.UserSetup.initPasswordValidation();
});
</script>
@endsection