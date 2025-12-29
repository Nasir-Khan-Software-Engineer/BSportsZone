WinPos.Brand = (function (Urls){
    /// public methods

    var validateBrand = function (formData, type){
        // let brand = $('#createBrandForm #brandName').val().trim();
        // let brandRegex = new RegExp("^[a-zA-Z ]*$");

        // if(!brandRegex.test(brand) || brand.length < 1 || brand.length > 15){
        //     toastr.error("Brand name is not valid.");
        //     $('#createBrandForm #brandName').addClass('is-invalid');
        //     return false;
        // }

        if(type === 'create'){
            save(formData);
        }else {
            update(formData, formData.brandId);
        }
    }

    var createBrandForm = function (containerId, callback){
        WinPos.Common.getAjaxCall(Urls.createBrand, function (response){
            if(typeof response === "string"){
                $(containerId).html("");
                $(containerId).html(response);
            }else if(typeof response === "object" && typeof response.errors !== "undefined"){  
                WinPos.Common.showValidationErrors(response.errors);  
            }
            callback(); 
        });
    }

    var updateBrandForm = function (containerId, brandId, callback){
        WinPos.Common.getAjaxCall(Urls.editBrand.replace("brandid", brandId), function (response){
            if(typeof response === "string"){
                $(containerId).html("");
                $(containerId).html(response);
            }else if(typeof response === "object" && typeof response.errors !== "undefined"){  
                WinPos.Common.showValidationErrors(response.errors);  
            }
            callback();
        });
    }

    var deleteBrand = function (brandId){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteBrand.replace('brandid', brandId), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors); 
            }
        });
    }

    /// private methods

    var save = function (formData){
        WinPos.Common.postAjaxCall(Urls.saveBrand, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.addNewRow(prepareBrandRow(response.brand), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
            }else{
                if (typeof response.errors != 'undefined') {
                    WinPos.Common.showValidationErrors(response.errors);
                }else{
                    toastr.error(response.message);
                }  
            }
        });
    }

    var update = function (formData, brandId){
        WinPos.Common.putAjaxCallPost(Urls.updateBrand.replace("brandid", brandId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.updateNewRow(prepareBrandRow(response.brand), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
            }else{
                
                if(typeof response.errors != 'undefined'){
                    WinPos.Common.showValidationErrors(response.errors);
                }else{
                    toastr.error(response.message);
                }              
            }
        });
    }

    var prepareBrandRow = function (data){
        // let description = (data.description.length > 30)? data.description.substring(0, 30) + '...':data.description;

        return [
            data.id, 
            data.name, 
            WinPos.Common.dataTableCreatedOnCell(data.formattedTime, data.formattedDate), 
            data.createdBy, 
            WinPos.Common.dataTableActionCell(data.id, 'brand','',['edit', 'delete'])];
    }

    var applyCssToNewlyAddedRow = function(row){
        let columns = $(row).find('td');

        columns.each(function(index){
            let col = $(this);
            col.addClass('text-center');
            col.addClass('align-middle');
        });
    }

    return {
        saveBrand: validateBrand,
        deleteBrand: deleteBrand,
        getCreateBrandForm: createBrandForm,
        getUpdateBrandForm: updateBrandForm,
    }
})(BrandUrls);
