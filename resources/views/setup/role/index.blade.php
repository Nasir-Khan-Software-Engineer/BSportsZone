@extends('layouts.main-layout')

@section('style')

@endsection

@section('content')


<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Role List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchRole" placeholder="Search Role">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <a data-toggle="tooltip" title="Create New Role" href="{{ route('setup.role.create') }}" class="btn thm-btn-bg thm-btn-text-color btn-sm"><i class="fa-solid fa-plus"></i> New Role</a>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered datatable" id="roleTable">
                <thead>
                    <tr>
                        <th class="text-left" scope="col">ID</th>
                        <th class="text-left" scope="col">Name</th>
                        <th class="text-left" scope="col">Users</th>
                        <th class="text-left" scope="col">Created On</th>
                        <th class="text-left" scope="col">Updated On</th>
                        <th class="text-right" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td class="text-left">{{$role->id}}</td>
                        <td class="text-left">{{$role->name}}</td>
                        <td class="text-left">{{$role->users->count()}}</td>
                        <td class="text-left">{{$role->created_at}}</td>
                        <td class="text-left">{{$role->updated_at}}</td>
                        <td class="text-right">
                            <button data-roleID="{{$role->id}}" class='btn thm-btn-bg thm-btn-text-color show-role btn-sm'><i class='fa-solid fa-eye'></i></button>
                            <a href="{{ route('setup.role.edit', $role->id) }}" class="btn thm-btn-bg thm-btn-text-color btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                            <button data-roleID="{{$role->id}}" class='btn thm-btn-bg thm-btn-text-color delete-role btn-sm'><i class='fa-solid fa-trash'></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@include('setup.role.show')

@endsection

@section('script')
@vite(['resources/js/setup/role-script.js'])
<script>
let roleUrls = {
    'showRole': "{{ route('setup.role.show',['role' => 'roleID']) }}",
    'deleteRole': "{{ route('setup.role.destroy',['role' => 'roleID']) }}"
};

$(document).ready(function() {

    WinPos.Datatable.initDataTable("#roleTable");

}); // end jquery



$(document).on('click', '.show-role', function() {
    let roleID = Number($(this).attr("data-roleID"));
    WinPos.Role.showRole(roleID);
});

$(document).on('click', '.delete-role', function() {
    WinPos.Datatable.selectRow(this);
    var roleID = $(this).attr('data-roleID');
    let confirmation = confirm('Are you sure you want to delete this role?');
    if (confirmation) {
        WinPos.Role.deleteRole(roleID);
    } else {
        alert('Deletion canceled');
    }
});
</script>
@endsection