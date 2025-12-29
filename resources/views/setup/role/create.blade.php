@extends('layouts.main-layout')

@section('title', 'Create Role')


@section('style')
@vite(['resources/css/custom-multiselect.css'])
@endsection

@section('content')
<div class="view-container mb-2">

    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Create Role</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('setup.role.index') }}" class="btn thm-btn-bg thm-btn-text-color"> <i class="fas fa-arrow-left"></i> Back</a>
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
            <form action="{{ route('setup.role.store') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Name<span class="text-danger required-star">*</span></label>
                                    <input type="text" class="form-control rounded" id="name" name="name" placeholder="Name" minlength="3" maxlength="100" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control rounded" cols="30" rows="8" placeholder="Description" maxlength="255"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn thm-btn-bg thm-btn-text-color w-100"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-5 mt-2">
                                <label for="left-search">Available User Access</label>
                                <input id="left-search" type="text" class="form-control search-box rounded" placeholder="Search items...">
                                <div id="left-list" class="list-container">
                                    @foreach ($allAccessRights as $access)
                                    <div title="{{ $access->description }}" class="list-item" data-id="{{ $access->id }}">{{ $access->title }}</div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Middle Buttons -->
                            <div class="col-md-2 d-flex flex-column justify-content-center align-items-center">
                                <button type="button" id="move-right" class="btn thm-btn-bg thm-btn-text-color btn-move">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                                <button type="button" id="move-all-right" class="btn thm-btn-bg thm-btn-text-color btn-move">
                                    <i class="fas fa-angle-double-right"></i>
                                </button>
                                <button type="button" id="move-left" class="btn thm-btn-bg thm-btn-text-color btn-move">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                <button type="button" id="move-all-left" class="btn thm-btn-bg thm-btn-text-color btn-move">
                                    <i class="fas fa-angle-double-left"></i>
                                </button>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-5">
                                <label for="right-search">Assigned User Access<span class="text-danger required-star">*</span></label>
                                <input id="right-search" type="text" class="form-control search-box rounded" placeholder="Search items...">
                                <div id="right-list" class="list-container">
                                </div>
                            </div>
                        </div>
                        <!-- Hidden input for storing assigned IDs -->
                        <select id="assigned_ids" name="assignedAccess[]" multiple hidden>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
@vite(['resources/js/custom-multiselect.js'])
@endsection