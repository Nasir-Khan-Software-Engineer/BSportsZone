WinPos.sale = (function (Urls){
    var currentSalesDetails = {};

    var showSaleModal = function (saleID){
        WinPos.Common.getAjaxCall(Urls.showSaleModal.replace('saleID', saleID), function (response){
            //debugger;
            currentSalesDetails = response;

            if(response.status === 'success'){
                var sale = response.sale;

                // Sale basic info
                $('#POSID').text(sale.POSID);
                $('#invoice_code').text(sale.invoice_code);
                $('#created_by').text(response.created_by_user.name);
                $('#updated_by').text(response.updated_by_user.name);
                $('#formattedCreatedDate').text(sale.formattedCreatedDate);
                $('#formattedUpdatedDate').text(sale.formattedUpdatedDate);


                $('#total_amount').text(sale.total_amount + ' Tk.');
                let discountText = sale.discount_amount + ' Tk.';

                if (sale.discount_type === 'fixed') {
                    discountText += ' (Fixed)';
                } else if (sale.discount_type === 'percentage') {
                    discountText += ' (' + sale.discount_value + '%)';
                }
                $('#discount_amount').text(discountText);
                $('#adjustment_amount').text(sale.adjustmentAmt + ' Tk.');

                $('#total_payable_amount').text(sale.total_payable_amount + ' Tk.');
                $('#sale_note').text(sale.note || '-');

                // Items table
                const serviceTableBody = $("#itemsTable tbody");
                serviceTableBody.html('');

                sale.items.forEach(function (item) {
                    let code = item.service ? item.service.code : '';
                    let name = item.service ? item.service.name : '';
                    let qty = item.quantity ?? 0;
                    let price = item.selling_price ?? 0;
                    let staffName = item.staff ? item.staff.name : 'None';

                    let row = `
                        <tr>
                            <td>${code}</td>
                            <td>${name}</td>
                            <td class="text-center">${staffName}</td>
                            <td class="text-center">${qty}</td>
                            <td class="text-right">${parseFloat(price).toFixed(2)} Tk.</td>
                        </tr>
                    `;
                    serviceTableBody.append(row);
                });

                // Payments table
                const paymentTableBody = $("#paymentsTableBody");
                paymentTableBody.html('');

                response.payments.forEach(function (payment) {
                    let row = `
                        <tr>
                            <td>${payment.payment_method}</td>
                            <td class="text-center align-middle">${payment.payment_via}</td>
                            <td class="text-right align-middle">${parseFloat(payment.paid_amount).toFixed(2)} Tk.</td>
                            <td class="text-right align-middle">${payment.transaction_id || ''}</td>
                            <td class="align-middle">${payment.note || ''}</td>
                            <td class="text-center align-middle">${payment.receivedBy}</td>
                            <td class="text-center align-middle">${WinPos.Common.dataTableCreatedOnCell(payment.formattedTime, payment.formattedDate)}</td>
                        </tr>
                    `;
                    paymentTableBody.append(row);
                });

                // Show modal
                $("#showSaleBasicInfoTab").click();
                WinPos.Common.showBootstrapModal('showSaleModal');

            } else {
                alert("Something went wrong");
            }
        });
    }


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
        debugger;

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
                id: item.service.id,
                name: item.service.name,
                code: item.service.code,
                price: parseFloat(item.service.price),
                quantity: parseInt(item.quantity),
                discount: parseFloat(item.service.discount_value||0),
                staff_name: item.staff ? item.staff.name : null
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

        console.log(cartObj);

        data.header = {};
        data.header.company_name = accountInfoSettings.companyName;
        data.header.company_phone = accountInfoSettings.primaryPhone;
        data.header.company_address = accountInfoSettings.address;
        data.header.company_pos_logo = WinPos.Common.CommonVariables.accountLogo;

        //debugger;

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
            order: [[0, 'desc'], [2, 'desc']],
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
                    orderable: 'true',
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.total_amount;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        let discountTitle = row.disountTpye == 'Fixed'? `${row.discount_amount} Tk (Fixed)`: `${row.discount_amount} Tk (Percentage)`;
                        return `<span title="Discount: ${discountTitle}">${row.total_payable_amount}</span>`;
                    }
                },
                {
                    data: null,
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.paidAmount;
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
                    type: 'string',
                    orderable: true,
                    className: 'text-center align-middle',
                    render: function(data, type, row){
                        return row.created_by;
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
