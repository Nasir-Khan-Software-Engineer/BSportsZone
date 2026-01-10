WinPos.Purchase = (function (Urls){

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
                { data: 'formattedDate', name: 'purchase_date', orderable: true },
                { data: 'invoice_number', name: 'invoice_number', orderable: true },
                { data: 'name', name: 'name', orderable: true },
                { data: 'product_name', name: 'product_name', orderable: false },
                { data: 'total_cost_formatted', name: 'total_cost_price', orderable: false },
                { data: 'total_qty', name: 'total_qty', orderable: false },
                { data: 'supplier_name', name: 'supplier_name', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 8,
                    render: function (data, type, row) {
                        return '<a href="' + Urls.showPurchase.replace('purchaseID', row.id) + '" class="btn btn-sm thm-btn-bg thm-btn-text-color" data-toggle="tooltip" title="View Details"><i class="fa-solid fa-eye"></i></a> ' +
                               '<a href="' + Urls.editPurchase.replace('purchaseID', row.id) + '" class="btn btn-sm thm-btn-bg thm-btn-text-color" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>';
                    }
                }
            ]
        };
    }

    var loadProductVariations = function (productId) {
        $.ajax({
            url: Urls.getVariations,
            type: 'GET',
            data: { product_id: productId },
            success: function(response) {
                if(response.status === 'success') {
                    displayVariants(response.variations);
                } else {
                    toastr.error(response.message || 'Failed to load variations.');
                }
            },
            error: function() {
                toastr.error('Failed to load variations.');
            }
        });
    }

    var displayVariants = function (variations) {
        if (!variations || variations.length === 0) {
            $('#variantsContainer').html('<p class="text-danger">No active variants found for this product.</p>');
            $('#purchaseItemsTableContainer').hide();
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
        html += '<thead><tr><th>Tag Line</th><th>Cost Price</th><th>New Stock Qty</th><th>Sellable Stock</th><th>Action</th></tr></thead>';
        html += '<tbody>';

        variations.forEach(function(variant) {
            const isAdded = addedVariants.includes(variant.id);
            html += '<tr data-variant-id="' + variant.id + '">';
            html += '<td>' + variant.tagline + '</td>';
            html += '<td><input type="number" step="0.01" class="form-control form-control-sm rounded variant-cost-price" value="" placeholder="0.00" data-variant-id="' + variant.id + '"></td>';
            html += '<td><input type="number" class="form-control form-control-sm rounded variant-qty" value="0" data-variant-id="' + variant.id + '"></td>';
            html += '<td><input type="number" class="form-control form-control-sm rounded variant-sellable" value="0" readonly></td>';
            html += '<td>';
            if (isAdded) {
                html += '<span class="text-success">Added</span>';
            } else {
                html += '<button type="button" class="btn btn-sm btn-primary add-variant-btn" data-variant-id="' + variant.id + '" data-tagline="' + variant.tagline + '"><i class="fa-solid fa-plus"></i> Add</button>';
            }
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        $('#variantsContainer').html(html);
        $('#purchaseItemsTableContainer').show();

        // Auto-update sellable stock when qty changes
        $(document).off('input', '.variant-qty').on('input', '.variant-qty', function() {
            const qty = $(this).val();
            $(this).closest('tr').find('.variant-sellable').val(qty);
        });
    }

    var addVariantToPurchase = function (variantId, tagline) {
        const row = $('tr[data-variant-id="' + variantId + '"]');
        const costPrice = parseFloat(row.find('.variant-cost-price').val()) || 0;
        const qty = parseInt(row.find('.variant-qty').val()) || 0;

        if (qty <= 0) {
            toastr.error('Quantity must be greater than 0.');
            return;
        }

        if (costPrice < 0) {
            toastr.error('Cost price cannot be negative.');
            return;
        }

        // Add to purchase items
        const item = {
            product_variant_id: variantId,
            cost_price: costPrice,
            purchased_qty: qty
        };

        purchaseItems.push(item);
        addedVariants.push(variantId);

        // Add to table
        addItemToTable(variantId, tagline, costPrice, qty, qty);

        // Update variant row
        row.find('.add-variant-btn').replaceWith('<span class="text-success">Added</span>');

        calculateTotals();
    }

    var addItemToTable = function (variantId, tagline, costPrice, purchasedQty, sellableQty) {
        const tbody = $('#purchaseItemsTableBody');
        
        // Check if we're on edit page (has unallocated qty column and allocated qty column)
        const isEditPage = $('#purchaseItemsTable thead th').length >= 6;
        
        if (isEditPage) {
            // Edit page format - with editable inputs and buttons
            const row = $('<tr data-variant-id="' + variantId + '" data-editable="true"></tr>');
            
            row.append('<td>' + tagline + '</td>');
            row.append('<td><input type="number" step="0.01" class="form-control form-control-sm rounded cost-price-input" value="' + parseFloat(costPrice).toFixed(2) + '" data-variant-id="' + variantId + '"></td>');
            row.append('<td><input type="number" class="form-control form-control-sm rounded purchased-qty-input" value="' + purchasedQty + '" data-variant-id="' + variantId + '"></td>');
            row.append('<td class="text-center unallocated-qty-cell">' + sellableQty + '</td>'); // Unallocated Qty (same as purchased for new items)
            row.append('<td class="text-center allocated-qty-cell">0</td>'); // Allocated Qty (0 for new items)
            row.append('<td class="text-center">' +
                '<button type="button" class="btn btn-sm btn-danger remove-purchase-item" data-variant-id="' + variantId + '" title="Remove Item"><i class="fa-solid fa-trash"></i></button>' +
                '</td>');
            
            tbody.append(row);
        } else {
            // Create page format
            const row = $('<tr data-variant-id="' + variantId + '"></tr>');
            
            row.append('<td>' + tagline + '</td>');
            row.append('<td class="text-center">' + parseFloat(costPrice).toFixed(2) + '</td>');
            row.append('<td class="text-center">' + purchasedQty + '</td>');
            row.append('<td class="text-center">' + sellableQty + '</td>');
            row.append('<td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-purchase-item" data-variant-id="' + variantId + '"><i class="fa-solid fa-trash"></i></button></td>');
            
            tbody.append(row);
        }
    }

    var removeItemFromPurchase = function (variantId) {
        purchaseItems = purchaseItems.filter(item => item.product_variant_id !== variantId);
        addedVariants = addedVariants.filter(id => id !== variantId);
        $('tr[data-variant-id="' + variantId + '"]').remove();
        
        // Update variant row in variants table
        const variantRow = $('#variantsContainer').find('tr[data-variant-id="' + variantId + '"]');
        if (variantRow.length) {
            variantRow.find('td:last').html('<button type="button" class="btn btn-sm btn-primary add-variant-btn" data-variant-id="' + variantId + '" data-tagline="' + variantRow.find('td:first').text() + '"><i class="fa-solid fa-plus"></i> Add</button>');
        }
        
        calculateTotals();
    }

    var calculateTotals = function () {
        let totalQty = 0;
        let totalCost = 0;
        let totalSellable = 0;
        let totalUnallocated = 0;

        // Check if we're on edit page (items in table with unallocated qty column)
        if ($('#purchaseItemsTableBody tr').length > 0 && $('#purchaseItemsTableBody tr:first').find('td').length >= 5) {
            // Edit page - has unallocated qty and allocated qty columns
            $('#purchaseItemsTableBody tr').each(function() {
                const variantId = $(this).data('variant-id');
                const itemId = $(this).data('item-id');
                const isEditable = $(this).data('editable') === 'true' || $(this).data('editable') === true;
                let costPrice, purchasedQty, unallocatedQty;
                
                if (isEditable && $(this).find('.cost-price-input').length) {
                    // Editable item with inputs (existing or new)
                    costPrice = parseFloat($(this).find('.cost-price-input').val()) || 0;
                    purchasedQty = parseInt($(this).find('.purchased-qty-input').val()) || 0;
                    // For editable items, unallocated = purchased (since no allocation has occurred)
                    unallocatedQty = purchasedQty;
                    
                    // Update unallocated and allocated qty cells for new items
                    if (!itemId) {
                        $(this).find('.unallocated-qty-cell').text(purchasedQty);
                        $(this).find('.allocated-qty-cell').text('0');
                    }
                } else {
                    // Non-editable item (has allocated qty) - read from displayed text
                    const costPriceText = $(this).find('td:eq(1)').text().trim();
                    const purchasedQtyText = $(this).find('td:eq(2)').text().trim();
                    const unallocatedQtyText = $(this).find('td:eq(3)').text().trim();
                    
                    costPrice = parseFloat(costPriceText.replace(/,/g, '')) || 0;
                    purchasedQty = parseInt(purchasedQtyText) || 0;
                    unallocatedQty = parseInt(unallocatedQtyText) || 0;
                }
                
                totalQty += purchasedQty;
                totalCost += costPrice * purchasedQty;
                totalUnallocated += unallocatedQty;
            });
            
            $('#totalQty').text(totalQty);
            $('#totalCost').text(totalCost.toFixed(2));
            if ($('#totalUnallocated').length) {
                $('#totalUnallocated').text(totalUnallocated);
            }
            if ($('#totalAllocated').length) {
                // Calculate total allocated (purchased - unallocated)
                let totalAllocated = totalQty - totalUnallocated;
                $('#totalAllocated').text(totalAllocated);
            }
        } else {
            // Create page - use purchaseItems array
            purchaseItems.forEach(function(item) {
                totalQty += item.purchased_qty;
                totalCost += item.cost_price * item.purchased_qty;
                totalSellable += item.purchased_qty; // Sellable = purchased qty on create page
            });
            
            $('#totalQty').text(totalQty);
            $('#totalCost').text(totalCost.toFixed(2));
            if ($('#totalSellable').length) {
                $('#totalSellable').text(totalSellable);
            }
        }
    }

    var savePurchase = function () {
        if (purchaseItems.length === 0) {
            toastr.error('Please add at least one purchase item.');
            return;
        }

        let formData = WinPos.Common.getFormData("#purchaseCreateForm");
        formData.purchase_items = purchaseItems;

        WinPos.Common.postAjaxCall(Urls.savePurchase, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                if(response.redirect){
                    window.location.href = response.redirect;
                } else {
                    window.location.href = purchaseUrls.showPurchase.replace('purchaseID', response.purchase.id);
                }
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updatePurchase = function () {
        // Collect purchase items from table
        const items = [];
        $('#purchaseItemsTableBody tr').each(function() {
            const variantId = $(this).data('variant-id');
            const itemId = $(this).data('item-id');
            const isEditable = $(this).data('editable') === 'true' || $(this).data('editable') === true;
            
            if (variantId) {
                let costPrice, purchasedQty, status;
                
                if (isEditable) {
                    costPrice = parseFloat($(this).find('.cost-price-input').val()) || 0;
                    purchasedQty = parseInt($(this).find('.purchased-qty-input').val()) || 0;
                    status = $(this).find('.purchase-item-status').val() || 'reserved';
                } else {
                    // For non-editable items, get from data attributes or existing values
                    costPrice = parseFloat($(this).find('td:eq(2)').text().replace(/,/g, '')) || 0;
                    purchasedQty = parseInt($(this).find('td:eq(3)').text()) || 0;
                    status = $(this).find('.purchase-item-status').val() || 'reserved';
                }

                if (purchasedQty > 0) {
                    items.push({
                        product_variant_id: variantId,
                        cost_price: costPrice,
                        purchased_qty: purchasedQty,
                        status: status
                    });
                }
            }
        });

        if (items.length === 0) {
            toastr.error('Please add at least one purchase item.');
            return;
        }

        let formData = WinPos.Common.getFormData("#purchaseEditForm");
        formData.purchase_items = items;

        WinPos.Common.putAjaxCallPost(Urls.updatePurchase, JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Reload page to reflect changes
                location.reload();
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updatePurchaseItem = function (itemId) {
        let row = $('tr[data-item-id="' + itemId + '"]');
        let isEditable = row.data('editable') === 'true' || row.data('editable') === true;
        
        // Check if item is editable (no allocated qty)
        if (!isEditable) {
            toastr.error('Cannot update purchase item. This item has allocated quantity.');
            return;
        }
        
        let costPrice = parseFloat(row.find('.cost-price-input').val()) || 0;
        let purchasedQty = parseInt(row.find('.purchased-qty-input').val()) || 0;
        let status = row.find('.purchase-item-status').val() || 'reserved';
        
        if (costPrice <= 0 || purchasedQty <= 0) {
            toastr.error('Please enter valid cost price and purchased quantity.');
            return;
        }
        
        let formData = {
            cost_price: costPrice,
            purchased_qty: purchasedQty,
            status: status
        };
        
        WinPos.Common.putAjaxCallPost(Urls.updatePurchaseItem.replace('ITEM_ID', itemId), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Reload page to reflect changes (including allocated qty)
                location.reload();
            }else{
                if(response.message){
                    toastr.error(response.message);
                }
                if(response.errors){
                    WinPos.Common.showValidationErrors(response.errors);
                }
            }
        }, function(xhr){
            var response = xhr.responseJSON || {};
            if(response.message){
                toastr.error(response.message);
            } else {
                toastr.error('An error occurred while updating the purchase item.');
            }
            if(response.errors && !response.message){
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var removePurchaseItem = function (itemId) {
        let row = $('tr[data-item-id="' + itemId + '"]');
        let isEditable = row.data('editable') === 'true' || row.data('editable') === true;
        
        // Check if item is editable (no allocated qty)
        if (!isEditable) {
            toastr.error('Cannot remove purchase item. This item has allocated quantity.');
            return;
        }
        
        if (!confirm('Are you sure you want to remove this purchase item?')) {
            return;
        }
        
        WinPos.Common.deleteAjaxCallPost(Urls.removePurchaseItem.replace('ITEM_ID', itemId), function (response){
            if(response.status === 'success'){
                toastr.success(response.message);
                // Reload page to reflect changes
                location.reload();
            }else{
                if(response.message){
                    toastr.error(response.message);
                }
                if(response.errors){
                    WinPos.Common.showValidationErrors(response.errors);
                }
            }
        }, function(xhr){
            var response = xhr.responseJSON || {};
            if(response.message){
                toastr.error(response.message);
            } else {
                toastr.error('An error occurred while removing the purchase item.');
            }
            if(response.errors && !response.message){
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    return {
        datatableConfiguration: datatableConfiguration,
        loadProductVariations: loadProductVariations,
        addVariantToPurchase: addVariantToPurchase,
        removeItemFromPurchase: removeItemFromPurchase,
        calculateTotals: calculateTotals,
        savePurchase: savePurchase,
        updatePurchase: updatePurchase,
        updatePurchaseItem: updatePurchaseItem,
        removePurchaseItem: removePurchaseItem
    }
})(purchaseUrls);

// Global event handlers
$(document).on('click', '.add-variant-btn', function() {
    const variantId = $(this).data('variant-id');
    const tagline = $(this).data('tagline');
    WinPos.Purchase.addVariantToPurchase(variantId, tagline);
});

// Handle purchase item status change
$(document).on('change', '.purchase-item-status', function() {
    const itemId = $(this).data('item-id');
    const row = $('tr[data-item-id="' + itemId + '"]');
    const isEditable = row.data('editable') === 'true' || row.data('editable') === true;
    
    if (!isEditable) {
        toastr.error('Cannot update status. This item has allocated quantity.');
        // Revert the change
        const originalStatus = $(this).data('original-status') || 'reserved';
        $(this).val(originalStatus);
        return;
    }
    
    // Update the purchase item with new status
    WinPos.Purchase.updatePurchaseItem(itemId);
});


