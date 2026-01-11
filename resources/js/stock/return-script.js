WinPos.Return = (function (Urls){
    
    var isSettingCustomer = false; // Flag to prevent search when setting customer

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
                { data: 'formattedDate', name: 'created_at', orderable: true },
                { data: 'customer_phone', name: 'customer_phone', orderable: false },
                { data: 'sale_invoice', name: 'sale_invoice', orderable: false },
                { data: 'status', name: 'status', orderable: true },
                { data: 'total_amount_formatted', name: 'total_amount', orderable: false },
                { data: 'adjustment_amt_formatted', name: 'adjustment_amt', orderable: false },
                { data: 'total_payable_formatted', name: 'total_payable_atm', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: [0, 1, 2, 3, 4, 5, 6, 7], // All data columns
                    className: 'text-center align-middle'
                },
                {
                    targets: 2, // Customer phone column
                    render: function (data, type, row) {
                        if (row.customer_id) {
                            return '<a href="' + Urls.showCustomer.replace('customerID', row.customer_id) + '" class="text-decoration-none">' + (row.customer_phone || '-') + '</a>';
                        }
                        return row.customer_phone || '-';
                    }
                },
                {
                    targets: 3, // Sale invoice column
                    render: function (data, type, row) {
                        if (row.sale_id) {
                            return '<a href="' + Urls.showSale.replace('saleID', row.sale_id) + '" class="text-decoration-none">' + (row.sale_invoice || '-') + '</a>';
                        }
                        return row.sale_invoice || '-';
                    }
                },
                {
                    targets: 8, // Action column
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        return '<a href="' + Urls.showReturn.replace('returnID', row.id) + '" class="btn btn-sm thm-btn-bg thm-btn-text-color" data-toggle="tooltip" title="View Details"><i class="fa-solid fa-eye"></i></a> ' +
                               '<a href="' + Urls.editReturn.replace('returnID', row.id) + '" class="btn btn-sm thm-btn-bg thm-btn-text-color" data-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>';
                    }
                }
            ]
        };
    }

    var searchCustomer = function (phone) {
        $.ajax({
            url: Urls.searchCustomer,
            type: 'GET',
            data: { phone: phone },
            success: function(response) {
                if(response.status === 'success') {
                    displayCustomerResults(response.customers);
                } else {
                    $('#customerResults').hide();
                }
            },
            error: function() {
                $('#customerResults').hide();
            }
        });
    }

    var displayCustomerResults = function (customers) {
        if (!customers || customers.length === 0) {
            $('#customerResults').hide();
            return;
        }

        let html = '';
        customers.forEach(function(customer) {
            html += '<span style="cursor: pointer;" class="list-group-item list-group-item-action customer-option" ' +
                   'data-id="' + customer.id + '" data-name="' + customer.name + '" data-phone="' + customer.phone1 + '">' +
                   customer.name + ' - ' + customer.phone1 +
                   '</span>';
        });

        $('#customerResults').html(html).show();

        // Handle customer selection
        $(document).off('click', '.customer-option').on('click', '.customer-option', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const customerId = $(this).data('id');
            const customerName = $(this).data('name');
            const customerPhone = $(this).data('phone');

            selectedCustomerId = customerId;
            $('#customerId').val(customerId);
            $('#customerName').val(customerName);
            
            // Set phone value without triggering search
            isSettingCustomer = true;
            $('#customerPhone').val(customerPhone);
            setTimeout(function() {
                isSettingCustomer = false;
            }, 500);
            
            $('#customerResults').hide();

            // Load customer sales
            loadCustomerSales(customerId);
        });
    }

    var loadCustomerSales = function (customerId) {
        $.ajax({
            url: Urls.getCustomerSales,
            type: 'GET',
            data: { customer_id: customerId },
            success: function(response) {
                if(response.status === 'success') {
                    displayCustomerSales(response.sales);
                    $('#saleSelectionGroup').show();
                } else {
                    toastr.error(response.message || 'Failed to load sales.');
                }
            },
            error: function() {
                toastr.error('Failed to load customer sales.');
            }
        });
    }

    var displayCustomerSales = function (sales) {
        let html = '<option value="">Select a sale</option>';
        sales.forEach(function(sale) {
            html += '<option value="' + sale.id + '">' + sale.invoice_code + ' - ' + sale.formattedDate + ' (Total: ' + sale.total_formatted + ')</option>';
        });
        $('#saleSelect').html(html);
    }

    var loadSaleItems = function (saleId) {
        $.ajax({
            url: Urls.getSaleItems,
            type: 'GET',
            data: { sale_id: saleId },
            success: function(response) {
                if(response.status === 'success') {
                    displaySaleItems(response.items);
                    $('#returnItemsCard').show();
                    $('#returnInfoCard').show();
                } else {
                    toastr.error(response.message || 'Failed to load sale items.');
                }
            },
            error: function() {
                toastr.error('Failed to load sale items.');
            }
        });
    }

    var displaySaleItems = function (items) {
        if (!items || items.length === 0) {
            $('#returnItemsTableBody').html('<tr><td colspan="7" class="text-center">No items found in this sale.</td></tr>');
            return;
        }

        let html = '';
        returnItems = [];

        items.forEach(function(item) {
            html += '<tr data-sales-item-id="' + item.id + '">';
            html += '<td class="text-center">' + (item.product_name || '-') + '</td>';
            html += '<td class="text-center">' + (item.variation_name || '-') + '</td>';
            html += '<td class="text-center">' + parseFloat(item.unit_price).toFixed(2) + '</td>';
            html += '<td class="text-center">' + item.quantity + '</td>';
            html += '<td class="text-center">';
            html += '<input type="number" class="form-control form-control-sm rounded return-qty" ' +
                   'value="0" min="0" max="' + item.quantity + '" ' +
                   'data-sales-item-id="' + item.id + '" ' +
                   'data-unit-price="' + item.unit_price + '">';
            html += '</td>';
            html += '<td class="text-center">';
            html += '<input type="checkbox" class="form-check-input is-sellable" ' +
                   'data-sales-item-id="' + item.id + '" checked>';
            html += '</td>';
            html += '<td class="text-center">';
            html += '<button type="button" class="btn btn-sm btn-primary add-return-item-btn" ' +
                   'data-sales-item-id="' + item.id + '">Create</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#returnItemsTableBody').html(html);

        // Handle add return item button
        $(document).off('click', '.add-return-item-btn').on('click', '.add-return-item-btn', function() {
            const salesItemId = $(this).data('sales-item-id');
            addReturnItem(salesItemId);
        });
    }

    var addReturnItem = function (salesItemId) {
        const row = $('tr[data-sales-item-id="' + salesItemId + '"]');
        const qty = parseInt(row.find('.return-qty').val()) || 0;
        const unitPrice = parseFloat(row.find('.return-qty').data('unit-price')) || 0;
        const isSellable = row.find('.is-sellable').is(':checked');
        const maxQty = parseInt(row.find('.return-qty').attr('max')) || 0;

        if (qty <= 0) {
            toastr.error('Please enter a valid return quantity.');
            return;
        }

        if (qty > maxQty) {
            toastr.error('Return quantity cannot exceed original quantity.');
            return;
        }

        // Check if item already added
        const existingItem = returnItems.find(item => item.sales_item_id == salesItemId);
        if (existingItem) {
            // Update existing item
            existingItem.qty = qty;
            existingItem.is_sellable = isSellable;
            existingItem.unit_price = unitPrice;
            toastr.success('Return item updated.');
        } else {
            // Add new item
            returnItems.push({
                sales_item_id: salesItemId,
                qty: qty,
                is_sellable: isSellable,
                unit_price: unitPrice
            });
            toastr.success('Return item added.');
        }

        // Disable the row inputs
        row.find('.return-qty').prop('readonly', true);
        row.find('.is-sellable').prop('disabled', true);
        row.find('.add-return-item-btn').prop('disabled', true).text('Added');

        calculateTotal();
    }

    var calculateTotal = function () {
        let totalAmount = 0;

        if (typeof returnItems !== 'undefined' && returnItems.length > 0) {
            returnItems.forEach(function(item) {
                totalAmount += parseFloat(item.unit_price) * parseInt(item.qty);
            });
        } else {
            // For edit page, calculate from table
            $('#returnItemsTableBody tr').each(function() {
                const qty = parseInt($(this).find('.return-qty').val()) || 0;
                let unitPrice = parseFloat($(this).find('.return-qty').data('unit-price')) || 0;
                if (!unitPrice) {
                    // Try to get from table cell
                    const priceText = $(this).find('td:eq(2)').text().replace(/[^0-9.]/g, '');
                    unitPrice = parseFloat(priceText) || 0;
                }
                totalAmount += unitPrice * qty;
            });
        }

        // Calculate total payable (total amount + adjustment)
        const adjustmentAmt = parseFloat($('#adjustmentAmt').val()) || 0;
        const totalPayableAtm = totalAmount + adjustmentAmt;

        // Update total payable field (total amount is calculated, not stored in form)
        $('#totalPayableAtm').val(totalPayableAtm.toFixed(2));
    }

    var saveReturn = function () {
        if (!selectedCustomerId) {
            toastr.error('Please select a customer.');
            return;
        }

        if (!selectedSaleId) {
            toastr.error('Please select a sale.');
            return;
        }

        if (!returnItems || returnItems.length === 0) {
            toastr.error('Please add at least one return item.');
            return;
        }

        const formData = {
            customer_id: selectedCustomerId,
            sale_id: selectedSaleId,
            reason: $('#returnReason').val(),
            note: $('#returnNote').val(),
            status: $('#returnStatus').val(),
            total_payable_atm: parseFloat($('#totalPayableAtm').val()) || 0,
            adjustment_amt: parseFloat($('#adjustmentAmt').val()) || 0,
            return_items: returnItems
        };

        WinPos.Common.postAjaxCall(Urls.saveReturn, JSON.stringify(formData), function(response) {
            if(response.status === 'success'){
                toastr.success(response.message);
                if(response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    window.location.href = '/stock/return';
                }
            } else {
                if(response.message){
                    toastr.error(response.message);
                }
                if(response.errors){
                    WinPos.Common.showValidationErrors(response.errors);
                }
            }
        }, function(xhr) {
            var response = xhr.responseJSON || {};
            if(response.message){
                toastr.error(response.message);
            } else {
                toastr.error('An error occurred while creating the return.');
            }
            if(response.errors && !response.message){
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateReturn = function (returnId) {
        // Collect return items from table
        const items = [];
        $('#returnItemsTableBody tr').each(function() {
            const salesItemId = $(this).data('sales-item-id');
            const qty = parseInt($(this).find('.return-qty').val()) || 0;
            const isSellable = $(this).find('.is-sellable').is(':checked');
            let unitPrice = parseFloat($(this).find('.return-qty').data('unit-price')) || 0;
            if (!unitPrice) {
                const unitPriceText = $(this).find('td:eq(2)').text().replace(/[^0-9.]/g, '');
                unitPrice = parseFloat(unitPriceText) || 0;
            }

            if (qty > 0) {
                items.push({
                    sales_item_id: salesItemId,
                    qty: qty,
                    is_sellable: isSellable,
                    unit_price: unitPrice
                });
            }
        });

        if (items.length === 0) {
            toastr.error('Please add at least one return item.');
            return;
        }

        const formData = {
            reason: $('#returnReason').val(),
            note: $('#returnNote').val(),
            status: $('#returnStatus').val(),
            total_payable_atm: parseFloat($('#totalPayableAtm').val()) || 0,
            adjustment_amt: parseFloat($('#adjustmentAmt').val()) || 0,
            return_items: items
        };

        WinPos.Common.putAjaxCallPost(Urls.updateReturn, JSON.stringify(formData), function(response) {
            if(response.status === 'success'){
                toastr.success(response.message);
                window.location.href = '/stock/return';
            } else {
                if(response.message){
                    toastr.error(response.message);
                }
                if(response.errors){
                    WinPos.Common.showValidationErrors(response.errors);
                }
            }
        }, function(xhr) {
            var response = xhr.responseJSON || {};
            if(response.message){
                toastr.error(response.message);
            } else {
                toastr.error('An error occurred while updating the return.');
            }
            if(response.errors && !response.message){
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var resetForm = function () {
        // Only reset if no customer is selected
        if (!selectedCustomerId) {
            selectedSaleId = null;
            returnItems = [];
            $('#customerId').val('');
            $('#customerName').val('');
            $('#saleSelect').html('<option value="">Select a sale</option>');
            $('#returnItemsTableBody').html('');
            $('#saleSelectionGroup').hide();
            $('#returnItemsCard').hide();
            $('#returnInfoCard').hide();
        }
    }

    var openPaymentModal = function (returnId) {
        $('#returnId').val(returnId);
        $('#paymentId').val('');
        $('#returnPaymentForm')[0].reset();
        $('#returnPaymentModalLabel').text('Add Payment');
        $('#paymentViaGroup').hide();
        $('#transactionIdGroup').hide();
        $('#paymentVia').html('<option value="">Select Option</option>');
        WinPos.Common.showBootstrapModal('returnPaymentModal');
    }

    var handlePaymentMethodChange = function (paymentMethod) {
        const paymentViaGroup = $('#paymentViaGroup');
        const transactionIdGroup = $('#transactionIdGroup');
        const paymentVia = $('#paymentVia');
        
        paymentViaGroup.hide();
        transactionIdGroup.hide();
        paymentVia.html('<option value="">Select Option</option>');
        paymentVia.prop('required', false);
        $('#transactionId').prop('required', false);

        if (paymentMethod === 'cash') {
            paymentViaGroup.show();
            paymentVia.prop('required', true);
            paymentVia.html('<option value="cash">Cash</option>');
        } else if (paymentMethod === 'card') {
            paymentViaGroup.show();
            transactionIdGroup.show();
            paymentVia.prop('required', true);
            $('#transactionId').prop('required', true);
            paymentVia.html(
                '<option value="">Select Card Type</option>' +
                '<option value="visa">Visa</option>' +
                '<option value="mastercard">MasterCard</option>' +
                '<option value="amex">American Express</option>'
            );
        } else if (paymentMethod === 'wallet') {
            paymentViaGroup.show();
            transactionIdGroup.show();
            paymentVia.prop('required', true);
            $('#transactionId').prop('required', true);
            paymentVia.html(
                '<option value="">Select Provider</option>' +
                '<option value="bkash">bKash</option>' +
                '<option value="nagad">Nagad</option>' +
                '<option value="rocket">Rocket</option>'
            );
        }
    }

    var savePayment = function (returnId, paymentUrls) {
        const formData = {
            payment_method: $('#paymentMethod').val(),
            payment_via: $('#paymentVia').val(),
            amount: parseFloat($('#paymentAmount').val()) || 0,
            transaction_id: $('#transactionId').val() || null,
            note: $('#paymentNote').val() || null
        };

        if (!formData.payment_method) {
            toastr.error('Please select payment method.');
            return;
        }

        if (!formData.payment_via) {
            toastr.error('Please select payment via.');
            return;
        }

        if (formData.amount <= 0) {
            toastr.error('Please enter a valid amount.');
            return;
        }

        const paymentId = $('#paymentId').val();
        
        if (paymentId) {
            // Update payment
            WinPos.Common.putAjaxCallPost(paymentUrls.updatePayment.replace('PAYMENT_ID', paymentId), JSON.stringify(formData), function(response) {
                if(response.status === 'success'){
                    toastr.success(response.message);
                    WinPos.Common.hideBootstrapModal('returnPaymentModal');
                    location.reload();
                } else {
                    if(response.message){
                        toastr.error(response.message);
                    }
                    if(response.errors){
                        WinPos.Common.showValidationErrors(response.errors);
                    }
                }
            }, function(xhr) {
                var response = xhr.responseJSON || {};
                if(response.message){
                    toastr.error(response.message);
                } else {
                    toastr.error('An error occurred while updating the payment.');
                }
                if(response.errors && !response.message){
                    WinPos.Common.showValidationErrors(response.errors);
                }
            });
        } else {
            // Create payment
            WinPos.Common.postAjaxCall(paymentUrls.storePayment, JSON.stringify(formData), function(response) {
                if(response.status === 'success'){
                    toastr.success(response.message);
                    WinPos.Common.hideBootstrapModal('returnPaymentModal');
                    location.reload();
                } else {
                    if(response.message){
                        toastr.error(response.message);
                    }
                    if(response.errors){
                        WinPos.Common.showValidationErrors(response.errors);
                    }
                }
            }, function(xhr) {
                var response = xhr.responseJSON || {};
                if(response.message){
                    toastr.error(response.message);
                } else {
                    toastr.error('An error occurred while saving the payment.');
                }
                if(response.errors && !response.message){
                    WinPos.Common.showValidationErrors(response.errors);
                }
            });
        }
    }

    var editPayment = function (returnId, paymentId, paymentUrls) {
        $.ajax({
            url: paymentUrls.getPayment.replace('PAYMENT_ID', paymentId),
            type: 'GET',
            success: function(response) {
                if(response.status === 'success') {
                    const payment = response.payment;
                    $('#returnId').val(returnId);
                    $('#paymentId').val(payment.id);
                    $('#paymentMethod').val(payment.payment_method);
                    $('#paymentAmount').val(payment.amount);
                    $('#transactionId').val(payment.transaction_id || '');
                    $('#paymentNote').val(payment.note || '');
                    $('#returnPaymentModalLabel').text('Edit Payment');
                    
                    // Handle payment method change to show appropriate fields
                    handlePaymentMethodChange(payment.payment_method);
                    $('#paymentVia').val(payment.payment_via);
                    
                    WinPos.Common.showBootstrapModal('returnPaymentModal');
                } else {
                    toastr.error(response.message || 'Failed to load payment.');
                }
            },
            error: function() {
                toastr.error('Failed to load payment.');
            }
        });
    }

    var deletePayment = function (returnId, paymentId, paymentUrls) {
        if (!confirm('Are you sure you want to delete this payment?')) {
            return;
        }

        WinPos.Common.deleteAjaxCallPost(paymentUrls.deletePayment.replace('PAYMENT_ID', paymentId), function(response) {
            if(response.status === 'success'){
                toastr.success(response.message);
                // Reload page to refresh payment history
                location.reload();
            } else {
                if(response.message){
                    toastr.error(response.message);
                }
            }
        }, function(xhr) {
            var response = xhr.responseJSON || {};
            if(response.message){
                toastr.error(response.message);
            } else {
                toastr.error('An error occurred while deleting the payment.');
            }
        });
    }

    return {
        datatableConfiguration: datatableConfiguration,
        searchCustomer: searchCustomer,
        loadCustomerSales: loadCustomerSales,
        loadSaleItems: loadSaleItems,
        addReturnItem: addReturnItem,
        calculateTotal: calculateTotal,
        saveReturn: saveReturn,
        updateReturn: updateReturn,
        resetForm: resetForm,
        setIsSettingCustomer: function(value) {
            isSettingCustomer = value;
        },
        getIsSettingCustomer: function() {
            return isSettingCustomer;
        },
        openPaymentModal: openPaymentModal,
        handlePaymentMethodChange: handlePaymentMethodChange,
        savePayment: savePayment,
        editPayment: editPayment,
        deletePayment: deletePayment
    }
})(typeof returnUrls !== 'undefined' ? returnUrls : {});
