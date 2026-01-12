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

        // Populate categories
        let categorySelect = $("#productCategory");
        categorySelect.html('<option value="">Select category</option>');
        if (typeof productData !== 'undefined' && productData.categories) {
            productData.categories.forEach(function(category) {
                categorySelect.append('<option value="' + category.id + '">' + category.name + '</option>');
            });
        }
    }

    // Function to generate slug from text
    var generateSlug = function(text) {
        if (!text) return '';
        
        // Convert to lowercase
        let slug = text.toLowerCase();
        
        // Replace spaces and special characters with hyphens
        slug = slug.replace(/[^\w\s-]/g, ''); // Remove special characters except word chars, spaces, and hyphens
        slug = slug.replace(/\s+/g, '-'); // Replace spaces with hyphens
        slug = slug.replace(/-+/g, '-'); // Replace multiple hyphens with single hyphen
        slug = slug.replace(/^-+|-+$/g, ''); // Remove leading and trailing hyphens
        
        // Limit to 100 characters
        if (slug.length > 100) {
            slug = slug.substring(0, 100);
            // Remove trailing hyphen if exists
            slug = slug.replace(/-+$/, '');
        }
        
        return slug;
    }

    // Auto-generate slug from product name (create form)
    var initSlugGeneration = function() {
        // For create form
        $(document).on('input', '#productName', function() {
            let name = $(this).val();
            let slugField = $('#productSlug');
            // Only auto-generate if slug field is empty or matches the previous auto-generated slug
            if (!slugField.data('manually-edited')) {
                let generatedSlug = generateSlug(name);
                slugField.val(generatedSlug);
            }
        });

        // Track manual edits to slug field (create form)
        $(document).on('input', '#productSlug', function() {
            $(this).data('manually-edited', true);
        });

        // Reset manual edit flag when modal is opened (create form)
        $(document).on('shown.bs.modal', '#productCreateModal', function() {
            $('#productSlug').data('manually-edited', false);
            $('#productName').val('');
            $('#productSlug').val('');
        });

        // For edit form
        $(document).on('input', '#editProductName', function() {
            let name = $(this).val();
            let slugField = $('#editProductSlug');
            // Only auto-generate if slug field is empty or matches the previous auto-generated slug
            if (!slugField.data('manually-edited')) {
                let generatedSlug = generateSlug(name);
                slugField.val(generatedSlug);
            }
        });

        // Track manual edits to slug field (edit form)
        $(document).on('input', '#editProductSlug', function() {
            $(this).data('manually-edited', true);
        });

        // Initialize manual edit flag for edit form based on current value
        if ($('#editProductSlug').length && $('#editProductSlug').val()) {
            $('#editProductSlug').data('manually-edited', true);
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
        
        // Validate discount if type is selected
        let discountType = $('#variationDiscountType').val();
        let discountValue = $('#variationDiscountValue').val();
        
        if(discountType && (!discountValue || parseFloat(discountValue) <= 0)){
            toastr.error('Please enter a valid discount value.');
            return;
        }
        
        if(discountType === 'percentage' && parseFloat(discountValue) > 100){
            toastr.error('Percentage discount cannot exceed 100%.');
            return;
        }
        
        // If no discount type, clear discount value
        if(!discountType){
            formData.discount_type = null;
            formData.discount_value = null;
        } else {
            formData.discount_type = discountType;
            formData.discount_value = discountValue ? parseFloat(discountValue) : null;
        }
        
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
    
    var updateAddDiscountFields = function(){
        let discountType = $('#variationDiscountType').val();
        let discountValueInput = $('#variationDiscountValue');
        let helpText = $('#addDiscountValueHelp');
        
        if(discountType === ''){
            discountValueInput.prop('disabled', true);
            discountValueInput.val('');
            helpText.text('Select discount type first');
        } else if(discountType === 'percentage'){
            discountValueInput.prop('disabled', false);
            discountValueInput.attr('max', '100');
            discountValueInput.attr('step', '0.01');
            helpText.text('Enter percentage (max 100%)');
        } else if(discountType === 'fixed'){
            discountValueInput.prop('disabled', false);
            discountValueInput.removeAttr('max'); // No max for fixed when creating (selling price is 0 initially)
            discountValueInput.attr('step', '0.01');
            helpText.text('Enter fixed amount in tk');
        }
    }

    var openEditVariationModal = function (variationId){
        // Get variation data from the table row
        let row = $('tr[data-variation-id="' + variationId + '"]');
        let status = row.data('status') || 'active';
        
        // Check if variation is closed
        if(status === 'closed'){
            toastr.error('Cannot edit closed variation.');
            return;
        }
        
        // Get full description from data attribute or fetch from backend
        // For now, get from the row - we'll need to store full description in data attribute
        let tagline = row.find('td:eq(0)').text().trim();
        let descriptionText = row.find('td:eq(1)').text().trim();
        // Remove the "..." if it was truncated
        if(descriptionText.endsWith('...')){
            descriptionText = descriptionText.slice(0, -3);
        }
        if(descriptionText === '-') descriptionText = '';
        
        // Get full description from data attribute if available, otherwise use truncated version
        let fullDescription = row.data('full-description') || descriptionText;
        
        // Get discount data from data attributes
        let discountType = row.data('discount-type') || '';
        let discountValue = row.data('discount-value') || '';
        
        // Populate modal with current variation data
        $('#editVariationId').val(variationId);
        $('#editVariationTagline').val(tagline);
        $('#editVariationDescription').val(fullDescription);
        $('#editVariationStatus').val(status);
        $('#editVariationDiscountType').val(discountType);
        $('#editVariationDiscountValue').val(discountValue);
        
        // Update discount value help text and max based on type
        updateDiscountFields();
        
        // Disable status dropdown if variation is closed (shouldn't happen, but safety check)
        if(status === 'closed'){
            $('#editVariationStatus').prop('disabled', true);
        } else {
            $('#editVariationStatus').prop('disabled', false);
        }
        
        // Open modal
        WinPos.Common.showBootstrapModal("editVariationModal");
    }
    
    var updateDiscountFields = function(){
        let discountType = $('#editVariationDiscountType').val();
        let discountValueInput = $('#editVariationDiscountValue');
        let helpText = $('#discountValueHelp');
        let sellingPrice = parseFloat($('tr[data-variation-id="' + $('#editVariationId').val() + '"]').find('.variation-selling-price').val() || 0);
        
        if(discountType === ''){
            discountValueInput.prop('disabled', true);
            discountValueInput.val('');
            helpText.text('Select discount type first');
        } else if(discountType === 'percentage'){
            discountValueInput.prop('disabled', false);
            discountValueInput.attr('max', '100');
            discountValueInput.attr('step', '0.01');
            helpText.text('Enter percentage (max 100%)');
        } else if(discountType === 'fixed'){
            discountValueInput.prop('disabled', false);
            discountValueInput.attr('max', sellingPrice);
            discountValueInput.attr('step', '0.01');
            helpText.text('Enter fixed amount (max ' + sellingPrice.toFixed(2) + 'tk)');
        }
    }

    var updateVariationFromModal = function (){
        let variationId = $('#editVariationId').val();
        let row = $('tr[data-variation-id="' + variationId + '"]');
        
        // Get current selling price and stock from the table (not from modal)
        let sellingPrice = parseFloat(row.find('.variation-selling-price').val() || 0);
        let stock = parseInt(row.find('.variation-stock').val() || 0);
        
        // Get discount data
        let discountType = $('#editVariationDiscountType').val();
        let discountValue = $('#editVariationDiscountValue').val();
        
        // Validate discount
        if(discountType && (!discountValue || parseFloat(discountValue) <= 0)){
            toastr.error('Please enter a valid discount value.');
            return;
        }
        
        if(discountType === 'percentage' && parseFloat(discountValue) > 100){
            toastr.error('Percentage discount cannot exceed 100%.');
            return;
        }
        
        if(discountType === 'fixed' && parseFloat(discountValue) > sellingPrice){
            toastr.error('Fixed discount cannot exceed selling price.');
            return;
        }
        
        // If no discount type, clear discount value
        if(!discountType){
            discountValue = null;
        }
        
        let formData = {
            tagline: $('#editVariationTagline').val(),
            description: $('#editVariationDescription').val(),
            selling_price: sellingPrice,
            stock: stock,
            status: $('#editVariationStatus').val(),
            discount_type: discountType || null,
            discount_value: discountValue ? parseFloat(discountValue) : null
        };
        
        // Validation
        if(!formData.tagline || formData.tagline.trim() === ''){
            toastr.error('Tagline is required.');
            return;
        }
        
        WinPos.Common.putAjaxCallPost(Urls.updateVariation.replace("variationID", variationId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Close modal
                WinPos.Common.hideBootstrapModal("editVariationModal");
                // Reload page to reflect changes
                location.reload();
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
                // Update row class if status changed to inactive or closed
                if(newStatus === 'inactive' || newStatus === 'closed'){
                    row.addClass('table-secondary');
                    // Make fields readonly
                    row.find('.variation-tagline').prop('readonly', true).attr('readonly', 'readonly');
                    row.find('.variation-description').prop('readonly', true).attr('readonly', 'readonly');
                    // Status dropdown should remain enabled (can always change status, except closed)
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
        let deleteBtn = row.find('.delete-variation');
        let canDelete = deleteBtn.data('can-delete') === true || deleteBtn.data('can-delete') === 'true';
        
        // Check if delete is allowed
        if(!canDelete){
            toastr.error('Cannot delete this variation. Only active variations with no stock and no sales can be deleted.');
            return;
        }
        
        // Confirm deletion
        if (!confirm("Are you sure you want to delete this variation?")) {
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
        // Get current variation data from table
        let row = $('tr[data-variation-id="' + variationId + '"]');
        
        // Get data from table cells and data attributes
        let tagline = row.find('td:eq(0)').text().trim();
        let descriptionText = row.find('td:eq(1)').text().trim();
        if(descriptionText.endsWith('...')){
            descriptionText = descriptionText.slice(0, -3);
        }
        if(descriptionText === '-') descriptionText = '';
        let fullDescription = row.data('full-description') || descriptionText;
        let stock = parseInt(row.find('.variation-stock').val() || 0);
        let status = row.data('status') || 'active';
        let discountType = row.data('discount-type') || '';
        let discountValue = row.data('discount-value') || '';
        
        let formData = {
            tagline: tagline,
            description: fullDescription,
            selling_price: newPrice,
            stock: stock,
            status: status,
            discount_type: discountType || null,
            discount_value: discountValue ? parseFloat(discountValue) : null
        };
        
        WinPos.Common.putAjaxCallPost(Urls.updateVariation.replace("variationID", variationId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Update the price input in the table
                row.find('.variation-selling-price').val(newPrice);
                // Close modal
                WinPos.Common.hideBootstrapModal("priceUpdateModal");
                // Reload page to reflect changes
                location.reload();
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

    var productImagesList = [];
    var currentProductId = null;

    var loadProductImagesList = function(callback) {
        if (productImagesList.length > 0 && callback) {
            callback();
            return;
        }
        WinPos.Common.getAjaxCall(Urls.getProductImagesList, function(response) {
            if (response.status === 'success') {
                productImagesList = response.images;
                // Populate datalist
                var datalist = $('#productImagesList');
                datalist.empty();
                productImagesList.forEach(function(image) {
                    var option = $('<option></option>');
                    option.attr('value', image.name);
                    option.attr('data-url', image.url);
                    option.text(image.name);
                    datalist.append(option);
                });
                if (callback) callback();
            }
        });
    };

    var loadProductImages = function(productId) {
        currentProductId = productId;
        WinPos.Common.getAjaxCall(Urls.getProductImages.replace('productID', productId), function(response) {
            if (response.status === 'success') {
                renderProductImagesTable(response.images);
            } else {
                toastr.error(response.message || 'Failed to load product images');
            }
        });
    };

    var renderProductImagesTable = function(images) {
        var tbody = $('#productImagesTable tbody');
        tbody.empty();
        
        if (images && images.length > 0) {
            images.forEach(function(image) {
                var row = '<tr data-image-id="' + image.id + '">' +
                    '<td class="text-center align-middle">' + image.id + '</td>' +
                    '<td class="text-center align-middle">' + image.image_name + '</td>' +
                    '<td class="text-center align-middle">' +
                    (image.image_url ? '<img src="' + image.image_url + '" style="max-width: 100px; max-height: 80px; object-fit: contain;">' : '-') +
                    '</td>' +
                    '<td class="text-center align-middle">' +
                    (image.is_default ? '<span class="badge badge-success"><i class="fa-solid fa-check"></i> Default</span>' : '-') +
                    '</td>' +
                    '<td class="text-center align-middle">' + (image.formattedDate || '-') + '</td>' +
                    '<td class="text-center align-middle">' +
                    '<button type="button" class="btn btn-sm btn-info show-product-image" data-image-id="' + image.id + '" title="Show"><i class="fa-solid fa-eye"></i></button> ' +
                    '<button type="button" class="btn btn-sm btn-success mark-default-product-image ' + (image.is_default ? 'active' : '') + '" data-image-id="' + image.id + '" title="Mark As Default">' +
                    (image.is_default ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-star"></i>') +
                    '</button> ' +
                    '<button type="button" class="btn btn-sm btn-danger delete-product-image" data-image-id="' + image.id + '" title="Delete"><i class="fa-solid fa-trash"></i></button>' +
                    '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        } else {
            tbody.append('<tr><td colspan="6" class="text-center">No images found. Click "Add New Image" to add one.</td></tr>');
        }
    };

    var openAddProductImageModal = function(productId) {
        currentProductId = productId;
        loadProductImagesList(function() {
            $('#addProductImageForm')[0].reset();
            $('#productImagePreview').css('background-image', '').html('<span class="text-muted">Image preview will appear here</span>');
            $('#addProductImageModal').modal('show');
        });
    };

    var previewProductImage = function(imageName) {
        if (!imageName) {
            $('#productImagePreview').css('background-image', '').html('<span class="text-muted">Image preview will appear here</span>');
            return;
        }
        
        var image = productImagesList.find(function(img) {
            return img.name === imageName;
        });
        
        if (image) {
            $('#productImagePreview').css('background-image', 'url(' + image.url + ')').html('');
        } else {
            $('#productImagePreview').css('background-image', '').html('<span class="text-danger">Image not found</span>');
        }
    };

    var saveProductImage = function() {
        if (!currentProductId) {
            toastr.error('Product ID not found');
            return;
        }
        
        var imageName = $('#productImageName').val().trim();
        if (!imageName) {
            toastr.error('Please select an image');
            return;
        }
        
        var data = {
            image_name: imageName
        };
        
        WinPos.Common.postAjaxCall(Urls.storeProductImage.replace('productID', currentProductId), JSON.stringify(data), function(response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                $('#addProductImageModal').modal('hide');
                loadProductImages(currentProductId);
            } else {
                WinPos.Common.showValidationErrors(response.errors || {});
            }
        });
    };

    var showProductImage = function(productId, imageId) {
        WinPos.Common.getAjaxCall(Urls.showProductImage.replace('productID', productId).replace('imageID', imageId), function(response) {
            if (response.status === 'success') {
                var image = response.image;
                
                $('#showProductImageId').text(image.id);
                $('#showProductImageName').text(image.image_name);
                $('#showProductImageSize').text(image.formattedSize || '-');
                $('#showProductImageDefault').text(image.is_default ? 'Yes' : 'No');
                $('#showProductImageCreatedAt').text(image.formattedDate + ' ' + image.formattedTime);
                $('#showProductImageCreatedBy').text(image.createdBy || 'N/A');
                
                if (image.image_url) {
                    $('#showProductImagePreview').css('background-image', 'url(' + image.image_url + ')').html('');
                } else {
                    $('#showProductImagePreview').html('<span class="text-muted">No image available</span>');
                }
                
                $('#showProductImageModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load image details');
            }
        });
    };

    var markProductImageAsDefault = function(productId, imageId) {
        WinPos.Common.postAjaxCall(Urls.markProductImageDefault.replace('productID', productId).replace('imageID', imageId), JSON.stringify({}), function(response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                loadProductImages(productId);
            } else {
                toastr.error(response.message || 'Failed to mark image as default');
            }
        });
    };

    var deleteProductImage = function(productId, imageId) {
        if (confirm("Are you sure you want to delete this image?\nThis will only remove the database record, not the image file.\nClick OK to continue or Cancel.")) {
            WinPos.Common.deleteAjaxCallPost(Urls.deleteProductImage.replace('productID', productId).replace('imageID', imageId), function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    loadProductImages(productId);
                } else {
                    toastr.error(response.message || 'Failed to delete image');
                }
            });
        }
    };

    var togglePublished = function(productId, buttonElement) {
        WinPos.Common.postAjaxCall(Urls.toggleProductPublished.replace('productID', productId), JSON.stringify({}), function(response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                // Update button appearance
                if (buttonElement) {
                    if (response.is_published) {
                        $(buttonElement).removeClass('btn-secondary').addClass('btn-success');
                        $(buttonElement).html('<i class="fa-solid fa-check-circle"></i> Published');
                        $(buttonElement).attr('data-published', '1');
                    } else {
                        $(buttonElement).removeClass('btn-success').addClass('btn-secondary');
                        $(buttonElement).html('<i class="fa-solid fa-times-circle"></i> Unpublished');
                        $(buttonElement).attr('data-published', '0');
                    }
                }
            } else {
                toastr.error(response.message || 'Failed to toggle published status');
            }
        });
    };

    var updateSeo = function(productId) {
        var formData = WinPos.Common.getFormData('#productSeoForm');
        
        WinPos.Common.putAjaxCallPost(Urls.updateProductSeo.replace('productID', productId), JSON.stringify(formData), function(response) {
            if (response.status === 'success') {
                toastr.success(response.message);
            } else {
                WinPos.Common.showValidationErrors(response.errors || {});
            }
        });
    };

    var updateDefaultDiscountFields = function() {
        var discountType = $('#editProductDefaultDiscountType').val();
        var discountValueInput = $('#editProductDefaultDiscount');
        var helpText = $('#defaultDiscountValueHelp');
        var defaultPrice = parseFloat($('#editProductDefaultPrice').val() || 0);
        
        if (discountType === '') {
            discountValueInput.prop('disabled', true);
            discountValueInput.val('');
            helpText.text('Enter discount value based on selected type');
        } else if (discountType === 'percentage') {
            discountValueInput.prop('disabled', false);
            discountValueInput.attr('max', '100');
            discountValueInput.attr('step', '0.01');
            helpText.text('Enter percentage (max 100%)');
            // Clear value if it exceeds 100
            if (parseFloat(discountValueInput.val()) > 100) {
                discountValueInput.val('');
            }
        } else if (discountType === 'fixed') {
            discountValueInput.prop('disabled', false);
            discountValueInput.attr('max', defaultPrice > 0 ? defaultPrice : '');
            discountValueInput.attr('step', '0.01');
            if (defaultPrice > 0) {
                helpText.text('Enter fixed amount (max ' + defaultPrice.toFixed(2) + 'tk)');
                // Clear value if it exceeds price
                if (parseFloat(discountValueInput.val()) > defaultPrice) {
                    discountValueInput.val('');
                }
            } else {
                helpText.text('Enter fixed amount');
            }
        }
    };

    return {
        datatableConfiguration: datatableConfiguration,
        populateCreateForm: populateCreateForm,
        saveProduct: saveProduct,
        updateProduct: updateProduct,
        deleteProduct: deleteProduct,
        initSlugGeneration: initSlugGeneration,
        loadProductDetails: loadProductDetails,
        saveVariation: saveVariation,
        openEditVariationModal: openEditVariationModal,
        updateVariationFromModal: updateVariationFromModal,
        updateDiscountFields: updateDiscountFields,
        updateAddDiscountFields: updateAddDiscountFields,
        updateVariationStatusOnly: updateVariationStatusOnly,
        deleteVariation: deleteVariation,
        openStockUpdateModal: openStockUpdateModal,
        addStockFromPurchaseItem: addStockFromPurchaseItem,
        moveStockToPurchase: moveStockToPurchase,
        openPriceUpdateModal: openPriceUpdateModal,
        updateVariationPrice: updateVariationPrice,
        createFreshVariant: createFreshVariant,
        initProductPurchasesTable: initProductPurchasesTable,
        loadProductImages: loadProductImages,
        openAddProductImageModal: openAddProductImageModal,
        previewProductImage: previewProductImage,
        saveProductImage: saveProductImage,
        showProductImage: showProductImage,
        markProductImageAsDefault: markProductImageAsDefault,
        deleteProductImage: deleteProductImage,
        togglePublished: togglePublished,
        updateSeo: updateSeo,
        updateDefaultDiscountFields: updateDefaultDiscountFields
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

