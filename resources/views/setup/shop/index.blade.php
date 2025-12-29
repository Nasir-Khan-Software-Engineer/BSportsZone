@extends('layouts.main-layout')

@section('style')
@endsection

@section('content')

<div class="row">
    <div class="col-12 col-md-3 text-left">
        <h3>All Shop</h3>
    </div>
    <div class="col-12 col-md-9 text-right">
        <button type="button" id="createNewShop" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm">Create New Shop</button>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        @include('setup.shop.allShop')
    </div>
</div>


<!-- shop show modal  -->
@include('setup.shop.show')

<!-- shop add edit modal -->
@include('setup.shop.addEdit')


@endsection

@section('script')
@vite(['resources/js/setup/shop-script.js'])
<script>
    let shopUrls = {
        'saveShop': "{{ route('setup.shop.store') }}",
        'showShop': "{{ route('setup.shop.show',['shop' => 'shopID']) }}",
        'editShop': "{{ route('setup.shop.edit',['shop' => 'shopID']) }}",
        'updateShop': "{{ route('setup.shop.update',['shop' => 'shopID']) }}",
        'deleteShop': "{{ route('setup.shop.destroy',['shop' => 'shopID']) }}"
    };

    $(document).ready(function () {
        WinPos.Datatable.initDataTable("#shopTable");
        $("#saveShop").click(function (event) {
            event.preventDefault();
            let data = WinPos.Common.getFormData('#shopAddEditForm');
            if (WinPos.Shop.isValidShop(data)) {
                WinPos.Shop.saveShop(data);
            }
        })

        $("#createNewShop").click(function () {
            $("#shopAddEditForm")[0].reset();
            $("#shopID").html('');
            $("#shopBasicInfoTab").click();
            $("#saveShop").show();
            $("#updateShop").hide();
            $("#isActiveDiv").hide();
            WinPos.Common.showBootstrapModal("shopAddEditModal");
        })

        $("#createShop").click(function (event) {
            event.preventDefault();
            WinPos.Shop.createShop(WinPos.Common.getFormData('#shopAddEditForm'));
            WinPos.Common.hideBootstrapModal("shopAddEditModal");
        })

        $("#updateShop").click(function (event) {
            event.preventDefault();
            let data = WinPos.Common.getFormData('#shopAddEditForm');
            let shopID = $("#hiddenShopID").val();
            if (WinPos.Shop.isValidShop(data)) {
                WinPos.Shop.updateShop(data, shopID);
            }
        })

    });

    $(document).on('click', '.show-shop', function () {
        let shopID = Number($(this).attr("data-shopID"));
        WinPos.Shop.showShop(shopID);
        $("#shopBasicInfoTabShow").click();
        WinPos.Common.showBootstrapModal('shopModalShow');
    });

    $(document).on('click', '.edit-shop', function () {
        WinPos.Datatable.selectRow(this);
        let shopID = Number($(this).attr("data-shopID"));
        WinPos.Shop.editShop(shopID);
    });

    $(document).on('click', '.delete-shop', function () {
        WinPos.Datatable.selectRow(this);
        var shopID = $(this).attr('data-shopID');
        let confirmation = confirm('Are you sure you want to delete this shop?');
        if (confirmation) {
            WinPos.Shop.deleteShop(shopID);
        } else {
            alert('Deletion canceled');
        }
    });

</script>

@endsection
