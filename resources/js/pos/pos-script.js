WinPos.Pos = (function (Urls) {
    const cartObj = {
        items: [],
        total: 0,
        discountType: 'fixed',
        discount: 0,
        adjustment: 0,
        customer: {
            'id': '',
            'name': '',
            'phone1': '',
            'age_group': ''
        },
        cashier: {},
        loyaltyCard: {
            'id': '',
            'card_number': '',
            'status': '',
            'verifyCard': false,
            'skipLoyalty': false,
            'skipLoyaltyReason': ''
        }
    };

    const cartListener = [];

    var setCartCustomer = function (customer) {
        cartObj.customer = customer;
    }

    var setCustomerLoyaltyCard = function (card) {
        cartObj.loyaltyCard = card;
    }

    var setCartCashier = function (cashier) {
        cartObj.cashier = cashier;
    }


    var addCartListener = function (fn) {
        cartListener.push(fn);
    }

    var notify = function () {
        cartListener.forEach(fn => fn(cartObj));
    }

    var addCartItem = function (service) {
        debugger;
        console.log(service);

        let cartItem;

        if(service.type && service.type == 'Product') {
            cartItem = cartObj.items.find((item) => item.id == service.id && item.variation_id == service.variation_id);
        }else{
            cartItem = cartObj.items.find((item) => item.id == service.id);
        }

        if(service.type  && service.type == 'Product') {
            if(service.stock == 0) {
                alert('Product is out of stock');
                return;
            }
            if(cartItem && cartItem.quantity == service.stock) {
                alert('Product is out of stock. You can not add more.');
                return;
            }
        }

        if (cartItem) {
            cartItem.quantity++;
        } else {
            // Get default staff from service if available
            const staffId = service.todays_staff ? service.staff_id : null;
            const staffName = service.todays_staff ? service.todays_staff.name : null;

            cartObj.items.push({ 
                id: service.id, 
                name: service.name, 
                code: service.code, 
                price: parseFloat(service.price), 
                quantity: 1,
                staff_id: staffId,
                staff_name: staffName,
                type: service.type,
                variation_id: service.variation_id ? service.variation_id : null,
                tagline: service.tagline ? service.tagline : null,
                stock: service.stock
            });
        }

        updateCartTotal();
        notify();
    }

    var removeCartItem = function (serviceId,type = null,variation_id = null) {
        debugger;
        if(type && type == 'Product') {
            cartObj.items = cartObj.items.filter(item => 
                !(item.id == serviceId && item.variation_id == variation_id)
            );
        }
        else{
            cartObj.items = cartObj.items.filter((item) => item.id != serviceId);
        }

        updateCartTotal();
        notify();
    }

    var updateCartQuantity = function (serviceId, qty, type = null, variation_id = null) {
        let cartItem;
        if(type && type == 'Product') {
            cartItem = cartObj.items.find((item) => item.id == serviceId && item.variation_id == variation_id);
        }else{
            cartItem = cartObj.items.find((item) => item.id == serviceId);
        }

        const quantity = parseInt(qty);

        if (cartItem == null || cartItem == undefined) {
            toastr.error("Please insert valid service", "Invalid Service");
            return 0;
        }

        if (quantity == NaN || quantity <= 0) {
            toastr.error("Please insert valid quantity", "Invalid Quantity");
            return 0;
        }

        if(cartItem.type  && cartItem.type == 'Product') {
            if(quantity > cartItem.stock) {
                toastr.error("Product is out of stock", "Invalid Quantity");
                return 0;
            }
        }

        if (cartItem) {
            cartItem.quantity = quantity;
        }

        updateCartTotal();
        notify();

        return quantity;
    }

    var addCartDiscount = function (discountType, discountAmountStr) {
        const discountAmount = parseFloat(discountAmountStr);

        if (discountAmount == NaN || discountAmount < 0) {
            toastr.error("Please insert valid discount amount", "Invalid Discount Amount");
            return;
        }

        if (!['fixed', 'percentage'].includes(discountType)) {
            discountType = 'fixed';
        }

        cartObj.discountType = discountType;
        cartObj.discount = parseFloat(discountAmount);

        updateCartTotal();
        notify();
    }

    var addCartAdjustment = function (adjustmentAmountStr='0') {
        let adjustmentAmount = parseFloat(adjustmentAmountStr);
       
        if (isNaN(adjustmentAmount) || adjustmentAmount == 0) {
            adjustmentAmount = 0;
        }

        cartObj.adjustment = parseFloat(adjustmentAmount);
        updateCartTotal();
        notify();
    }


    var isCartEmpty = function () {
        return cartObj.items.length <= 0;
    }

    var updateCartTotal = function () {
        const totalSum = cartObj.items.reduce((total, item) => {
            return total + (item.price * item.quantity);
        }, 0);

        cartObj.total = totalSum;
    }

    var search = function (searchCriteria, categoryId, productOrService) {
        categoryId = (categoryId == null || categoryId == "") ? 0 : categoryId;
        productOrService = (productOrService == null || productOrService == "") ? 0 : productOrService;

        const params = new URLSearchParams({
            searchCriteria: searchCriteria || "",
            categoryId: categoryId || "0",
            productOrService: productOrService || "Product"
        });

        const queryString = "?" + params.toString();

        return new Promise((resolve, reject) => {

            const cacheKey = (searchCriteria + "-" + categoryId + "-" + productOrService).toLowerCase();

            if (search.cache[cacheKey]) {
                resolve(search.cache[cacheKey]);
            } else {
                WinPos.Common.getAjaxCall(
                    Urls.searchService + queryString,
                    (response) => {
                        search.cache[cacheKey] = response;
                        resolve(response);
                    },
                    (response) => { reject(response); }
                );
            }
        });
    }

    search.cache = {}; // can we move this to top? is it global?

    var saveSalesDetails = function (paymentData) {
        return new Promise((resolve, reject) => {

            const requestData = {
                services: cartObj.items.map(item => ({ 
                    id: item.id, 
                    quantity: item.quantity,
                    staff_id: item.staff_id || null,
                    variation_id: item.variation_id || null,
                    type: item.type,
                    price: item.price,
                    tagline: item.tagline
                })),
                discountType: cartObj.discountType,
                discount: cartObj.discount,
                customerId: cartObj.customer.id,
                payment: paymentData,
                adjustmentAmt: cartObj.adjustment
            };

            // ✅ Add loyalty fields only if feature is enabled
            if (WinPos.Common.isFeatureEnabled('ENABLED_LOYALTY')) {
                requestData.loyaltyCardNumber   = cartObj.loyaltyCard.card_number || null;
                requestData.loyaltyCardId       = cartObj.loyaltyCard.id || null;
                requestData.loyaltyCardVerified = cartObj.loyaltyCard.verifyCard || false;
                requestData.loyaltyCardStatus   = cartObj.loyaltyCard.status || null;
                requestData.skipLoyalty         = cartObj.loyaltyCard.skipLoyalty || false;
                requestData.skipLoyaltyReason   = cartObj.loyaltyCard.skipLoyaltyReason || null;
            }


            if (!Number.isInteger(requestData.customerId) || requestData.customerId < 1) {
                toastr.warning("Please Select or Create a Customer.");
                reject({ message: "Invalid Customer ID" });
                return;
            }

            WinPos.Common.postAjaxCall(
                Urls.saveSales,
                JSON.stringify(requestData),
                (response) => { resolve(response) },
                (response) => { reject(response) }
            );
        });
    }

    var getImageAsBase64 = function (imageUrl) {
        return new Promise(function (resolve, reject) {
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

    var printReceiptFunc = function (response) {
        // here we need to get company info or store into the variable on page load
        let data = {};

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

        data.footer = {};
        data.footer.receipt_no = '105';

        WinPos.PrintReceipt.config = ['header', 'salesDetails', 'footer'];

        let dom = WinPos.PrintReceipt.getPreview(data);

        // preview the dom
        WinPos.PrintReceipt.print(dom);
        //$('.modal-body').html(dom);
        //$('#receiptPreviewModal').modal('show');
    }

    var RenderSearchService = function (services) {

        const searchResultCon = $('#searchServiceContainer');

        if (services.length == 0) {
            $(searchResultCon).html('')
            $(searchResultCon).html('<h3 class="text-center">No Result Found</h3>');
            return;
        }

        const publicUrl = Urls.publicUrl;
        const gridItems = "";
        const listItems = "";

        const itemListDom = services.map((item) => {
            
            // populate the grid view items
            var imgCon = '';
            const girdDev = $('<div>');
            girdDev.addClass('grid-item d-flex flex-column align-items-center p-2');
            girdDev.attr('style', 'background-color: #ccc; cursor: pointer;');
            girdDev.attr('data-id', item.id);
            girdDev.attr('data-stock', item.stock);


            if (item.image && item.image.trim() !== '') {
                imgCon = $('<img>');
                imgCon.attr('src', `${publicUrl}images/${item.POSID}/services/${item.image}`);
                imgCon.addClass('rounded');
                imgCon.attr('style', 'width: 100px; height: 50px; object-fit: cover;');
            } else {
                imgCon = $('<div>');
                imgCon.addClass('rounded');
                imgCon.attr('style', 'background-color: #ffffffff; width: 100px; height: 50px;');
            }

            const prodName = $('<p>');
            prodName.addClass('m-0 mt-1 pos-page-font-size');
            prodName.attr('style', 'text-align: center;');
            prodName.attr('title', item.name);
            prodName.text(WinPos.Common.truncate(item.name, 15));

            const tagLine = $('<p>');
            tagLine.addClass('m-0')
            tagLine.attr('style', 'font-size: 12px;');
            tagLine.text(`(${item.tagline} | ${item.stock})`);

            const prodCode = $('<p>');
            prodCode.addClass('m-0')
            prodCode.attr('style', 'font-size: 12px;');
            prodCode.text(`(${item.code})`);

            const price = $('<p>');
            price.addClass('m-0')
            price.attr('style', 'font-size: 12px;');
            price.text(`(${item.price}) Tk.`);

            girdDev.append(imgCon);
            girdDev.append(prodName);
            if(item.type == "Product"){
                girdDev.append(tagLine);
            }
            girdDev.append(prodCode);
            girdDev.append(price);

            girdDev.on('click', () => {
                WinPos.Pos.cart.addItem(item)
            });

            // populate the list view items
            const listDiv = $('<div>');
            listDiv.addClass('list-item list-group-item list-group-item-action d-none pos-page-font-size');
            listDiv.attr('data-id', item.id);
            listDiv.attr('data-stock', item.stock);
            if(item.type == "Product"){
                listDiv.text(`${item.code} -> ${item.name} -> (${item.tagline} -> ${item.stock}) -> (${item.price}) Tk.`);
            }else{
                listDiv.text(`${item.code} -> ${item.name} -> (${item.price}) Tk.`);
            }

            listDiv.on('click', () => {
                WinPos.Pos.cart.addItem(item)
            });

            // return both views
            const parentDiv = $('<div>');
            parentDiv.append(girdDev);
            parentDiv.append(listDiv);

            return parentDiv;
        });

        $(searchResultCon).html('')
        $(searchResultCon).append(itemListDom);
    }


    var setTerminalCustomerForm = function (customer) {
        if (customer.id) {

            $("#terminalCustomerNameShow").text(customer.name);
            $("#terminalCustomerPhoneShow").text(customer.phone1);
            $("#terminalCustomerAgeGroupShow").text(customer.age_group);
            
            $("#terminalCustomerTotalNumberOfSalesShow").text(customer.totalSales);
            $("#terminalCustomerLastVisitShow").text(customer.lastVisit);
            

            // hide add button and show clear button
            $('#terminalCustomerAddBtn').addClass('d-none');
            $('#terminalCustomerClearBtn').removeClass('d-none');

            $("#terminalCustomerAddForm").addClass('d-none');
            $("#terminalCustomerRibbon").removeClass('d-none');
        }
    }

    var clearTerminalCustomerForm = function () {
        $("#terminalCustomerAddForm").removeClass('d-none');
        $("#terminalCustomerRibbon").addClass('d-none');

        // $("#terminalExistingCustomerLoyaltyInfo").html('');
        // $("#terminalNewCustomerLoyaltyInfo").removeClass('d-none');

        $(".terminal-customer-loyalty-info").addClass("d-none");

        $('#terminalCustomerName').val('');
        $('#terminalCustomerPhone').val('');
        $('#terminalCustomerAgeGroup')
            .val('')
            .trigger('change');

        // hide clear button and show add button
        $('#terminalCustomerAddBtn').removeClass('d-none');
        $('#terminalCustomerClearBtn').addClass('d-none');

        // customer btn
        $('#posLastSalesHistoryModalBtn').prop('disabled', true);
        $('#posCustomerInfoModalBtn').prop('disabled', true);

        if (WinPos.Common.isFeatureEnabled('ENABLED_LOYALTY')) {
            $('#posLoyaltyHistoryModalBtn').prop('disabled', true);
        }

        // focus on name field
        $('#terminalCustomerName').focus();
    }

    var updateCartStaff = function(itemId, staffId, staffName) {
        const cartItem = WinPos.Pos.cartObj.items.find(item => item.id == itemId);
        if (cartItem) {
            cartItem.staff_id = staffId;
            cartItem.staff_name = staffName;
            notify();
            return true;
        }

        return false;
    }

    var renderStaffCards = function (staffs, selectedStaffId) {
        const container = $('#staffCardsContainer');
        container.html('');
        
        if (staffs.length === 0) {
            container.html('<p class="text-center">No staffs available.</p>');
            return;
        }
        
        staffs.forEach(function (staff) {
            const isAssigned = staff.id == selectedStaffId;

            let cardClass = 'staff-card cursor-pointer';

            if (!staff.is_present) {
                cardClass += ' disabled';
            } else if (isAssigned) {
                cardClass += ' assigned';
            } else {
                cardClass += ' available thm-btn-bg thm-btn-text-color';
            }

            const card = `
                <div class="col-md-4 col-lg-3">
                    <div class="${cardClass}"
                        data-staff-id="${staff.id}"
                        data-staff-name="${staff.name}">
                        
                        <div class="staff-info">
                            <p class="mb-1"><strong>ID:</strong> ${staff.id}</p>
                            <p class="mb-1"><strong>Name:</strong> ${staff.name}</p>
                            <p class="mb-1"><strong>Today's Service:</strong> ${staff.today_service_count}</p>

                            ${
                                !staff.is_present
                                    ? '<p class="mb-1 text-danger staff-status"><strong>Absent Today</strong></p>'
                                    : isAssigned ? '<p class="mb-1 staff-status"><strong>Assigned <i class="fa fa-solid fa-check"></i></strong></p>' : '<p class="mb-1 staff-status"><strong>Available</strong></p>'
                            }
                        </div>
                    </div>
                </div>
            `;

            container.append(card);
        });

    }

    return {
        searchService: search,
        cartObj: cartObj,
        cart: {
            addItem: addCartItem,
            listener: addCartListener,
            remove: removeCartItem,
            updateQuantity: updateCartQuantity,
            applyDiscount: addCartDiscount,
            isEmpty: isCartEmpty,
            setCustomer: setCartCustomer,
            setCashier: setCartCashier,
            applyAdjustment: addCartAdjustment,
            updateStaff: updateCartStaff

        },
        saveSalesDetails: saveSalesDetails,
        printReceipt: printReceiptFunc,
        getImageAsBase64: getImageAsBase64,
        RenderSearchService: RenderSearchService,
        renderStaffCards: renderStaffCards,
        customer: {
            setTerminalCustomerForm: setTerminalCustomerForm,
            clearTerminalCustomerForm: clearTerminalCustomerForm,
            setCustomerLoyaltyCard: setCustomerLoyaltyCard
        }
    }
})(posUrls);
