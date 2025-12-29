@extends('layouts.main-layout')

@section('title', 'Edit User')


@section('style')
@vite(['resources/css/setup/user-setup-style.css'])
@endsection

@section('content')
<div class="view-container mb-2">

    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Edit User</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('setup.user.index') }}" class="btn thm-btn-bg thm-btn-text-color thm-btn-bg thm-btn-text-color"> <i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body p-1">
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
            <form action="{{ route('setup.user.update', ['user' => $user->id]) }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger required-star">*</span></label>
                                    <input type="text" class="form-control rounded" id="name" name="name"
                                        value="{{ $user->name }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="phone">Phone Number <span class="text-danger required-star">*</span></label>
                                    <input type="text" class="form-control rounded" id="phone" name="phone"
                                        value="{{ $user->phone }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="role_id">Role <span class="text-danger required-star">*</span></label>
                                    <select name="role_id" id="role_id" class="form-control rounded" required>
                                        @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $role->id == $user->role_id ? 'selected' : '' }}>{{ $role->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group text-right">
                                    <button type="submit" class="btn thm-btn-bg thm-btn-text-color"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')

<script>

</script>
@endsection