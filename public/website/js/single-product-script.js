$(document).ready(function() {

    $('.variation-btn').on('click', function(e) {
        e.preventDefault();
        $('.variation-btn').removeClass('active');
        $(this).addClass('active');
    });

    $('.qty-plus-btn').on('click', function(e) {
        e.preventDefault();
        const $quantityInput = $('.quantity');
        let quantity = parseInt($quantityInput.val()) || 1;
        quantity = Math.max(1, quantity + 1);
        $quantityInput.val(quantity);
    });

   
    $('.qty-minus-btn').on('click', function(e) {
        e.preventDefault();
        const $quantityInput = $('.quantity');
        let quantity = parseInt($quantityInput.val()) || 1;
        quantity = Math.max(1, quantity - 1);
        $quantityInput.val(quantity);
    });


    $('#addToCartBtn').on('click', function(e) {
        e.preventDefault();

        let selectedProduct = getSelectedProductData();
        if (selectedProduct) {
            let isAdded = Website.AddToCart.addProductToWebsiteCart(selectedProduct);
            if(isAdded){
                Website.Common.showToastMessage('success', 'The '+selectedProduct.variation_tagline + ' added to cart successfully!');
                Website.AddToCart.setWebsiteCartCount();
            }
        }
    });
})
// end jQuery

function getSelectedProductData(){
    let selectedVariation = $('.variation-btn.active');
    if (selectedVariation.length === 0 || selectedVariation.length > 1) {
        Website.Common.showToastMessage('error', 'Please select a size / variation first');
        return;
    }

    let quantity = parseInt($('#quantityInput').val());
    if (isNaN(quantity) || quantity <= 0) {
        Website.Common.showToastMessage('error', 'Please enter a valid quantity');
        return;
    }

    let selectedProductData = {
        id: $("#addToCartBtn").data('product-id'),
        name: $("#addToCartBtn").data('product-name'),
        image: $("#addToCartBtn").data('product-image'),

        quantity: quantity,

        variation_id: selectedVariation.data('variation-id'),
        variation_tagline: selectedVariation.data('variation-tagline'),
        variation_price_after_discount: selectedVariation.data('variation-price-after-discount'),
        variation_selling_price: selectedVariation.data('variation-selling-price'),
        variation_discount_type: selectedVariation.data('variation-discount-type'),
        variation_discount_value: selectedVariation.data('variation-discount-value'),
    }
    return selectedProductData;
}