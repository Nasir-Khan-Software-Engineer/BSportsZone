WinPos.Shop = (function (Urls){
    var showShop = function (shopID){
        WinPos.Common.getAjaxCall(Urls.showShop.replace('shopID', shopID), function (response){
            if(response.status === 'success'){
                var shop = response.shop;
                
                $("#showName").html(shop.name);
                $("#showEmail").html(shop.email);
                $("#showPrimaryPhone").html(shop.primaryPhone);
                $("#showSecondaryPhone").html(shop.secondaryPhone);
                $("#showAddress").html(shop.address);
                $("#showDistrict").html(shop.district);
                $("#showDivision").html(shop.division);
                $("#showThana").html(shop.thana);
                $("#showAbout").html(shop.about);
                $("#showIs_active").html(shop.is_active? 'Yes' : 'No');
                $("#showShopID").html(' | Shop ID: '+shop.id);
                $("#shopBasicInfoTabShow").click();
                WinPos.Common.showBootstrapModal('showShopModal');
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var editShop = function (shopID){
        WinPos.Common.getAjaxCall(Urls.editShop.replace('shopID', shopID), function (response){
            if(response.status === 'success'){
                var shop = response.shop;

                $("#name").val(shop.name);
                $("#email").val(shop.email);
                $("#primaryPhone").val(shop.primaryPhone);
                $("#secondaryPhone").val(shop.secondaryPhone);
                $("#address").val(shop.address);
                $("#district").val(shop.district);
                $("#division").val(shop.division);
                $("#thana").val(shop.thana);
                $("#about").val(shop.about);
                $("#hiddenShopID").val(shop.id);

                $("#shopID").html(' | Shop ID: '+shop.id);

                $("#saveShop").hide();
                $("#updateShop").show();

                
                $("#shopBasicInfoTab").click();
                $("#shopAddEditModal").modal('show');

            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var saveShop = function (formData){
        WinPos.Common.postAjaxCall(Urls.saveShop, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.addNewRow(prepareDatatableRow(response.shop), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("shopAddEditModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateShop = function (formData, shopID){
        WinPos.Common.putAjaxCallPost(Urls.updateShop.replace("shopID", shopID), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.updateNewRow(prepareDatatableRow(response.shop), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("shopAddEditModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteShop = function (shopID){
        WinPos.Common.deleteAjaxCall(Urls.deleteShop.replace('shopID', shopID), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var isValidShop = function(formData){
        
        if (!formData.name || formData.name.trim().length < 3 || formData.name.trim().length > 200) {
            toastr.error("Name is not valid!");
            return false;
        }
    
        if (!formData.email || !WinPos.Common.isValidEmail(formData.email)) {
            toastr.error("Email is not valid!");
            return false;
        }

        if(!WinPos.Common.isValidPhoneNumber(formData.primaryPhone)){
            toastr.error("Primary Phone Number is not valid!");
            return false;
        }
        
        if (formData.secondaryPhone && (!WinPos.Common.isValidPhoneNumber(formData.secondaryPhone))) {
            toastr.error("Secondary Phone Number is not valid!");
            return false;
        }

        if(formData.primaryPhone === formData.secondaryPhone){
            toastr.error("Primary or Secondary Phone Number is not valid!");
            return false;
        }

        if (!formData.address || formData.address.trim().length < 3 || formData.address.trim().length > 300) {
            toastr.error("Address is not valid!");
            return false;
        }

        if (!formData.division || formData.division.trim().length < 3 || formData.division.trim().length > 100) {
            toastr.error("Division is not valid!");
            return false;
        }

        if (!formData.district || formData.district.trim().length < 3 || formData.district.trim().length > 100) {
            toastr.error("District is not valid!");
            return false;
        }

        if (!formData.thana || formData.thana.trim().length < 3 || formData.thana.trim().length > 100) {
            toastr.error("Area or Thana is not valid!");
            return false;
        }
    
        if (formData.about && (formData.about.trim().length < 3 || formData.about.trim().length > 1000)) {
            toastr.error("About is not valid!");
            return false;
        }

        return true;
    }

    var prepareDatatableRow = function (shop){
        let actionCell = [];

        actionCell.push('<button data-shopID="');
        actionCell.push(shop.id);
        actionCell.push('" class="btn thm-btn-bg thm-btn-text-color show-shop"><i class="fa-solid fa-eye"></i></button>');

        actionCell.push(' <button data-shopID="');
        actionCell.push(shop.id);
        actionCell.push('" class="btn thm-btn-bg thm-btn-text-color edit-shop"><i class="fa-solid fa-pen-to-square"></i></button>');

        actionCell.push(' <button data-shopID="');
        actionCell.push(shop.id);
        actionCell.push('" class="btn thm-btn-bg thm-btn-text-color delete-shop"><i class="fa-solid fa-trash"></i></button>');

        return [shop.id, shop.name, shop.primaryPhone, shop.address, actionCell.join("")];
    }

    var applyCssToNewlyAddedRow = function(row){
        let columns = $(row).find('td');

        columns.each(function(index){
            let col = $(this);

            if(index === columns.length-1){
                col.addClass('text-right');
            }else{
                col.addClass('text-left');
            }
            col.addClass('align-middle');
        });
    }

    return {
        saveShop: saveShop,
        updateShop: updateShop,
        deleteShop: deleteShop,
        showShop: showShop,
        editShop: editShop,
        isValidShop: isValidShop
    }
})(shopUrls);
