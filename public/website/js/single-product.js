/**
 * Single Product Page Functionality
 * Handles quantity controls, variation selection, and cart integration
 */

(function() {
    'use strict';

    // Wait for jQuery and Cart to be available
    function initSingleProduct() {
        if (typeof jQuery === 'undefined' || typeof window.Cart === 'undefined') {
            setTimeout(initSingleProduct, 100);
            return;
        }

        var $ = jQuery;

        // Wait for DOM to be ready
        $(document).ready(function() {
            const $productInfo = $('#product-info');
            
            if ($productInfo.length === 0) {
                return; // Product info element not found
            }

            // Initialize product data
            let currentVariationId = parseInt($productInfo.data('variation-id')) || 0;
            let currentQuantity = parseInt($productInfo.data('quantity')) || 1;
            const productId = parseInt($productInfo.data('product-id'));
            const productName = $productInfo.data('product-name');

            /**
             * Update product info data attributes
             */
            function updateProductInfoData(variationId, tagline, price, quantity) {
                $productInfo.attr('data-variation-id', variationId);
                $productInfo.attr('data-variation-tagline', tagline);
                $productInfo.attr('data-variation-price', price);
                $productInfo.attr('data-quantity', quantity);
                
                currentVariationId = variationId;
                currentQuantity = quantity;
            }

            /**
             * Update price display
             */
            function updatePriceDisplay(price, originalPrice, hasDiscount) {
                const $priceDisplay = $('.price-display');
                const $originalPrice = $('.original-price');
                
                $priceDisplay.text('Tk.' + parseFloat(price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                
                if (hasDiscount && originalPrice) {
                    if ($originalPrice.length === 0) {
                        $priceDisplay.before(
                            '<span class="original-price" style="text-decoration: line-through; color: #999; margin-right: 8px;">' +
                            'Tk.' + parseFloat(originalPrice).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") +
                            '</span>'
                        );
                    } else {
                        $originalPrice.text('Tk.' + parseFloat(originalPrice).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    }
                } else {
                    $originalPrice.remove();
                }
            }

            /**
             * Add or update item in cart
             */
            function addOrUpdateCart() {
                const variationId = parseInt($productInfo.data('variation-id'));
                const quantity = parseInt($productInfo.data('quantity'));
                const tagline = $productInfo.data('variation-tagline');
                const price = parseFloat($productInfo.data('variation-price'));

                if (!variationId || variationId === 0) {
                    alert('Please select a variation');
                    return;
                }

                const itemData = {
                    productId: productId,
                    variationId: variationId,
                    quantity: quantity,
                    productName: productName,
                    variationTagline: tagline,
                    price: price
                };

                window.Cart.addOrUpdate(itemData);
                
                // Show feedback (you can customize this)
                showCartFeedback('Item added to cart!');
            }

            /**
             * Show cart feedback message
             */
            function showCartFeedback(message) {
                // Remove existing feedback if any
                $('.cart-feedback').remove();
                
                // Create and show feedback
                const $feedback = $('<div class="cart-feedback" style="position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 15px 20px; border-radius: 5px; z-index: 9999; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">' + message + '</div>');
                $('body').append($feedback);
                
                // Auto remove after 3 seconds
                setTimeout(function() {
                    $feedback.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }

            /**
             * Handle quantity increment
             */
            $('.qty-plus-btn').on('click', function(e) {
                e.preventDefault();
                const $quantityInput = $('.quantity');
                let quantity = parseInt($quantityInput.val()) || 1;
                quantity = Math.max(1, quantity + 1);
                $quantityInput.val(quantity);
                updateProductInfoData(currentVariationId, $productInfo.data('variation-tagline'), $productInfo.data('variation-price'), quantity);
                
                // Update cart if item exists
                if (currentVariationId > 0) {
                    addOrUpdateCart();
                }
            });

            /**
             * Handle quantity decrement
             */
            $('.qty-minus-btn').on('click', function(e) {
                e.preventDefault();
                const $quantityInput = $('.quantity');
                let quantity = parseInt($quantityInput.val()) || 1;
                quantity = Math.max(1, quantity - 1);
                $quantityInput.val(quantity);
                updateProductInfoData(currentVariationId, $productInfo.data('variation-tagline'), $productInfo.data('variation-price'), quantity);
                
                // Update cart if item exists
                if (currentVariationId > 0) {
                    addOrUpdateCart();
                }
            });

            /**
             * Handle quantity input change
             */
            $('.quantity').on('change blur', function() {
                let quantity = parseInt($(this).val()) || 1;
                quantity = Math.max(1, quantity);
                $(this).val(quantity);
                updateProductInfoData(currentVariationId, $productInfo.data('variation-tagline'), $productInfo.data('variation-price'), quantity);
                
                // Update cart if item exists
                if (currentVariationId > 0) {
                    addOrUpdateCart();
                }
            });

            /**
             * Handle variation button click
             */
            $('.variation-btn').on('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all variation buttons
                $('.variation-btn').removeClass('active');
                
                // Add active class to clicked button
                $(this).addClass('active');
                
                // Get variation data
                const variationId = parseInt($(this).data('id'));
                const tagline = $(this).data('tagline');
                const finalPrice = parseFloat($(this).data('final-price'));
                const sellingPrice = parseFloat($(this).data('selling-price'));
                const discountType = $(this).data('discount-type');
                const discountValue = $(this).data('discount-value');
                const hasDiscount = discountType && discountValue;
                
                // Update product info data attributes
                updateProductInfoData(variationId, tagline, finalPrice, currentQuantity);
                
                // Update price display
                updatePriceDisplay(finalPrice, sellingPrice, hasDiscount);
                
                // Update add to cart button variation id
                $('.add-to-cart-btn').attr('data-variation-id', variationId);
                
                // Add or update in cart
                addOrUpdateCart();
            });

            /**
             * Handle add to cart button click
             */
            $('.add-to-cart-btn').on('click', function(e) {
                e.preventDefault();
                addOrUpdateCart();
            });

            // Initialize: Check if current variation is in cart and sync quantity
            if (currentVariationId > 0 && typeof window.Cart !== 'undefined') {
                const cartItem = window.Cart.findItem(productId, currentVariationId);
                if (cartItem) {
                    // Sync quantity from cart
                    const cartQuantity = cartItem.quantity;
                    $('.quantity').val(cartQuantity);
                    updateProductInfoData(currentVariationId, $productInfo.data('variation-tagline'), $productInfo.data('variation-price'), cartQuantity);
                }
            }
        });
    }

    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSingleProduct);
    } else {
        initSingleProduct();
    }

})();
