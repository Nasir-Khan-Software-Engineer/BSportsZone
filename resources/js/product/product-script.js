WinPos.Product = (function (Urls){

    var datatableConfiguration = function (){
        return {
            order: [[0, 'desc']],
            serverSide: true,
            processing: true,
            ajax: {
                url: Urls.datatable,
                type: 'GET',
                data: function (d) {
                    d.search = $('.data-table-search').val();
                }
            },
            columns: [
                { data: 'id', name: 'id', orderable: true },
                { data: 'code', name: 'code', orderable: true },
                { data: 'name', name: 'name', orderable: true },
                { data: 'unit_name', name: 'unit_name', orderable: false },
                { data: 'brand_name', name: 'brand_name', orderable: false },
                { data: 'supplier_name', name: 'supplier_name', orderable: false },
                { data: 'variations_count', name: 'variations_count', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 7,
                    render: function (data, type, row) {
                        return '<a href="' + Urls.showProduct.replace('productID', row.id) + '" class="btn btn-sm thm-btn-bg thm-btn-text-color" data-toggle="tooltip" title="View Details"><i class="fa-solid fa-eye"></i></a> ' +
                               '<a href="' + Urls.editProduct.replace('productID', row.id) + '" class="btn btn-sm thm-btn-bg thm-btn-text-color" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a> ' +
                               '<button data-productid="' + row.id + '" class="btn btn-sm thm-btn-bg thm-btn-text-color delete-product" data-toggle="tooltip" title="Delete"><i class="fa-solid fa-trash"></i></button>';
                    }
                }
            ]
        };
    }

    var populateCreateForm = function (){
        // Populate units
        let unitSelect = $("#productUnit");
        unitSelect.html('<option value="">Select Unit</option>');
        if (typeof productData !== 'undefined' && productData.units) {
            productData.units.forEach(function(unit) {
                unitSelect.append('<option value="' + unit.id + '">' + unit.name + '</option>');
            });
        }

        // Populate brands
        let brandSelect = $("#productBrand");
        brandSelect.html('<option value="">Select Brand</option>');
        if (typeof productData !== 'undefined' && productData.brands) {
            productData.brands.forEach(function(brand) {
                brandSelect.append('<option value="' + brand.id + '">' + brand.name + '</option>');
            });
        }

        // Populate suppliers
        let supplierSelect = $("#productSupplier");
        supplierSelect.html('<option value="">Select Supplier</option>');
        if (typeof productData !== 'undefined' && productData.suppliers) {
            productData.suppliers.forEach(function(supplier) {
                supplierSelect.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
            });
        }

        // Populate categories
        let categorySelect = $("#productCategory");
        categorySelect.html('<option value="">Select category</option>');
        if (typeof productData !== 'undefined' && productData.categories) {
            productData.categories.forEach(function(category) {
                categorySelect.append('<option value="' + category.id + '">' + category.name + '</option>');
            });
        }
    }

    var saveProduct = function (){
        let formData = WinPos.Common.getFormData("#productCreateForm");
        
        WinPos.Common.postAjaxCall(Urls.saveProduct, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("productCreateModal");
                if(response.redirect){
                    window.location.href = response.redirect;
                } else {
                    WinPos.Datatable.refresh();
                }
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateProduct = function (productId){
        let formData = WinPos.Common.getFormData("#productEditForm");
        
        WinPos.Common.putAjaxCallPost(Urls.updateProduct.replace("productID", productId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                WinPos.Datatable.refresh();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteProduct = function (productId){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteProduct.replace('productID', productId), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var loadProductDetails = function (productId){
        WinPos.Common.getAjaxCall(Urls.showProduct.replace('productID', productId), function (response){
            if(response.status === 'success'){
                // This will be handled by the show page view
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var saveVariation = function (){
        let formData = WinPos.Common.getFormData("#addVariationForm");
        
        WinPos.Common.postAjaxCall(Urls.storeVariation, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("addVariationModal");
                // Reload page to show new variation
                location.reload();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateVariationFromTable = function (variationId){
        let row = $('tr[data-variation-id="' + variationId + '"]');
        let formData = {
            tagline: row.find('.variation-tagline').val(),
            description: row.find('.variation-description').val(),
            cost_price: row.find('.variation-cost-price').val(),
            selling_price: row.find('.variation-selling-price').val(),
            stock: row.find('.variation-stock').val(),
            status: row.find('.variation-status').val()
        };
        
        WinPos.Common.putAjaxCallPost(Urls.updateVariation.replace("variationID", variationId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteVariation = function (variationId){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteVariation.replace('variationID', variationId), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Remove row from table
                $('tr[data-variation-id="' + variationId + '"]').remove();
                // If no variations left, show message
                if($("#variationsTable tbody tr").length === 0){
                    $("#variationsTable tbody").html('<tr id="noVariationsRow"><td colspan="7" class="text-center">No variations found. Click "Add New Variation" to create one.</td></tr>');
                }
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    return {
        datatableConfiguration: datatableConfiguration,
        populateCreateForm: populateCreateForm,
        saveProduct: saveProduct,
        updateProduct: updateProduct,
        deleteProduct: deleteProduct,
        loadProductDetails: loadProductDetails,
        saveVariation: saveVariation,
        updateVariationFromTable: updateVariationFromTable,
        deleteVariation: deleteVariation
    }
})(productUrls);

// Global event handlers
$(document).on('click', '#saveProduct', function() {
    WinPos.Product.saveProduct();
});

$(document).on('click', '.delete-product', function() {
    WinPos.Datatable.selectRow(this);
    if (confirm("Are you sure you want to delete this product?\nClick OK to continue or Cancel.")) {
        let productId = $(this).data('productid');
        WinPos.Product.deleteProduct(productId);
    }
});

