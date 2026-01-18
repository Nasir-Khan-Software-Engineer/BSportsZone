WinPos.Category = (function(Urls){
    // Auto-generate slug from category name
    var generateSlug = function(name) {
        return name.toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    };

    // Update slug when name changes (if slug is empty)
    var setupSlugAutoGeneration = function() {
        $('#createCategoryForm #categoryName').on('input', function() {
            let slugField = $('#createCategoryForm #categorySlug');
            if (!slugField.val() || slugField.data('auto-generated')) {
                let slug = generateSlug($(this).val());
                slugField.val(slug);
                slugField.data('auto-generated', true);
            }
        });

        // Mark slug as manually edited if user types in it
        $('#createCategoryForm #categorySlug').on('input', function() {
            $(this).data('auto-generated', false);
        });
    };

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

    var loadCategoryForEdit = function(categoryId, callback) {
        WinPos.Common.getAjaxCall(Urls.editCategory.replace('categoryid', categoryId), function(response) {
            if(response.status === 'success') {
                let cat = response.category;
                $('#createCategoryForm #categoryName').val(cat.name || '');
                $('#createCategoryForm #categorySlug').val(cat.slug || '').data('auto-generated', false);
                $('#createCategoryForm #categoryTitle').val(cat.title || '');
                $('#createCategoryForm #categoryKeyword').val(cat.keyword || '');
                $('#createCategoryForm #categoryDescription').val(cat.description || '');
                $('#createCategoryForm #categoryID').val(cat.id);
                if(callback) callback();
            } else {
                toastr.error('Failed to load category data');
            }
        });
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
        deleteCategory: deleteCategory,
        loadCategoryForEdit: loadCategoryForEdit,
        setupSlugAutoGeneration: setupSlugAutoGeneration
    }
})(CategoryUrls);
