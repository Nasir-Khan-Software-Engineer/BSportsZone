WinPos.Category = (function(Urls){
    var validateCategory = function (formData, type, callback){

        let category = $('#createCategoryForm #categoryName').val().trim();
        let categoryRegex = new RegExp("^[A-Za-z][A-Za-z0-9 _-]*$");

        if(!categoryRegex.test(category)){
            toastr.error("Category name is not valid. Supported character are A-Z, a-z, 0-9, _ and -");
            $('#createCategoryForm #categoryName').addClass('is-invalid');

            return false;
        }

        if(category.length < 3 || category.length > 100){
            toastr.error("Category name must be between 3 to 100 characters");
            $('#createCategoryForm #categoryName').addClass('is-invalid');

            return false;
        }

        if(type === 'create'){
            save(formData, callback);
        }else{
            let id = $('#createCategoryForm #categoryID').val().trim();
            if(id === "" || id === "0"){
                toastr.error("Something went wrong. Please try again.");
                return false;
            }

            update(formData, id, callback);
        }
    }

    var save = function (formData, callback){
        WinPos.Common.postAjaxCall(Urls.saveCategory, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.addNewRow(prepareCategoryRow(response.category), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                callback();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var update = function (formData, id, callback){
        WinPos.Common.putAjaxCallPost(Urls.updateCategory.replace('categoryid', id), JSON.stringify(formData), function(response){
            if(response.status === 'success'){
                let row = WinPos.Datatable.updateNewRow(prepareCategoryRow(response.category), true);
                applyCssToNewlyAddedRow(row);
                toastr.success(response.message);
                callback();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        })
    }

    var deleteCategory = function (id){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteCategory.replace('categoryid', id), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        })
    }

    var prepareCategoryRow = function(data){
        return [
            data.id,
            data.name,
            WinPos.Common.dataTableCreatedOnCell(data.formattedTime, data.formattedDate),
            data.createdBy,
            WinPos.Common.dataTableActionCell(data.id, 'category', 'data-name="'+ data.name +'"',['edit', 'delete'])
        ];
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
        saveCategory: validateCategory,
        deleteCategory: deleteCategory
    }
})(CategoryUrls);
