/**
 * Cart Initialization Script
 * Include this after cart.js to initialize the cart
 */
(function() {
    'use strict';
    
    // Function to initialize cart
    function initializeCart() {
        if (typeof window.ShoppingCart !== 'undefined' && typeof window.cart === 'undefined') {
            window.cart = new window.ShoppingCart();
            console.log('Cart initialized successfully');
        }
    }
    
    // Initialize immediately if DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCart);
    } else {
        initializeCart();
    }
})();