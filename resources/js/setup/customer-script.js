WinPos.Customer = (function (Urls){
    var editCustomer = function (customerID){
        WinPos.Common.getAjaxCall(Urls.editCustomer.replace('customerID', customerID), function (response){
            if(response.status === 'success'){
                let customer = response.customer;
                $("#name").val(customer.name);
                $("#email").val(customer.email);
                $("#phone1").val(customer.phone1);
                $("#address").val(customer.address);
                $("#note").val(customer.note);
                $("#hiddenCustomerID").val(customer.id);
                $("#customerID").html(' | Customer ID: '+customer.id);


                if (customer.age_group == "Teen (13–19)") {
                    document.getElementById('age_group').selectedIndex = 1;
                } else if (customer.age_group == "Young Adult (20–35)") {
                    document.getElementById('age_group').selectedIndex = 2;
                } else if (customer.age_group == "Adult (36–55)") {
                    document.getElementById('age_group').selectedIndex = 3;
                } else {
                    document.getElementById('age_group').selectedIndex = 4;
                }

                $("#customerAddEditForm").attr('data-formSubmitFor', 'update');

                $("#customerBasicInfoTab").click();
                $("#customerAddEditModal").modal('show');

            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var saveCustomer = function (formData){
        const callback = arguments[1];

        WinPos.Common.postAjaxCall(Urls.saveCustomer, JSON.stringify(formData), function (response){
            if(callback && callback!=undefined){

                if(response.status === 'success'){
                    toastr.success(response.message);
                    callback(response.customer);
                }else if (response.status === 'exists'){
                    toastr.info(response.message);
                    callback(response.customer);
                }
                else{
                    WinPos.Common.showValidationErrors(response.errors);
                }

                return;
            }

            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("customerAddEditModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var updateCustomer = function (formData, customerID){
        WinPos.Common.putAjaxCallPost(Urls.updateCustomer.replace("customerID", customerID), JSON.stringify(formData), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
                WinPos.Common.hideBootstrapModal("customerAddEditModal");
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var deleteCustomer = function (customerID){
        WinPos.Common.deleteAjaxCallPost(Urls.deleteCustomer.replace('customerID', customerID), function (response){
            if(response.status === 'success'){
                WinPos.Datatable.refresh();
                toastr.success(response.message);
            }else{
                WinPos.Common.showValidationErrors(response.errors);
            }
        });
    }

    var isValidCustomer = function(formData){
        if (!formData.name || formData.name.trim().length < 3 || formData.name.trim().length > 200) {
            toastr.error("Name is not valid!");
            return false;
        }

        if(!formData.gender || (formData.gender != "M" && formData.gender != "F" && formData.gender != "O")){
            toastr.error("Please select gender!");
            return false;
        }

        if (formData.email && !WinPos.Common.isValidEmail(formData.email)) {
            toastr.error("Email is not valid!");
            return false;
        }

        if(!WinPos.Common.isValidPhoneNumber(formData.phone1)){
            toastr.error("Phone Number 1 is not valid!");
            return false;
        }

        if (formData.phone2 && (!WinPos.Common.isValidPhoneNumber(formData.phone2))) {
            toastr.error("Phone Number 2 is not valid!");
            return false;
        }

        if(formData.phone1 === formData.phone2){
            toastr.error("Phone Number 1 or Phone Number 2 is not valid!");
            return false;
        }

        if (!formData.address || formData.address.trim().length < 3 || formData.address.trim().length > 300) {
            toastr.error("Address is not valid!");
            return false;
        }

        if (formData.note && (formData.note.trim().length < 3 || formData.note.trim().length > 1000)) {
            toastr.error("Note is not valid!");
            return false;
        }

        return true;
    }

    var showCustomerInfo = function(customerId) {
        if (!customerId) {
            toastr.error("Customer ID is required");
            return;
        }

        // Convert to integer if it's a string
        customerId = parseInt(customerId);
        if (isNaN(customerId)) {
            toastr.error("Invalid customer ID");
            return;
        }

        // Show loading state
        $('#customerInfoModal').modal('show');
        clearCustomerInfoData();

        // Debug: Log the URL being called
        const url = Urls.getCustomerInfo.replace('customerID', customerId);
        console.log('Customer Info URL:', url);
        console.log('Customer ID:', customerId);

        // Fetch customer data
        WinPos.Common.getAjaxCall(
            url,
            function(response) {
                console.log('Customer Info Response:', response);
                if (response.status === 'success') {
                    populateCustomerInfo(response.customer);
                } else {
                    toastr.error(response.message || "Failed to load customer information");
                    $('#customerInfoModal').modal('hide');
                }
            },
            function(error) {
                console.log('Customer Info Error:', error);
                toastr.error("Error loading customer information");
                $('#customerInfoModal').modal('hide');
            }
        );
    };

    var populateCustomerInfo = function(customer) {
        // Basic Info Tab
        $('#customerInfoName').text(customer.name || '-');
        let genderText = '-';
        if (customer.gender === 'M') {
            genderText = 'Male';
        } else if (customer.gender === 'F') {
            genderText = 'Female';
        } else if (customer.gender === 'O') {
            genderText = 'Other';
        }
        $('#customerInfoGender').text(genderText);
        $('#customerInfoEmail').text(customer.email || '-');
        
        // Handle phone number masking
        if (hasShowPhonePermission) {
            $('#customerInfoPhone1').text(customer.phone1 || '-');
        } else {
            $('#customerInfoPhone1').text(maskPhoneNumber(customer.phone1) || '-');
        }
        
        $('#customerInfoAddress').text(customer.address || '-');
        $('#customerInfoAgeGroup').text(customer.age_group || '-');
        $('#customerInfoNote').text(customer.note || '-');
        $('#customerInfoCreatedOn').text(customer.formattedCreatedOn || '-');
        $('#customerInfoCreatedBy').text(customer.createdBy || '-');
    };
    

    var clearCustomerInfoData = function() {
        $('#customerInfoName').text('-');
        $('#customerInfoGender').text('-');
        $('#customerInfoEmail').text('-');
        $('#customerInfoPhone1').text('-');
        $('#customerInfoAddress').text('-');
        $('#customerInfoAgeGroup').text('-');
        $('#customerInfoNote').text('-');
        $('#customerInfoCreatedOn').text('-');
        $('#customerInfoCreatedBy').text('-');
    };

    var showLastSalesHistory = function(customerId) {
        if (!customerId) {
            toastr.error("Customer ID is required");
            return;
        }

        // Convert to integer if it's a string
        customerId = parseInt(customerId);
        if (isNaN(customerId) || customerId <= 0) {
            toastr.error("Invalid customer ID");
            return;
        }

        // Show modal and loading state
        $('#lastSalesHistoryModal').modal('show');
        $('#lastSalesHistoryLoading').removeClass('d-none');
        $('#lastSalesHistoryContent').addClass('d-none');
        $('#lastSalesHistoryEmpty').addClass('d-none');

        // Clear previous data
        clearLastSalesHistoryData();

        // Fetch last sales data
        const url = Urls.getLastSales.replace('customerID', customerId);
        
        WinPos.Common.getAjaxCall(
            url,
            function(response) {
                $('#lastSalesHistoryLoading').addClass('d-none');
                
                if (response.status === 'success') {
                    if (response.sale) {
                        populateLastSalesHistory(response);
                    } else {
                        $('#lastSalesHistoryEmpty').removeClass('d-none');
                    }
                } else {
                    toastr.error(response.message || "Failed to load sales history");
                    $('#lastSalesHistoryModal').modal('hide');
                }
            },
            function(error) {
                $('#lastSalesHistoryLoading').addClass('d-none');
                toastr.error("Error loading sales history");
                $('#lastSalesHistoryModal').modal('hide');
            }
        );
    };

    var populateLastSalesHistory = function(response) {
        const sale = response.sale;
        
        // Populate sales information
        $('#lastSaleDate').text(sale.formattedDate + ' ' + sale.formattedTime);
        $('#lastSaleInvoice').text(sale.invoice_code || '-');
        $('#lastSaleTotal').text(Number(sale.total_amount).toFixed(2) + ' Tk.');
        $('#lastSaleDiscount').text(Number(sale.discount_amount || 0).toFixed(2) + ' Tk.');
        $('#lastSalePaid').text(Number(response.totalPaid || sale.total_payable_amount).toFixed(2) + ' Tk.');
        $('#lastSalePaymentMethod').text(response.paymentMethod || '-');
        $('#lastSaleAdjustment').text(response.adjustmentAmount + ' Tk.');
        $('#lastSaleSalesBy').text(response.salesBy || '-');

        // Populate service details table
        const tbody = $('#lastSaleItemsTableBody');
        tbody.empty();
        
        if (sale.items && sale.items.length > 0) {
            sale.items.forEach(function(item, index) {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.product ? item.product.name : '-'}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-right">${Number(item.product_price).toFixed(2)} Tk.</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }

        // Show content
        $('#lastSalesHistoryContent').removeClass('d-none');
    };

    var clearLastSalesHistoryData = function() {
        $('#lastSaleDate').text('-');
        $('#lastSaleInvoice').text('-');
        $('#lastSaleTotal').text('-');
        $('#lastSaleDiscount').text('-');
        $('#lastSalePaid').text('-');
        $('#lastSalePaymentMethod').text('-');
        $('#lastSaleAdjustment').text('-');
        $('#lastSaleSalesBy').text('-');
        $('#lastSaleItemsTableBody').empty();
    };

    var maskPhoneNumber = function(phone) {
        if (!phone || phone.length !== 11) {
            return phone || '-';
        }
        return 'XXXXXXX' + phone.slice(-3);
    };

    var datatableConfig = function(){
        let columns = [
            {
                data: null,
                type: 'num',
                orderable: true,
                searchable: true,
                className: 'text-center align-middle',
                render: function(data, type, row){
                    return row.id;
                }
            },
            {
                data: null,
                type: 'string',
                orderable: true,
                searchable: true,
                className: 'text-center align-middle',
                render: function(data, type, row){
                    let name = row.name;

                    if(row.latest_card_id) {
                        name += ' <i class="fa-solid fa-credit-card thm-text-color"></i>';
                    }
                    return name;
                }
            },
            {
                data: null,
                type: 'string',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function(data, type, row){
                    return row.phone1;
                }
            },
            {
                data: null,
                type: 'num',
                orderable: true,
                searchable: false,
                className: 'text-center align-middle',
                render: function(data, type, row){
                    return row.purchases_count || 0;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                type: 'date',
                className: 'text-center align-middle',
                render: function(data, type, row){
                    return WinPos.Common.dataTableCreatedOnCell(row.formattedTime, row.formattedDate);
                }
            }
        ];

        // ✅ Add the Type column only if Loyalty feature is enabled
        if (WinPos.Common.isFeatureEnabled('ENABLED_LOYALTY')) {
            columns.push({
                data: null,
                type: 'string',
                orderable: true,
                searchable: true,
                className: 'text-center align-middle',
                render: function(data, type, row){
                    return row.type;
                }
            });
        }

        // ✅ Action buttons column (always included)
        columns.push({
            data: null,
            orderable: false,
            searchable: false,
            className: 'text-center align-middle',
            render: function (data, type, row) {
                let detailsUrl = Urls.detailsCustomer.replace('customerID', row.id);
                let viewBtn = `<a title="Details" class='btn thm-btn-bg thm-btn-text-color btn-sm' href="${detailsUrl}"><i class="fa-solid fa-eye"></i></a>`;
                let editBtn = `<button data-customerID="${row.id}" class='btn thm-btn-bg thm-btn-text-color btn-sm edit-customer' title="Edit"><i class='fa-solid fa-pen-to-square'></i></button>`;
                let deleteBtn = `<button data-customerID="${row.id}" class='btn thm-btn-bg thm-btn-text-color btn-sm delete-customer' title="Delete"><i class='fa-solid fa-trash'></i></button>`;
                return viewBtn + ' ' + editBtn + ' ' + deleteBtn;
            }
        });

        return {
            serverSide: true,
            processing: true,
            ajax: {
                url: Urls.datatable,
                type: 'GET',
                data: function (d) {
                    return {
                        draw: d.draw,
                        start: d.start,
                        length: d.length,
                        search: d.search.value,
                        order: d.order
                    }
                }
            },
            order: [[0, 'desc']],
            columns: columns
        }
    }

    return {
        saveCustomer: saveCustomer,
        updateCustomer: updateCustomer,
        deleteCustomer: deleteCustomer,
        editCustomer: editCustomer,
        isValidCustomer: isValidCustomer,
        showCustomerInfo: showCustomerInfo,
        clearCustomerInfoData: clearCustomerInfoData,
        showLastSalesHistory: showLastSalesHistory,
        datatableConfiguration: datatableConfig
    }
})(customerUrls);
