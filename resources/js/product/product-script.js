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
        
        // Check if variation is inactive
        if(row.hasClass('table-secondary') || row.find('.variation-status').prop('disabled')){
            toastr.error('Cannot update inactive variant.');
            return;
        }
        
        let formData = {
            tagline: row.find('.variation-tagline').val(),
            description: row.find('.variation-description').val(),
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
        let row = $('tr[data-variation-id="' + variationId + '"]');
        
        // Check if variation is inactive
        if(row.hasClass('table-secondary') || row.find('.variation-status').prop('disabled')){
            toastr.error('Cannot delete inactive variant.');
            return;
        }
        
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

    var openStockUpdateModal = function (variationId){
        // Show loading state
        $('#purchaseItemsContainer').html('<p class="text-muted text-center p-3">Loading purchase items...</p>');
        $('#modalVariationTagline').text('Loading...');
        $('#variationNameDisplay').text('-');
        $('#currentSellingPriceDisplay').text('-');
        $('#currentStocksDisplay').text('-');
        $('#alreadySalesQtyDisplay').text('-');
        
        // Open modal
        WinPos.Common.showBootstrapModal("stockUpdateModal");
        
        // Fetch purchase items
        WinPos.Common.getAjaxCall(Urls.getPurchaseItems.replace('variationID', variationId), function (response){
            if(response.status === 'success'){
                $('#modalVariationTagline').text(response.variation.tagline);
                displayVariationInfo(response.variation);
                displayPurchaseItems(response.purchase_items, response.variation);
            }else{
                $('#purchaseItemsContainer').html('<p class="text-danger text-center p-3">' + (response.message || 'Failed to load purchase items.') + '</p>');
            }
        });
    }

    var displayVariationInfo = function (variation){
        $('#variationNameDisplay').text(variation.tagline || '-');
        $('#currentSellingPriceDisplay').text(parseFloat(variation.selling_price || 0).toFixed(2));
        $('#currentStocksDisplay').text(variation.current_stock || 0);
        $('#alreadySalesQtyDisplay').text(variation.sold_items_qty || 0);
    }

    var displayPurchaseItems = function (purchaseItems, variation){
        let container = $('#purchaseItemsContainer');
        
        if(purchaseItems.length === 0){
            container.html('<p class="text-muted text-center p-3">No purchase items available for this variation.</p>');
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-bordered table-hover mb-0">';
        html += '<thead class="thead-light">';
        html += '<tr>';
        html += '<th class="text-center align-middle">Purchases Date</th>';
        html += '<th class="text-center align-middle">Cost Price</th>';
        html += '<th class="text-center align-middle">Available Stocks</th>';
        html += '<th class="text-center align-middle">Action</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        
        purchaseItems.forEach(function(item){
            html += '<tr>';
            html += '<td class="text-center align-middle">' + (item.purchase_date || 'N/A') + '</td>';
            html += '<td class="text-center align-middle">' + parseFloat(item.cost_price || 0).toFixed(2) + '</td>';
            html += '<td class="text-center align-middle">' + (item.available_stock || 0) + '</td>';
            html += '<td class="text-center align-middle">';
            html += '<div class="d-flex align-items-center justify-content-center gap-2">';
            html += '<input type="number" ';
            html += 'class="form-control form-control-sm" ';
            html += 'id="qtyInput_' + item.id + '" ';
            html += 'min="1" ';
            html += 'max="' + (item.available_stock || 0) + '" ';
            html += 'value="1" ';
            html += 'style="width: 80px;">';
            html += '<button type="button" ';
            html += 'class="btn btn-sm btn-success add-stock-btn" ';
            html += 'data-purchase-item-id="' + item.id + '" ';
            html += 'data-variation-id="' + variation.id + '" ';
            html += 'data-cost-price="' + item.cost_price + '" ';
            html += 'data-selling-price="' + variation.selling_price + '">';
            html += '<i class="fa-solid fa-plus"></i> Add';
            html += '</button>';
            html += '</div>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody>';
        html += '</table></div>';
        
        container.html(html);
    }

    var addStockFromPurchaseItem = function (variationId, purchaseItemId, quantity){
        let formData = {
            purchase_item_id: purchaseItemId,
            quantity: quantity
        };
        
        WinPos.Common.postAjaxCall(Urls.addStockFromPurchase.replace('variationID', variationId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Update the stock input in the table
                $('tr[data-variation-id="' + variationId + '"] .variation-stock').val(response.variation.stock);
                // Reload purchase items to update available stock
                openStockUpdateModal(variationId);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var openPriceUpdateModal = function (variationId){
        // Show loading state
        $('#priceUpdateContainer').html('<p class="text-muted text-center">Loading price information...</p>');
        $('#priceModalVariationTagline').text('Loading...');
        // Reset button visibility
        $('#savePriceUpdate').hide();
        $('#createFreshVariantBtn').hide();
        
        // Open modal
        WinPos.Common.showBootstrapModal("priceUpdateModal");
        
        // Fetch price update information
        WinPos.Common.getAjaxCall(Urls.getPriceUpdateInfo.replace('variationID', variationId), function (response){
            if(response.status === 'success'){
                $('#priceModalVariationTagline').text(response.variation.tagline);
                displayPriceUpdateInfo(response);
            }else{
                $('#priceUpdateContainer').html('<p class="text-danger text-center">' + (response.message || 'Failed to load price information.') + '</p>');
            }
        });
    }

    var displayPriceUpdateInfo = function (data){
        let container = $('#priceUpdateContainer');
        let variation = data.variation;
        
        if(data.has_sales){
            // Hide save button, show create fresh variant button
            $('#savePriceUpdate').hide();
            $('#createFreshVariantBtn').show();
            $('#createFreshVariantBtn').data('variation-id', variation.id);
            
            // Show message that price cannot be updated due to existing sales
            let html = '<div class="alert alert-warning">';
            html += '<h6 class="alert-heading"><i class="fa-solid fa-exclamation-triangle"></i> Price Update Restricted</h6>';
            html += '<p class="mb-0">';
            html += 'You cannot update the price of this variant as you already have ' + (data.sold_items_qty || 0) + ' sales recorded for this item. ';
            html += 'To update the price, you need to first create a new variant from this variant and mark this one as stockout. ';
            html += 'This will move all available stock to the new variant while keeping this variant for reporting and historical purposes.';
            html += '</p>';
            html += '</div>';
            container.html(html);
        }else{
            // Show save button, hide create fresh variant button
            $('#savePriceUpdate').show();
            $('#createFreshVariantBtn').hide();
            
            // Show price update form
            let costPrice = data.cost_price ? parseFloat(data.cost_price).toFixed(2) : 'N/A';
            let html = '<div class="card border mb-3">';
            html += '<div class="card-header">';
            html += '<h6 class="mb-0">Price Information</h6>';
            html += '</div>';
            html += '<div class="card-body">';
            html += '<div class="row mb-3">';
            html += '<div class="col-12">';
            html += '<p class="mb-2"><strong>Variation Name:</strong> ' + (variation.tagline || '-') + '</p>';
            html += '<p class="mb-2"><strong>Cost Price:</strong> ' + costPrice + '</p>';
            html += '<p class="mb-2"><strong>Current Selling Price:</strong> ' + parseFloat(variation.selling_price || 0).toFixed(2) + '</p>';
            html += '</div>';
            html += '</div>';
            html += '<hr>';
            html += '<div class="row">';
            html += '<div class="col-12">';
            html += '<form id="priceUpdateForm">';
            html += '<div class="form-group">';
            html += '<label for="newSellingPrice">New Selling Price*</label>';
            html += '<input type="number" step="0.01" min="0" class="form-control rounded" name="selling_price" id="newSellingPrice" value="' + parseFloat(variation.selling_price || 0).toFixed(2) + '" required>';
            html += '<small class="form-text text-muted">Current cost price: ' + costPrice + '</small>';
            html += '</div>';
            html += '<input type="hidden" id="priceUpdateVariationId" value="' + variation.id + '">';
            html += '</form>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            container.html(html);
        }
    }

    var updateVariationPrice = function (variationId, newPrice){
        let formData = {
            selling_price: newPrice
        };
        
        // Get current variation data from table
        let row = $('tr[data-variation-id="' + variationId + '"]');
        formData.tagline = row.find('.variation-tagline').val();
        formData.description = row.find('.variation-description').val();
        formData.stock = row.find('.variation-stock').val();
        formData.status = row.find('.variation-status').val();
        
        WinPos.Common.putAjaxCallPost(Urls.updateVariation.replace("variationID", variationId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Update the price input in the table
                row.find('.variation-selling-price').val(newPrice);
                // Close modal
                WinPos.Common.hideBootstrapModal("priceUpdateModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var createFreshVariant = function (variationId){
        WinPos.Common.postAjaxCall(Urls.createFreshVariant.replace('variationID', variationId), JSON.stringify({}), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Close modal
                WinPos.Common.hideBootstrapModal("priceUpdateModal");
                // Reload page to show new variation and updated inactive variant
                location.reload();
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
        deleteVariation: deleteVariation,
        openStockUpdateModal: openStockUpdateModal,
        addStockFromPurchaseItem: addStockFromPurchaseItem,
        openPriceUpdateModal: openPriceUpdateModal,
        updateVariationPrice: updateVariationPrice,
        createFreshVariant: createFreshVariant
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

// Handle add stock from purchase item
$(document).on('click', '.add-stock-btn', function() {
    let purchaseItemId = $(this).data('purchase-item-id');
    let variationId = $(this).data('variation-id');
    let costPrice = parseFloat($(this).data('cost-price'));
    let sellingPrice = parseFloat($(this).data('selling-price'));
    let quantity = parseInt($('#qtyInput_' + purchaseItemId).val());
    
    if(!quantity || quantity < 1){
        toastr.error('Please enter a valid quantity.');
        return;
    }
    
    let maxQty = parseInt($('#qtyInput_' + purchaseItemId).attr('max'));
    if(quantity > maxQty){
        toastr.error('Quantity cannot exceed available stock: ' + maxQty);
        return;
    }
    
    // Store data for confirmation
    $('#confirmStockUpdate').data('purchase-item-id', purchaseItemId);
    $('#confirmStockUpdate').data('variation-id', variationId);
    $('#confirmStockUpdate').data('quantity', quantity);
    
    // Show confirmation modal
    let confirmationMessage = 'The cost price of this product is ' + costPrice.toFixed(2) + ', ';
    confirmationMessage += 'Are you sure want to sell this product for ' + sellingPrice.toFixed(2) + ' (Current price). ';
    confirmationMessage += 'If not please update the price first.';
    $('#confirmationMessage').text(confirmationMessage);
    
    WinPos.Common.showBootstrapModal("stockUpdateConfirmationModal");
});

// Handle confirmation
$(document).on('click', '#confirmStockUpdate', function() {
    let purchaseItemId = $(this).data('purchase-item-id');
    let variationId = $(this).data('variation-id');
    let quantity = $(this).data('quantity');
    
    // Close confirmation modal
    WinPos.Common.hideBootstrapModal("stockUpdateConfirmationModal");
    
    // Add stock
    WinPos.Product.addStockFromPurchaseItem(variationId, purchaseItemId, quantity);
});

