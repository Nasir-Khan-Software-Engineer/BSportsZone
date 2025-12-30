@extends('layouts.main-layout')
@section('style')
<style>
input,
select,
textarea {
    border: 1px solid #333 !important;
}
</style>

@vite(['resources/css/service/service-style.css'])

@endsection

@php
$posid = auth()->user()->posid;
$imagePath = "/images/{$posid}/services/";
@endphp

@section('content')

<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Service List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchService" placeholder="Search Service">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button type="button" id="createNewService" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> New Service</button>

                </div>
            </div>
        </div>
        <div class="card-body p-1">
            <table class="table table-bordered" id="serviceTable">
                <thead>
                    <tr>
                        <th class="text-center align-middle" style="width: 5%;" scope="col">ID</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Code</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Image</th>
                        <th class="text-center align-middle" style="width: 25%;" scope="col">Name</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Price</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Beautician</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Created At</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Created By</th>
                        <th class="text-center align-middle" style="width: 10%;" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('service.add')
@include('service.edit')

@endsection



@section('script')
@vite(['resources/js/service/service-script.js'])
<script>
let serviceUrls = {
    'saveService': "{{ route('service.store') }}",
    'showService': "{{ route('service.show',['service' => 'serviceID']) }}",
    'editService': "{{ route('service.edit',['service' => 'serviceID']) }}",
    'updateService': "{{ route('service.update',['service' => 'serviceID']) }}",
    'deleteService': "{{ route('service.destroy',['service' => 'serviceID']) }}",
    'copyService': "{{ route('service.copy',['service' => 'serviceID']) }}",
    'serviceImagePath': "{{ asset($imagePath) }}",
    'defaultServiceImagePath': "{{ asset('images/default_service_img.png') }}",
    'datatable': "{{ route('service.datatable') }}"
};

$(document).ready(function() {
    WinPos.Datatable.initDataTable("#serviceTable", WinPos.Service.datatableConfiguration());


    $("#searchService").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    })

    $("#saveService").click(async function(event) {
        event.preventDefault();

        let serviceInfo = WinPos.Common.getFormData('#serviceAddForm');

        if (serviceInfo.image instanceof File) {
            serviceInfo.image = await fileToBase64(serviceInfo.image);
        }

        WinPos.Service.saveService(serviceInfo);
    });

    $("#createNewService").click(function() {
        debugger;
        $("#serviceAddForm")[0].reset();
        $('#imagePreview').css('background-image', '');
        $("#serviceBasicInfoTab").click();
        WinPos.Common.showBootstrapModal("serviceAddModal");
    })

    $("#updateService").click(async function(event) {
        event.preventDefault();
        let serviceInfo = WinPos.Common.getFormData('#serviceEditForm');
        let serviceID = $("#hiddenServiceID").val();

        if (serviceInfo.image instanceof File) {
            serviceInfo.image = await fileToBase64(serviceInfo.image);
        }

        WinPos.Service.updateService(serviceInfo, serviceID);
    });

    $("#image").change(function() {
        WinPos.Common.previewImage('#imagePreview', this);
    });

}); // end jquery

$(document).on('change', '#editImage', function() {
    console.log(this);
    WinPos.Common.previewImage('#imagePreviewEdit', this);
})

$(document).on('click', '.edit-service', function() {
    WinPos.Datatable.selectRow(this);
    let serviceID = $(this).data('serviceid');
    WinPos.Service.editService(serviceID);
})

$(document).on('click', '.copy-service', function() {
    WinPos.Datatable.selectRow(this);
    let serviceID = $(this).data('serviceid');
    WinPos.Service.copyService(serviceID);
})

$(document).on('click', '.delete-service', function(event) {
    WinPos.Datatable.selectRow(this);
    if (confirm("Are you sure you want to delete this user?\nClick OK to continue or Cancel.")) {
        let serviceID = $(this).data('serviceid');
        WinPos.Service.deleteService(serviceID);
    }
});

function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}
</script>
@endsection