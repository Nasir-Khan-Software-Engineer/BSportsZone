@extends('layouts.main-layout')

@section('style')

@vite(['resources/css/setup/user-setup-style.css'])
@endsection

@section('content')
<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>User List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchUser" placeholder="Search User">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <a data-toggle="tooltip" title="Create New User Account" href="{{ route('setup.user.create') }}" class="btn thm-btn-bg thm-btn-text-color btn-sm"><i class="fa-solid fa-plus"></i> New User</a>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered datatable" id="userTable">
                <thead>
                    <tr>
                        <th scope="col" style="width: 5%;" class="text-left">ID</th>
                        <th scope="col" style="width: 20%;">Name</th>
                        <th scope="col" class="text-center" style="width: 20%;">Email</th>
                        <th scope="col" class="text-center" style="width: 15%;">Phone Number</th>
                        <th scope="col" class="text-center" style="width: 10%;">Role</th>
                        <th scope="col" class="text-center" style="width: 15%;">Created On</th>
                        <th scope="col" class="text-right" style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="align-middle text-left">{{$user->id}}</td>
                        <td class="align-middle">{{$user->name}}</td>
                        <td class="text-center align-middle">{{ $user->email }}</td>
                        <td class="text-center align-middle">{{$user->phone}}</td>
                        <td class="text-center align-middle">{{$user->role->name}}</td>
                        <td class="text-center align-middle">
                            <div class="text-center d-inline-block px-2" style="line-height: normal;">
                                {{ $user->formattedTime }}
                                <br>
                                {{ $user->formattedDate }}
                            </div>
                        </td>
                        <td class="text-right align-middle">
                            <a data-toggle="tooltip" title="Edit This User Information" href="{{ route('setup.user.edit', ['user' => $user->id]) }}" class="btn thm-btn-bg thm-btn-text-color btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    WinPos.Datatable.initDataTable('#userTable');

    $("#searchUser").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })

    $('[data-toggle="tooltip"]').tooltip({
        html: true,
        placement: "auto",
        boundary: "window"
    });

});
</script>
@endsection