WinPos.Supplier = (function (Urls){
    var createSupplierForm = function (containerId, callback){
        WinPos.Common.getAjaxCall(Urls.createSupplier, function (response){
            if(typeof response === "string"){
                $(containerId).html("");
                $(containerId).html(response);
            }else if(typeof response === "object" && typeof response.errors !== "undefined"){  
                WinPos.Common.showValidationErrors(response.errors);  
            }
            callback();
        });
    }

    var validateSupplier = function (formData, type, callback){
        let supplierRegex = new RegExp("^[a-zA-Z ]*$");

        if(!supplierRegex.test(formData.name) || formData.name.length < 3 || formData.name.length > 15){
            toastr.error("Supplier name is not valid.");
            $('#createSupplierForm #supplierName').addClass('is-invalid');
            return false;
        }

        if(!supplierRegex.test(formData.address) || formData.name.address < 5 || formData.name.address > 50){
            toastr.error("Supplier address is not valid.");
            $('#createSupplierForm #shortAddress').addClass('is-invalid');
            return false;
        }

        if(!WinPos.Common.isValidPhoneNumber(formData.phone)){
            toastr.error("Phone number is invalid");
            $('#createSupplierForm #supplierPhone').addClass('is-invalid');
            return false;
        }
        
        if(formData.email != ''){
            if(!WinPos.Common.isValidEmail(formData.email)){
                toastr.error("Email is invalid");
                $('#createSupplierForm #supplierEmail').addClass('is-invalid');
                return false;
            }
        }

        if(type === 'create'){
            save(formData, callback);
        }else {
            update(formData, formData.supplierId, callback);
        }
    }

    var save = function (formData, callback){
        WinPos.Common.postAjaxCall(Urls.saveSupplier, JSON.stringify(formData), function (response){
            console.log(response);
            if(response.status === 'success'){
                let row = WinPos.Datatable.addNewRow(prepareCategoryRow(response.supplier), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                callback();
            }else{
                if(typeof response.errors !== "undefined"){
                    WinPos.Common.showValidationErrors(response.errors);
                }else{
                    WinPos.Common.somethingWrongToast(response);
                }
            }
        });
    }

    var update = function (formData, supplierId, callback){
        WinPos.Common.postAjaxCall(Urls.updateSupplier.replace("supplierid", supplierId), JSON.stringify(formData), function (response){
            console.log(response);
            if(response.status === 'success'){
                let row = WinPos.Datatable.updateNewRow(prepareCategoryRow(response.supplier), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                callback();
            }else{
                if(typeof response.errors !== "undefined"){
                    WinPos.Common.showValidationErrors(response.errors);
                }else{
                    WinPos.Common.somethingWrongToast(response);
                }
            }
        });
    }
    
    var updateSupplierForm = function (containerId, supplierId, callback){
        WinPos.Common.getAjaxCall(Urls.editSupplier.replace("supplierid", supplierId), function (response){
            if(typeof response === "string"){
                $(containerId).html("");
                $(containerId).html(response);
            }else if(typeof response === "object" && typeof response.errors !== "undefined"){  
                WinPos.Common.showValidationErrors(response.errors);  
            }
            callback();
        });
    }

    var deleteSupplier = function (brandId){
        WinPos.Common.deleteAjaxCall(Urls.deleteSupplier.replace('supplierid', brandId), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }
    var prepareCategoryRow = function(data){
        let address = (data.address.length > 30)? data.address.substring(0, 30) + '...':data.address;

        return [
            data.name,
            data.phone,
            address,
            WinPos.Common.dataTableCreatedOnCell(data.formattedTime, data.formattedDate),
            WinPos.Common.dataTableActionCell(data.id, 'supplier', '')
        ];
    }

    var applyCssToNewlyAddedRow = function(row){
        let columns = $(row).find('td');

        columns.each(function(index){
            let col = $(this);

            if(index === 0){
                col.addClass('text-left');
            }else if(index === columns.length-1){
                col.addClass('text-right');
            }else{
                col.addClass('text-center');
            }
            col.addClass('align-middle');
        });
    }

    return {
        getCreateSupplierForm: createSupplierForm,
        getUpdateSupplierForm: updateSupplierForm,
        saveSupplier: validateSupplier,
        deleteSupplier: deleteSupplier,
    }
})(SupplierUrls);
