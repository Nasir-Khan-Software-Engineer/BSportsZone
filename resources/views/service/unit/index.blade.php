@extends('layouts.main-layout')

@section('style')
@endsection

@section('content')
<div class="view-container mb-2">
    <div class="card full-height-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 align-items-center">
                <h3>Brand List</h3>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control data-table-search" id="searchUnit" placeholder="Search Unit">
                <div class="vr mx-1"></div>
                <div class="text-right">
                    <button type="button" id="createNewUnit" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm"><i class="fa-solid fa-plus"></i> New Unit</button>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            @include('service.unit.all')
        </div>
    </div>
</div>

@include('service.unit.show')
@include('service.unit.addEdit')

@endsection

@section('script')
@vite(['resources/js/service/unit-script.js'])
<script>
let unitUrls = {
    'saveUnit': "{{ route('service.unit.store') }}",
    'editUnit': "{{ route('service.unit.edit',['unit' => 'unitID']) }}",
    'updateUnit': "{{ route('service.unit.update',['unit' => 'unitID']) }}",
    'deleteUnit': "{{ route('service.unit.destroy',['unit' => 'unitID']) }}"
};

$(document).ready(function() {
    WinPos.Datatable.initDataTable("#unitTable", {
        order: [
            [0, 'desc']
        ]
    });

    $("#searchUnit").on("keyup search input paste cut", function() {
        WinPos.Datatable.filter($(this).val());
    });

    $("#saveUnit").click(function(event) {
        event.preventDefault();
        let data = WinPos.Common.getFormData('#unitAddEditForm');
        if (WinPos.Unit.isValidUnit(data)) {
            WinPos.Unit.saveUnit(data);
        }
    })

    $("#createNewUnit").click(function() {
        $("#unitAddEditForm")[0].reset();
        $("#unitID").html('');
        $("#unitBasicInfoTab").click();
        $("#saveUnit").show();
        $("#updateUnit").hide();
        $("#isActiveDiv").hide();
        WinPos.Common.showBootstrapModal("unitAddEditModal");
    })

    $("#updateUnit").click(function(event) {
        event.preventDefault();
        let data = WinPos.Common.getFormData('#unitAddEditForm');
        data['isActive'] = $("#isActive").is(':checked') ? 1 : 0;
        let unitID = $("#hiddenUnitID").val();
        if (WinPos.Unit.isValidUnit(data)) {
            WinPos.Unit.updateUnit(data, unitID);
        }
    })
}); // end jquery

$(document).on('click', '.show-unit', function() {
    let unitID = Number($(this).attr("data-unitID"));
    WinPos.Unit.showUnit(unitID);
});

$(document).on('click', '.edit-unit', function() {
    WinPos.Datatable.selectRow(this);
    let unitID = Number($(this).attr("data-unitID"));
    WinPos.Unit.editUnit(unitID);
});

$(document).on('click', '.delete-unit', function() {
    WinPos.Datatable.selectRow(this);
    var unitID = $(this).attr('data-unitID');
    let confirmation = confirm('Are you sure you want to delete this unit?');
    if (confirmation) {
        WinPos.Unit.deleteUnit(unitID);
    } else {
        alert('Deletion canceled');
    }
});
</script>
@endsection