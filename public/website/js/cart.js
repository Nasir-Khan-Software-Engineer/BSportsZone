/**
 * Cart Management with localStorage
 */
// Prevent redeclaration
if (typeof window.Cart === 'undefined') {
    window.Cart = {
    /**
     * Get cart from localStorage
     */
    getCart: function() {
        const cart = localStorage.getItem('cart');
        return cart ? JSON.parse(cart) : [];
    },

    /**
     * Save cart to localStorage
     */
    saveCart: function(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
    },

    /**
     * Find item in cart by product id and variation id
     */
    findItem: function(productId, variationId) {
        const cart = this.getCart();
        return cart.find(item => 
            item.productId == productId && item.variationId == variationId
        );
    },

    /**
     * Add or update item in cart
     */
    addOrUpdate: function(itemData) {
        const cart = this.getCart();
        const { productId, variationId, quantity, productName, variationTagline, price } = itemData;

        // Find existing item
        const existingItem = this.findItem(productId, variationId);

        if (existingItem) {
            // Update existing item
            existingItem.quantity = parseInt(quantity);
            existingItem.price = parseFloat(price);
            existingItem.productName = productName;
            existingItem.variationTagline = variationTagline;
        } else {
            // Add new item
            cart.push({
                productId: parseInt(productId),
                variationId: parseInt(variationId),
                quantity: parseInt(quantity),
                productName: productName,
                variationTagline: variationTagline,
                price: parseFloat(price),
                addedAt: new Date().toISOString()
            });
        }

        this.saveCart(cart);
        this.updateCartCount();
        return cart;
    },

    /**
     * Remove item from cart
     */
    remove: function(productId, variationId) {
        const cart = this.getCart();
        const filteredCart = cart.filter(item => 
            !(item.productId == productId && item.variationId == variationId)
        );
        this.saveCart(filteredCart);
        this.updateCartCount();
        return filteredCart;
    },

    /**
     * Clear entire cart
     */
    clear: function() {
        localStorage.removeItem('cart');
        this.updateCartCount();
    },

    /**
     * Get total items count in cart
     */
    getTotalItems: function() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + item.quantity, 0);
    },

    /**
     * Get total price of cart
     */
    getTotalPrice: function() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    /**
     * Update cart count display (if cart count element exists)
     */
    updateCartCount: function() {
        const cartCount = this.getTotalItems();
        const cartCountElements = document.querySelectorAll('.cart-count, #cart-count');
        cartCountElements.forEach(element => {
            element.textContent = cartCount;
            element.style.display = cartCount > 0 ? 'inline' : 'none';
        });
    },

    /**
     * Initialize cart count on page load
     */
    init: function() {
        this.updateCartCount();
    }
    };

    // Initialize cart on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.Cart.init();
        });
    } else {
        window.Cart.init();
    }
}
