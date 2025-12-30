WinPos.Supplier = (function(Urls){
    var getCreateSupplierForm = function (containerId, callback){
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

    var getUpdateSupplierForm = function (containerId, supplierId, callback){
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

    var validateSupplier = function (formData, type, callback){
        let name = $('#createSupplierForm #supplierName').val().trim();
        let phone = $('#createSupplierForm #supplierPhone').val().trim();
        let email = $('#createSupplierForm #supplierEmail').val().trim();
        let address = $('#createSupplierForm #shortAddress').val().trim();

        if(name.length < 1 || name.length > 100){
            toastr.error("Supplier name must be between 1 to 100 characters");
            $('#createSupplierForm #supplierName').addClass('is-invalid');
            return false;
        }

        if(!WinPos.Common.isValidPhoneNumber(phone)){
            toastr.error("Phone number is invalid");
            $('#createSupplierForm #supplierPhone').addClass('is-invalid');
            return false;
        }
        
        if(email != ''){
            if(!WinPos.Common.isValidEmail(email)){
                toastr.error("Email is invalid");
                $('#createSupplierForm #supplierEmail').addClass('is-invalid');
                return false;
            }
        }

        if(address.length < 1){
            toastr.error("Address is required");
            $('#createSupplierForm #shortAddress').addClass('is-invalid');
            return false;
        }

        if(type === 'create'){
            save(formData, callback);
        }else {
            let id = $('#createSupplierForm #supplierId').val().trim();
            if(id === "" || id === "0"){
                toastr.error("Something went wrong. Please try again.");
                return false;
            }
            update(formData, id, callback);
        }
    }

    var save = function (formData, callback){
        WinPos.Common.postAjaxCall(Urls.saveSupplier, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.addNewRow(prepareSupplierRow(response.supplier), true);
                applyCssToNewlyAddedRow(row, response.supplier.id);
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
        WinPos.Common.putAjaxCallPost(Urls.updateSupplier.replace("supplierid", supplierId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.updateNewRow(prepareSupplierRow(response.supplier), true);
                applyCssToNewlyAddedRow(row, response.supplier.id);
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

    var deleteSupplier = function (supplierId){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteSupplier.replace('supplierid', supplierId), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                if(typeof response.errors !== "undefined"){
                    WinPos.Common.showValidationErrors(response.errors);
                }else{
                    WinPos.Common.somethingWrongToast(response);
                }
            }
        });
    }

    var prepareSupplierRow = function(data){
        let address = (data.address && data.address.length > 30) ? data.address.substring(0, 30) + '...' : (data.address || '-');
        let productsCount = data.products_count || 0;
        let phone = data.phone_1 || data.phone || '-';

        return [
            data.id,
            data.name,
            phone,
            address,
            productsCount,
            WinPos.Common.dataTableActionCell(data.id, 'supplier', 'data-name="'+ data.name +'"', ['edit', 'delete'])
        ];
    }

    var applyCssToNewlyAddedRow = function(row, supplierId){
        let columns = $(row).find('td');

        columns.each(function(index){
            let col = $(this);
            col.addClass('text-center');
            col.addClass('align-middle');
        });

        // Add view button to the action column
        if(supplierId) {
            let actionCell = $(row).find('td').last();
            let viewButton = $('<a>')
                .attr('href', Urls.showSupplier.replace('supplierid', supplierId))
                .addClass('btn btn-sm thm-btn-bg thm-btn-text-color')
                .attr('data-toggle', 'tooltip')
                .attr('title', 'View Details')
                .html('<i class="fa-solid fa-eye"></i>');
            $(actionCell).prepend(viewButton);
        }
    }

    return {
        getCreateSupplierForm: getCreateSupplierForm,
        getUpdateSupplierForm: getUpdateSupplierForm,
        saveSupplier: validateSupplier,
        deleteSupplier: deleteSupplier,
    }
})(SupplierUrls);
