Website.AddToCart = (function(data) {

    var setWebsiteCartObject = function (websiteCartObject) {
        localStorage.setItem('websiteCartObject', JSON.stringify(websiteCartObject));
    }

    var getWebsiteCartObject = function () {
        const websiteCartObject = localStorage.getItem('websiteCartObject');
        if(websiteCartObject) {
            return JSON.parse(websiteCartObject);
        }
        return {items: []};
    }

   var addProductToWebsiteCart = function (product) {
        let websiteCartObject = getWebsiteCartObject();
        let cartItem = null;

        if(addProductToWebsiteCart) {
            cartItem = websiteCartObject.items.find(item => item.id == product.id && item.variation_id == product.variation_id);
        }

        if(cartItem == null || cartItem == undefined) {
            websiteCartObject.items.push({
                id: product.id,
                name: product.name,
                image: product.image,
                quantity: product.quantity,

                variation_id: product.variation_id,
                variation_tagline: product.variation_tagline,
                variation_price_after_discount: product.variation_price_after_discount,
                variation_selling_price: product.variation_selling_price,
                variation_discount_type: product.variation_discount_type,
                variation_discount_value: product.variation_discount_value
            });
        }else{
            // just update the quantity
            cartItem.quantity = product.quantity;
        }
        setWebsiteCartObject(websiteCartObject);
        return true;
   }

   var removeProductFromWebsiteCart = function (product) {
        let websiteCartObject = getWebsiteCartObject();
        websiteCartObject.items = websiteCartObject.items.filter(item => !(item.id == product.id && item.variation_id == product.variation_id));
        setWebsiteCartObject(websiteCartObject);
   }

   var updateWebsiteCartQuantity = function (product) {
        let websiteCartObject = getWebsiteCartObject();
        let cartItem = websiteCartObject.items.find(item => item.id == product.id && item.variation_id == product.variation_id);
        if(cartItem){
            cartItem.quantity = product.quantity;
        }
        setWebsiteCartObject(websiteCartObject);
   }

   var clearWebsiteCart = function () {
        localStorage.removeItem('websiteCartObject');
   }

   var getWebsiteCartQuantity = function () {
        let websiteCartObject = getWebsiteCartObject();
        return websiteCartObject.items.length;
   }

   var setWebsiteCartCount = function () {
        let websiteCartObject = getWebsiteCartObject();
        $(".check-out-btn").append('<span class="cart-count">' + websiteCartObject.items.length + '</span>');
   }

   var renderWebsiteCartTable = function () {
        let tbody = '';
        let subtotal = 0;

        let websiteCartObject = getWebsiteCartObject();
        let items = websiteCartObject.items;

        items.forEach(item => {

            let unitPrice = item.variation_price_after_discount;
            let lineTotal = unitPrice * item.quantity;
            subtotal += lineTotal;

            tbody += `
                <tr>
                    <td>
                        <strong>${item.name}</strong>
                        <br>
                        <small class="text-muted">(${item.variation_tagline})</small>
                    </td>

                    <td class="text-center">
                        ${item.quantity} × ৳${unitPrice.toFixed(2)}
                    </td>

                    <td class="text-end">
                        ৳${lineTotal.toFixed(2)}
                    </td>

                    <td class="text-center">
                    <button class="remove-cart-item"
                        data-product-id="${item.id}"
                        data-variation-id="${item.variation_id}">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </td>
                </tr>
            `;
        });

        tbody += `
            <tr class="order-total-row">
                <td><strong>Order Total</strong> <small class="text-muted">(Without Delivery Charge)</small> </td>
                <td></td>
                <td class="text-end"><strong>৳${subtotal.toFixed(2)}</strong></td>
                <td></td>
            </tr>
        `;

        $('#cartTable tbody').html(tbody);
    }

    var updateDeliveryCharge = function (deliveryCharge = 0) {

        let websiteCartObject = getWebsiteCartObject();
        let subtotal = 0;

        websiteCartObject.items.forEach(item => {
            subtotal += item.quantity * item.variation_price_after_discount;
        });

        let grandTotal = subtotal + deliveryCharge;

        let extraRows = `
            <tr class="border-top delivery-row">
                <td><strong>Delivery Charge</strong></td>
                <td></td>
                <td class="text-end">৳${deliveryCharge.toFixed(2)}</td>
                <td></td>
            </tr>
            <tr class="border-top grand-row">
                <td><strong>Grand Total</strong></td>
                <td></td>
                <td class="text-end"><strong>৳${grandTotal.toFixed(2)}</strong></td>
                <td></td>
            </tr>
        `;

        // Remove old rows first
        $('#cartTable tbody .delivery-row, #cartTable tbody .grand-row').remove();

        // Append after Order Total
        $('#cartTable tbody tr:last').after(extraRows);
    }

    return {
        addProductToWebsiteCart: addProductToWebsiteCart,
        removeProductFromWebsiteCart: removeProductFromWebsiteCart,
        updateWebsiteCartQuantity: updateWebsiteCartQuantity,
        getWebsiteCartObject: getWebsiteCartObject,
        clearWebsiteCart: clearWebsiteCart,
        getWebsiteCartQuantity: getWebsiteCartQuantity,
        setWebsiteCartCount: setWebsiteCartCount,
        renderWebsiteCartTable: renderWebsiteCartTable,
        updateDeliveryCharge: updateDeliveryCharge
    };
})(websiteData);