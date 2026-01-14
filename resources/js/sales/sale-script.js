WinPos.sale = (function (Urls){
    var currentSalesDetails = {};

    var showSaleModal = function (saleID) {

        WinPos.Common.getAjaxCall(
            Urls.showSaleModal.replace('saleID', saleID),
            function (response) {

                if (response.status !== 'success') {
                    alert("Something went wrong");
                    return;
                }

                currentSalesDetails = response;
                const sale = response.sale;

                /* =====================================================
                * BASIC INFO
                * ===================================================== */
                $('#POSID').text(sale.POSID ?? '-');
                $('#invoice_code').text(sale.invoice_code ?? '-');
                $('#created_by').text(response.created_by_user?.name ?? '-');
                $('#updated_by').text(response.updated_by_user?.name ?? '-');
                $('#formattedCreatedDate').text(sale.formattedCreatedDate ?? '-');
                $('#formattedUpdatedDate').text(sale.formattedUpdatedDate ?? '-');

                $('#total_amount').text(`${Number(sale.total_amount || 0).toFixed(2)} Tk.`);

                let discountText = `${Number(sale.discount_amount || 0).toFixed(2)} Tk.`;
                if (sale.discount_type === 'fixed') {
                    discountText += ' (Fixed)';
                } else if (sale.discount_type === 'percentage') {
                    discountText += ` (${sale.discount_value}%)`;
                }
                $('#discount_amount').text(discountText);

                $('#adjustment_amount').text(`${Number(sale.adjustmentAmt || 0).toFixed(2)} Tk.`);
                $('#total_payable_amount').text(`${Number(sale.total_payable_amount || 0).toFixed(2)} Tk.`);
                $('#sale_note').text(sale.note || '-');

                /* =====================================================
                * SERVICE TABLE
                * ===================================================== */
                const serviceTableBody = $("#serviceItemsTable tbody");
                serviceTableBody.html('');

                if (Array.isArray(response.serviceList) && response.serviceList.length) {

                    response.serviceList.forEach(function (item) {

                        const qty   = Number(item.quantity) || 0;
                        const price = Number(item.selling_price) || 0;
                        const total = qty * price;

                        let row = `
                            <tr>
                                <td>${item.code ?? '-'}</td>
                                <td>${item.name ?? '-'}</td>
                                <td class="text-center">${item.staff_name ?? 'None'}</td>
                                <td class="text-center">${qty}</td>
                                <td class="text-end">${price.toFixed(2)} Tk.</td>
                                <td class="text-end">${total.toFixed(2)} Tk.</td>
                            </tr>
                        `;
                        serviceTableBody.append(row);
                    });

                } else {
                    serviceTableBody.append(`
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No service items found
                            </td>
                        </tr>
                    `);
                }

                /* =====================================================
                * PRODUCT TABLE
                * ===================================================== */
                const productTableBody = $("#productItemsTable tbody");
                productTableBody.html('');

                if (Array.isArray(response.productList) && response.productList.length) {

                    response.productList.forEach(function (item) {

                        const qty   = Number(item.quantity) || 0;
                        const price = Number(item.selling_price) || 0;
                        const total = qty * price;

                        let row = `
                            <tr>
                                <td>${item.code ?? '-'}</td>
                                <td>${item.name ?? '-'}</td>
                                <td class="text-center">${qty}</td>
                                <td class="text-end">${price.toFixed(2)} Tk.</td>
                                <td class="text-end">${total.toFixed(2)} Tk.</td>
                            </tr>
                        `;
                        productTableBody.append(row);
                    });

                } else {
                    productTableBody.append(`
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No product items found
                            </td>
                        </tr>
                    `);
                }

                /* =====================================================
                * PAYMENT TABLE
                * ===================================================== */
                const paymentTableBody = $("#paymentsTableBody");
                paymentTableBody.html('');

                if (Array.isArray(response.payments) && response.payments.length) {

                    response.payments.forEach(function (payment) {

                        let row = `
                            <tr>
                                <td>${payment.payment_method}</td>
                                <td class="text-center">${payment.payment_via}</td>
                                <td class="text-end">
                                    ${Number(payment.paid_amount || 0).toFixed(2)} Tk.
                                </td>
                                <td>${payment.transaction_id || '-'}</td>
                                <td>${payment.note || '-'}</td>
                                <td class="text-center">${payment.receivedBy}</td>
                                <td class="text-center">
                                    ${WinPos.Common.dataTableCreatedOnCell(
                                        payment.formattedTime,
                                        payment.formattedDate
                                    )}
                                </td>
                            </tr>
                        `;
                        paymentTableBody.append(row);
                    });

                } else {
                    paymentTableBody.append(`
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No payments found
                            </td>
                        </tr>
                    `);
                }

                /* =====================================================
                * SHOW MODAL
                * ===================================================== */
                $("#showSaleBasicInfoTab").trigger('click');
                WinPos.Common.showBootstrapModal('showSaleModal');
            }
        );
    };



    var deleteSale = function (saleID){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteSale.replace('saleID', saleID), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.deleteRow();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }
    var getImageAsBase64 = function (imageUrl) {
        return new Promise(function(resolve, reject) {
            let img = new Image();
            img.crossOrigin = 'Anonymous';

            img.onload = function () {
                let canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;

                let ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                try {
                    let dataURL = canvas.toDataURL('image/png');
                    resolve(dataURL);
                } catch (err) {
                    reject('Error converting image to Base64: ' + err);
                }
            };

            img.onerror = function () {
                reject('Image failed to load.');
            };

            img.src = imageUrl;

            if (img.complete || img.complete === undefined) {
                img.src = imageUrl;
            }
        });
    }
    var printReceiptFunc = function(){
        let data = {};

        const cartObj = {
            items: [],
            total: 0,
            discountType: 'fixed',
            discount: 0,
            customer: {},
            cashier: {},
            adjustment: 0,
            loyaltyCard: {}
        };

        const items = currentSalesDetails.sale.items.map((item) => {
            return {
                id: item.product_id,
                name: item.product ? item.product.name : item.service.name,
                code: item.product ? item.product.code : item.service.code,
                price: parseFloat(item.selling_price),
                quantity: parseInt(item.quantity),
                staff_name: item.staff ? item.staff.name : null,
                tagline: item.variant_tagline ? item.variant_tagline : null,
                type: item.type,
                discount_type: item.discount_type,
                discount_value: item.discount_value
            };
        });

        cartObj.total = parseFloat(currentSalesDetails.sale.total_amount);
        cartObj.discount = parseFloat(currentSalesDetails.sale.discount_amount);
        cartObj.cashier = {id: currentSalesDetails.created_by_user.id, name: currentSalesDetails.created_by_user.name};
        cartObj.customer = currentSalesDetails.sale.customer;
        cartObj.items = items;
        cartObj.adjustment = parseFloat(currentSalesDetails.sale.adjustmentAmt);

        if (currentSalesDetails.loyaltyHistories.length > 0) {
            cartObj.loyaltyCard.verifyCard = true;
        }else{
            cartObj.loyaltyCard.verifyCard = false;
        }     

        data.header = {};
        data.header.company_name = accountInfoSettings.companyName;
        data.header.company_phone = accountInfoSettings.primaryPhone;
        data.header.company_address = accountInfoSettings.address;
        data.header.company_pos_logo = WinPos.Common.CommonVariables.accountLogo;

        data.header.invoice_no = 'VC-87';

        data.salesDetails = {};
        data.salesDetails.customer = cartObj.customer;
        data.salesDetails.cashier = cartObj.cashier;
        data.salesDetails.cartInfo = cartObj;
        //data.salesDetails.applyLoyalty = cartObj.applyLoyalty;

        data.footer = {};
        data.footer.receipt_no = '105';

        WinPos.PrintReceipt.config = ['header', 'salesDetails', 'footer'];

        let dom = WinPos.PrintReceipt.getPreview(data);

        // preview the dom
        WinPos.PrintReceipt.print(dom);
    }

    var datatableConfig = function(){
        return {
            serverSide: true,
            ajax: {
                url: Urls.datatable,
                type: 'GET',
                data: function (d) {
                    return {
                        draw: d.draw,
                        start: d.start,
                        length: d.length,
                        search: d.search.value
                    }
                }
            },
            order: [[0, 'desc'], [6, 'desc']],
            columns: [
                {
                    data: null,
                    type: 'string',
                    orderable: 'true',
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.invoice_code;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        if (row.customer && row.customer.id) {
                            return `<a href="/sales/customer/${row.customer.id}/details" class="text-primary" style="text-decoration: none;" title="View Customer Details">${row.customer.name}</a>`;
                        }
                        return row.customer ? row.customer.name : '-';
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        // Extract numeric values from formatted strings
                        let payableAmt = parseFloat(row.total_payable_amount.replace(/[^\d.-]/g, '')) || 0;
                        let paidAmt = parseFloat(row.paidAmount.replace(/[^\d.-]/g, '')) || 0;
                        let warningIcon = '';
                        if (Math.abs(payableAmt - paidAmt) > 0.01) {
                            warningIcon = ' <i class="fa-solid fa-asterisk text-warning" title="Payable and Paid amounts do not match"></i>';
                        }
                        let discountTitle = row.disountTpye == 'Fixed'? `${row.discount_amount} Tk (Fixed)`: `${row.discount_amount} Tk (Percentage)`;
                        return `<span title="Discount: ${discountTitle}">${row.total_payable_amount}${warningIcon}</span>`;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        // Extract numeric values from formatted strings
                        let payableAmt = parseFloat(row.total_payable_amount.replace(/[^\d.-]/g, '')) || 0;
                        let paidAmt = parseFloat(row.paidAmount.replace(/[^\d.-]/g, '')) || 0;
                        let warningIcon = '';
                        if (Math.abs(payableAmt - paidAmt) > 0.01) {
                            warningIcon = ' <i class="fa-solid fa-asterisk text-warning" title="Payable and Paid amounts do not match"></i>';
                        }
                        return `${row.paidAmount}${warningIcon}`;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        let status = row.payment_status || 'pending';
                        let badgeClass = status === 'paid' ? 'success' : 'warning';
                        return `<span class="badge bg-${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        let status = row.sale_status || 'pending';
                        let badgeClass = status === 'completed' ? 'success' : (status === 'pending' ? 'info' : 'secondary');
                        return `<span class="badge bg-${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: true,
                    type: 'date',
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return WinPos.Common.dataTableCreatedOnCell(row.formattedTime, row.formattedDate);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle',
                    render: function (data, type, row) {
                        let showBtn = `<a href="`+Urls.showSale.replace('saleID', row.id)+`" class="btn btn-sm thm-btn-bg thm-btn-text-color"><i class="fa-solid fa-eye"></i></a>`;
                        let deleteBtn = ` <button data-id="${row.id}" class="btn btn-sm thm-btn-bg thm-btn-text-color delete-sale"><i class="fa-solid fa-trash"></i></button>`;
                        return showBtn  + deleteBtn;
                    }
                }
            ]
        }
    },
    getCurrentSalesDetails = function(){
        return currentSalesDetails;
    },

    setCurrentSalesDetails = function(data){
        currentSalesDetails = data;
    }

    return {
        deleteSale: deleteSale,
        showSaleModal: showSaleModal,
        getImageAsBase64: getImageAsBase64,
        printReceipt: printReceiptFunc,
        datatableConfiguration: datatableConfig,
        getCurrentSalesDetails: getCurrentSalesDetails,
        setCurrentSalesDetails: setCurrentSalesDetails
    }
})(saleUrls);
