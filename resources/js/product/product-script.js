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
                { data: 'salable_stocks', name: 'salable_stocks', orderable: false },
                { data: 'warehouse_stocks', name: 'warehouse_stocks', orderable: false },
                { data: 'cost_price_range', name: 'cost_price_range', orderable: false },
                { data: 'selling_price_range', name: 'selling_price_range', orderable: false },
                { data: 'variations_count', name: 'variations_count', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: [3, 4],
                    className: 'text-center',
                    render: function (data, type, row) {
                        return data !== null && data !== undefined ? data : '0';
                    }
                },
                {
                    targets: [5, 6],
                    className: 'text-center',
                    render: function (data, type, row) {
                        return data || '-';
                    }
                },
                {
                    targets: 8,
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
        
        // Check if variation is inactive (only check table-secondary class)
        if(row.hasClass('table-secondary')){
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
                // Make inputs readonly again
                row.find('.variation-tagline').prop('readonly', true).attr('readonly', 'readonly');
                row.find('.variation-description').prop('readonly', true).attr('readonly', 'readonly');
                // Remove editing class
                row.find('.variation-tagline, .variation-description').removeClass('editing');
                // Status dropdown is always enabled (can always change status)
                // No need to disable it
                // Show edit button, hide save button
                row.find('.edit-variation').show();
                row.find('.save-variation').hide();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        }, function(xhr){
            // Error callback - handle 422 and other error responses
            var response = xhr.responseJSON || {};
            
            // Show error message - prioritize message over errors to avoid duplicates
            if(response.message){
                toastr.error(response.message);
            } else if(xhr.status === 422){
                toastr.error('Validation failed. Please check the errors.');
            } else {
                toastr.error('An error occurred while updating the variation.');
            }
            
            // Only show validation errors if there's no message to avoid duplicates
            if(response.errors && !response.message){
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateVariationStatusOnly = function (variationId, newStatus){
        let row = $('tr[data-variation-id="' + variationId + '"]');
        
        // Get current values from the row
        let formData = {
            tagline: row.find('.variation-tagline').val(),
            description: row.find('.variation-description').val(),
            selling_price: row.find('.variation-selling-price').val(),
            stock: row.find('.variation-stock').val(),
            status: newStatus
        };
        
        WinPos.Common.putAjaxCallPost(Urls.updateVariation.replace("variationID", variationId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Update the original status data attribute
                row.find('.variation-status').data('original-status', newStatus);
                // Update row class if status changed to inactive
                if(newStatus === 'inactive'){
                    row.addClass('table-secondary');
                    // Make fields readonly
                    row.find('.variation-tagline').prop('readonly', true).attr('readonly', 'readonly');
                    row.find('.variation-description').prop('readonly', true).attr('readonly', 'readonly');
                    // Status dropdown should remain enabled (can always change status)
                } else {
                    row.removeClass('table-secondary');
                    // Status dropdown should remain enabled
                }
                // Reload page to reflect all changes
                location.reload();
            }else{
                // Revert status dropdown on error
                let originalStatus = row.find('.variation-status').data('original-status');
                row.find('.variation-status').val(originalStatus);
                WinPos.Common.showValidationErrors(response.errors);
            }
        }, function(xhr){
            // Error callback - handle 422 and other error responses
            var response = xhr.responseJSON || {};
            // Revert status dropdown on error
            let originalStatus = row.find('.variation-status').data('original-status');
            row.find('.variation-status').val(originalStatus);
            
            // Show error message - prioritize message over errors to avoid duplicates
            if(response.message){
                toastr.error(response.message);
            } else if(xhr.status === 422){
                toastr.error('Validation failed. Please check the errors.');
            } else {
                toastr.error('An error occurred while updating the status.');
            }
            
            // Only show validation errors if there's no message to avoid duplicates
            if(response.errors && !response.message){
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteVariation = function (variationId){
        let row = $('tr[data-variation-id="' + variationId + '"]');
        
        // Check if variation is inactive (only check table-secondary class, not disabled status)
        if(row.hasClass('table-secondary')){
            toastr.error('Cannot delete inactive variant.');
            return;
        }
        
        WinPos.Common.deleteAjaxCallPost(
            Urls.deleteVariation.replace('variationID', variationId), 
            function (response){
                // Success callback
                if(response.status === 'success'){
                    toastr.success(response.message);
                    // Remove row from table
                    $('tr[data-variation-id="' + variationId + '"]').remove();
                    // If no variations left, show message
                    if($("#variationsTable tbody tr").length === 0){
                        $("#variationsTable tbody").html('<tr id="noVariationsRow"><td colspan="7" class="text-center">No variations found. Click "Add New Variation" to create one.</td></tr>');
                    }
                }else{
                    // Show error message
                    if(response.message){
                        toastr.error(response.message);
                    }
                    // Show validation errors if any
                    if(response.errors){
                        WinPos.Common.showValidationErrors(response.errors);
                    }
                }
            },
            function (xhr){
                // Error callback - handle 422 and other error responses
                var response = xhr.responseJSON || {};
                
                // Show error message - prioritize message over errors to avoid duplicates
                if(response.message){
                    toastr.error(response.message);
                } else if(xhr.status === 422){
                    toastr.error('Cannot delete this variation. Please check the requirements.');
                } else if(xhr.status === 404){
                    toastr.error('Variation not found.');
                } else if(xhr.status === 403){
                    toastr.error('Unauthorized access.');
                } else {
                    toastr.error('An error occurred while deleting the variation.');
                }
                
                // Only show validation errors if there's no message to avoid duplicates
                if(response.errors && !response.message){
                    WinPos.Common.showValidationErrors(response.errors);
                }
            }
        );
    }

    var openStockUpdateModal = function (variationId, actionType){
        // Show loading state
        $('#purchaseItemsContainer').html('<p class="text-muted text-center p-3">Loading purchase items...</p>');
        $('#modalVariationTagline').text('Loading...');
        $('#variationNameDisplay').text('-');
        $('#currentSellingPriceDisplay').text('-');
        $('#currentStocksDisplay').text('-');
        $('#alreadySalesQtyDisplay').text('-');
        
        // Store action type for later use
        $('#stockUpdateModal').data('action-type', actionType || 'add');
        
        // Open modal
        WinPos.Common.showBootstrapModal("stockUpdateModal");
        
        // Fetch purchase items
        WinPos.Common.getAjaxCall(Urls.getPurchaseItems.replace('variationID', variationId), function (response){
            if(response.status === 'success'){
                $('#modalVariationTagline').text(response.variation.tagline);
                displayVariationInfo(response.variation);
                displayPurchaseItems(response.purchase_items, response.variation, actionType || 'add');
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

    var displayPurchaseItems = function (purchaseItems, variation, actionType){
        let container = $('#purchaseItemsContainer');
        
        // Safety check: ensure purchaseItems is an array
        if(!purchaseItems || !Array.isArray(purchaseItems)){
            container.html('<p class="text-danger text-center p-3">Invalid purchase items data received.</p>');
            return;
        }
        
        if(purchaseItems.length === 0){
            container.html('<p class="text-muted text-center p-3">No purchase items available for this variation.</p>');
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-bordered table-hover mb-0">';
        html += '<thead class="thead-light">';
        html += '<tr>';
        html += '<th class="text-center align-middle">Purchase Date</th>';
        html += '<th class="text-center align-middle">Invoice Number</th>';
        html += '<th class="text-center align-middle">Status</th>';
        html += '<th class="text-center align-middle">Cost Price</th>';
        html += '<th class="text-center align-middle">Available Stocks</th>';
        html += '<th class="text-center align-middle">Action Type</th>';
        html += '<th class="text-center align-middle">Action</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        
        purchaseItems.forEach(function(item){
            let unallocatedQty = item.unallocated_qty || 0;
            let allocatedQty = item.allocated_qty || 0;
            let defaultAction = actionType === 'move' ? 'move' : 'add';
            
            html += '<tr>';
            html += '<td class="text-center align-middle">' + (item.purchase_date || 'N/A') + '</td>';
            html += '<td class="text-center align-middle">' + (item.invoice_number || 'N/A') + '</td>';
            html += '<td class="text-center align-middle">';
            html += '<span class="badge badge-success">';
            html += (item.status || 'N/A');
            html += '</span>';
            html += '</td>';
            html += '<td class="text-center align-middle">' + parseFloat(item.cost_price || 0).toFixed(2) + '</td>';
            html += '<td class="text-center align-middle">' + (item.available_stock || 0) + '</td>';
            html += '<td class="text-center align-middle">';
            html += '<select class="form-control form-control-sm action-type-select" ';
            html += 'id="actionType_' + item.id + '" ';
            html += 'data-purchase-item-id="' + item.id + '">';
            html += '<option value="add" ' + (defaultAction === 'add' ? 'selected' : '') + '>Add to Product</option>';
            html += '<option value="move" ' + (defaultAction === 'move' ? 'selected' : '') + '>Back to Purchase</option>';
            html += '</select>';
            html += '</td>';
            html += '<td class="text-center align-middle">';
            html += '<div class="d-flex align-items-center justify-content-center gap-2">';
            html += '<input type="number" ';
            html += 'class="form-control form-control-sm qty-input" ';
            html += 'id="qtyInput_' + item.id + '" ';
            html += 'data-purchase-item-id="' + item.id + '" ';
            html += 'data-unallocated-qty="' + unallocatedQty + '" ';
            html += 'data-allocated-qty="' + allocatedQty + '" ';
            html += 'min="1" ';
            html += 'max="' + (defaultAction === 'add' ? unallocatedQty : allocatedQty) + '" ';
            html += 'value="1" ';
            html += 'style="width: 80px;">';
            html += '<button type="button" ';
            html += 'class="btn btn-sm btn-success save-stock-btn" ';
            html += 'data-purchase-item-id="' + item.id + '" ';
            html += 'data-variation-id="' + variation.id + '" ';
            html += 'data-cost-price="' + item.cost_price + '" ';
            html += 'data-selling-price="' + variation.selling_price + '">';
            html += '<i class="fa-solid fa-save"></i> Save';
            html += '</button>';
            html += '</div>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody>';
        html += '</table></div>';
        
        container.html(html);
        
        // Add event listener for action type change
        $(document).off('change', '.action-type-select').on('change', '.action-type-select', function() {
            let purchaseItemId = $(this).data('purchase-item-id');
            let actionType = $(this).val();
            let qtyInput = $('#qtyInput_' + purchaseItemId);
            let unallocatedQty = parseInt(qtyInput.data('unallocated-qty')) || 0;
            let allocatedQty = parseInt(qtyInput.data('allocated-qty')) || 0;
            let currentQty = parseInt(qtyInput.val()) || 1;
            
            // Update max value based on action type
            if (actionType === 'add') {
                qtyInput.attr('max', unallocatedQty);
                if (currentQty > unallocatedQty || unallocatedQty === 0) {
                    qtyInput.val(unallocatedQty > 0 ? unallocatedQty : 1);
                }
            } else {
                qtyInput.attr('max', allocatedQty);
                if (currentQty > allocatedQty || allocatedQty === 0) {
                    qtyInput.val(allocatedQty > 0 ? allocatedQty : 1);
                }
            }
        });
        
        // Add event listener for quantity input validation
        $(document).off('input change', '.qty-input').on('input change', '.qty-input', function() {
            let maxQty = parseInt($(this).attr('max')) || 1;
            let currentQty = parseInt($(this).val()) || 1;
            
            if (currentQty > maxQty) {
                $(this).val(maxQty);
                toastr.warning('Quantity cannot exceed maximum allowed: ' + maxQty);
            } else if (currentQty < 1) {
                $(this).val(1);
            }
        });
    }

    var addStockFromPurchaseItem = function (variationId, purchaseItemId, quantity){
        let formData = {
            current_purchase_item_id: purchaseItemId,
            quantity: quantity
        };
        
        WinPos.Common.postAjaxCall(Urls.addStockFromPurchase.replace('variationID', variationId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Update the stock input in the table
                $('tr[data-variation-id="' + variationId + '"] .variation-stock').val(response.variation.stock);
                // Reload purchase items to update available stock
                let actionType = $('#stockUpdateModal').data('action-type') || 'add';
                openStockUpdateModal(variationId, actionType);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var moveStockToPurchase = function (variationId, purchaseItemId, quantity){
        let formData = {
            current_purchase_item_id: purchaseItemId,
            quantity: quantity
        };
        
        WinPos.Common.postAjaxCall(Urls.moveStockToPurchase.replace('variationID', variationId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Update the stock input in the table
                $('tr[data-variation-id="' + variationId + '"] .variation-stock').val(response.variation.stock);
                // Reload purchase items to update available stock
                let actionType = $('#stockUpdateModal').data('action-type') || 'move';
                openStockUpdateModal(variationId, actionType);
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

    var productPurchasesTable = null;

    var initProductPurchasesTable = function () {
        // Fetch all purchases for the product
        WinPos.Common.getAjaxCall(Urls.getProductPurchases, function (response) {
            if (response.status === 'success') {
                // Initialize DataTable with client-side pagination
                productPurchasesTable = $('#productPurchasesTable').DataTable({
                    data: response.purchases,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    order: [[0, 'desc']],
                    serverSide: false, // Client-side pagination
                    processing: false,
                    columns: [
                        { data: 'id', name: 'id', orderable: true },
                        { data: 'purchase_date', name: 'purchase_date', orderable: true },
                        { 
                            data: 'invoice_number', 
                            name: 'invoice_number', 
                            orderable: true,
                            render: function(data, type, row) {
                                if (data && data !== 'N/A') {
                                    return '<a href="' + Urls.showPurchase.replace('purchaseID', row.id) + '" class="text-primary">' + data + '</a>';
                                }
                                return data || 'N/A';
                            }
                        },
                        { data: 'name', name: 'name', orderable: true },
                        { data: 'total_cost_price', name: 'total_cost_price', orderable: false },
                        { data: 'total_qty', name: 'total_qty', orderable: false },
                        { data: 'total_variations', name: 'total_variations', orderable: false },
                        { data: 'supplier_name', name: 'supplier_name', orderable: false },
                        { data: 'status', name: 'status', orderable: false }
                    ],
                    columnDefs: [
                        {
                            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                            className: 'text-center align-middle'
                        }
                    ]
                });

                // Add search functionality
                $('#searchProductPurchases').on('keyup search input paste cut', function() {
                    if (productPurchasesTable) {
                        productPurchasesTable.search($(this).val()).draw();
                    }
                });
            } else {
                $('#productPurchasesTable tbody').html('<tr><td colspan="9" class="text-center">No purchases found for this product.</td></tr>');
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
        updateVariationStatusOnly: updateVariationStatusOnly,
        deleteVariation: deleteVariation,
        openStockUpdateModal: openStockUpdateModal,
        addStockFromPurchaseItem: addStockFromPurchaseItem,
        moveStockToPurchase: moveStockToPurchase,
        openPriceUpdateModal: openPriceUpdateModal,
        updateVariationPrice: updateVariationPrice,
        createFreshVariant: createFreshVariant,
        initProductPurchasesTable: initProductPurchasesTable
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

// Handle save stock from purchase item
$(document).on('click', '.save-stock-btn', function() {
    let purchaseItemId = $(this).data('purchase-item-id');
    let variationId = $(this).data('variation-id');
    let costPrice = parseFloat($(this).data('cost-price'));
    let sellingPrice = parseFloat($(this).data('selling-price'));
    let quantity = parseInt($('#qtyInput_' + purchaseItemId).val());
    let actionType = $('#actionType_' + purchaseItemId).val();
    
    if(!quantity || quantity < 1){
        toastr.error('Please enter a valid quantity.');
        return;
    }
    
    let maxQty = parseInt($('#qtyInput_' + purchaseItemId).attr('max'));
    if(quantity > maxQty){
        toastr.error('Quantity cannot exceed maximum allowed: ' + maxQty);
        return;
    }
    
    // Store data for confirmation
    $('#confirmStockUpdate').data('purchase-item-id', purchaseItemId);
    $('#confirmStockUpdate').data('variation-id', variationId);
    $('#confirmStockUpdate').data('quantity', quantity);
    $('#confirmStockUpdate').data('action-type', actionType);
    $('#confirmStockUpdate').data('cost-price', costPrice);
    $('#confirmStockUpdate').data('selling-price', sellingPrice);
    
    // Show confirmation modal with appropriate message
    let confirmationMessage = '';
    if (actionType === 'add') {
        confirmationMessage = 'The cost price of this product is ' + costPrice.toFixed(2) + ', ';
        confirmationMessage += 'Are you sure want to sell this product for ' + sellingPrice.toFixed(2) + ' (Current price). ';
        confirmationMessage += 'If not please update the price first.';
    } else {
        confirmationMessage = 'The unit cost price of this purchase is: ' + costPrice.toFixed(2) + '. ';
        confirmationMessage += 'Are you sure want to move ' + quantity + ' item(s) to this purchase? ';
        confirmationMessage += 'You are currently selling this product at price: ' + sellingPrice.toFixed(2) + '.';
    }
    $('#confirmationMessage').text(confirmationMessage);
    
    WinPos.Common.showBootstrapModal("stockUpdateConfirmationModal");
});

// Handle confirmation
$(document).on('click', '#confirmStockUpdate', function() {
    let purchaseItemId = $(this).data('purchase-item-id');
    let variationId = $(this).data('variation-id');
    let quantity = $(this).data('quantity');
    let actionType = $(this).data('action-type');
    
    // Close confirmation modal
    WinPos.Common.hideBootstrapModal("stockUpdateConfirmationModal");
    
    // Perform action based on type
    if (actionType === 'add') {
        WinPos.Product.addStockFromPurchaseItem(variationId, purchaseItemId, quantity);
    } else {
        WinPos.Product.moveStockToPurchase(variationId, purchaseItemId, quantity);
    }
});

// Handle modal close - reload page
$(document).on('hidden.bs.modal', '#stockUpdateModal', function() {
    location.reload();
});

