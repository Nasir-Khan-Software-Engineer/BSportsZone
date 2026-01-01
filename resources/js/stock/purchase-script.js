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
        const row = $('<tr data-variant-id="' + variantId + '"></tr>');
        
        row.append('<td>' + tagline + '</td>');
        row.append('<td class="text-center">' + parseFloat(costPrice).toFixed(2) + '</td>');
        row.append('<td class="text-center">' + purchasedQty + '</td>');
        row.append('<td class="text-center">' + sellableQty + '</td>');
        row.append('<td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-purchase-item" data-variant-id="' + variantId + '"><i class="fa-solid fa-trash"></i></button></td>');
        
        tbody.append(row);
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
        if ($('#purchaseItemsTableBody tr').length > 0 && $('#purchaseItemsTableBody tr:first').find('td').length >= 4) {
            // Edit page - has unallocated qty column
            $('#purchaseItemsTableBody tr').each(function() {
                const variantId = $(this).data('variant-id');
                if (variantId) {
                    const isEditable = $(this).data('editable') === 'true' || $(this).data('editable') === true;
                    let costPrice, purchasedQty, unallocatedQty;
                    
                    if (isEditable) {
                        costPrice = parseFloat($(this).find('.cost-price-input').val()) || 0;
                        purchasedQty = parseInt($(this).find('.purchased-qty-input').val()) || 0;
                        // For editable items, unallocated = purchased (since no allocation has occurred)
                        unallocatedQty = purchasedQty;
                    } else {
                        // For non-editable items, read from displayed text
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
                }
            });
            
            $('#totalQty').text(totalQty);
            $('#totalCost').text(totalCost.toFixed(2));
            if ($('#totalUnallocated').length) {
                $('#totalUnallocated').text(totalUnallocated);
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
                let costPrice, purchasedQty;
                
                if (isEditable) {
                    costPrice = parseFloat($(this).find('.cost-price-input').val()) || 0;
                    purchasedQty = parseInt($(this).find('.purchased-qty-input').val()) || 0;
                } else {
                    // For non-editable items, get from data attributes or existing values
                    costPrice = parseFloat($(this).find('td:eq(1)').text().replace(/,/g, '')) || 0;
                    purchasedQty = parseInt($(this).find('td:eq(2)').text()) || 0;
                }

                if (purchasedQty > 0) {
                    items.push({
                        product_variant_id: variantId,
                        cost_price: costPrice,
                        purchased_qty: purchasedQty
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
                WinPos.Datatable.refresh();
            }else{
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
        updatePurchase: updatePurchase
    }
})(purchaseUrls);

// Global event handlers
$(document).on('click', '.add-variant-btn', function() {
    const variantId = $(this).data('variant-id');
    const tagline = $(this).data('tagline');
    WinPos.Purchase.addVariantToPurchase(variantId, tagline);
});

$(document).on('click', '.remove-purchase-item', function() {
    const variantId = $(this).data('variant-id');
    WinPos.Purchase.removeItemFromPurchase(variantId);
});

