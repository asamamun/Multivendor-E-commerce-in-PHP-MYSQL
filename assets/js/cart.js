/**
 * Shopping Cart Class with LocalStorage Support
 * Handles cart operations, notifications, and UI updates
 */
if (typeof window.ShoppingCart === 'undefined') {
    window.ShoppingCart = class ShoppingCart {
        constructor() {
            this.storageKey = 'marketplace_cart';
            this.cart = this.loadCart();
            this.init();
        }

        /**
         * Initialize cart functionality
         */
        init() {
            this.updateCartBadge();
            this.bindEvents();
            
            // Initialize SweetAlert2 if not already loaded
            if (typeof Swal === 'undefined') {
                this.loadSweetAlert();
            }
        }

        /**
         * Load SweetAlert2 dynamically
         */
        loadSweetAlert() {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(script);
            
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css';
            document.head.appendChild(link);
        }

        /**
         * Bind event listeners
         */
        bindEvents() {
            // Listen for storage changes from other tabs
            window.addEventListener('storage', (e) => {
                if (e.key === this.storageKey) {
                    this.cart = this.loadCart();
                    this.updateCartBadge();
                    this.refreshCartPage();
                }
            });
        }

        /**
         * Load cart from localStorage
         */
        loadCart() {
            try {
                const cartData = localStorage.getItem(this.storageKey);
                return cartData ? JSON.parse(cartData) : {};
            } catch (error) {
                console.error('Error loading cart:', error);
                return {};
            }
        }

        /**
         * Save cart to localStorage
         */
        saveCart() {
            try {
                localStorage.setItem(this.storageKey, JSON.stringify(this.cart));
                this.updateCartBadge();
                
                // Trigger storage event for other tabs
                window.dispatchEvent(new StorageEvent('storage', {
                    key: this.storageKey,
                    newValue: JSON.stringify(this.cart)
                }));
            } catch (error) {
                console.error('Error saving cart:', error);
            }
        }

        /**
         * Add product to cart
         */
        addToCart(productId, productData, quantity = 1) {
            const id = String(productId);
            
            // Validate productData
            if (!productData || !productData.name || !productData.price) {
                console.error('Invalid product data:', productData);
                return false;
            }
            
            const maxStock = productData.stock !== undefined ? parseInt(productData.stock) : 999999;
            let currentQty = this.cart[id] ? this.cart[id].quantity : 0;
            
            if (currentQty + quantity > maxStock) {
                this.showNotification('error', 'Out of Stock', `Only ${maxStock} items available in stock.`);
                return false;
            }
            
            if (this.cart[id]) {
                this.cart[id].quantity += quantity;
                // Update stock info just in case it changed (though usually comes from fresh add)
                this.cart[id].stock = maxStock;
            } else {
                this.cart[id] = {
                    id: id,
                    name: productData.name,
                    price: parseFloat(productData.price) || 0,
                    image: productData.image || 'https://via.placeholder.com/100x80/f8f9fa/6c757d?text=No+Image',
                    vendor: productData.vendor || 'Unknown Vendor',
                    quantity: quantity,
                    stock: maxStock,
                    addedAt: new Date().toISOString()
                };
            }
            
            this.saveCart();
            this.showNotification('success', 'Added to Cart!', `${productData.name} has been added to your cart.`);
            return true;
        }

        /**
         * Remove product from cart
         */
        removeFromCart(productId) {
            const id = String(productId);
            
            if (this.cart[id]) {
                const productName = this.cart[id].name;
                delete this.cart[id];
                this.saveCart();
                this.showNotification('success', 'Removed!', `${productName} has been removed from your cart.`);
                this.refreshCartPage();
                return true;
            }
            return false;
        }

        /**
         * Update product quantity
         */
        updateQuantity(productId, quantity) {
            const id = String(productId);
            quantity = parseInt(quantity);
            
            if (quantity <= 0) {
                return this.removeFromCart(id);
            }
            
            if (this.cart[id]) {
                const maxStock = this.cart[id].stock !== undefined ? parseInt(this.cart[id].stock) : 999999;
                
                if (quantity > maxStock) {
                    this.showNotification('error', 'Limit Reached', `Only ${maxStock} items available in stock.`);
                    // Reset input value if possible, or just don't update
                    // We need to trigger a refresh to reset the UI input
                    this.refreshCartPage();
                    return false;
                }
                
                this.cart[id].quantity = quantity;
                this.saveCart();
                this.refreshCartPage();
                return true;
            }
            return false;
        }

        /**
         * Get cart item count
         */
        getItemCount() {
            return Object.values(this.cart).reduce((total, item) => total + item.quantity, 0);
        }

        /**
         * Get cart total
         */
        getCartTotal() {
            return Object.values(this.cart).reduce((total, item) => {
                const price = parseFloat(item.price) || 0;
                const quantity = parseInt(item.quantity) || 0;
                return total + (price * quantity);
            }, 0);
        }

        /**
         * Clear entire cart
         */
        clearCart() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Clear Cart?',
                    text: 'Are you sure you want to remove all items from your cart?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, clear it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.cart = {};
                        this.saveCart();
                        this.showNotification('success', 'Cart Cleared!', 'All items have been removed from your cart.');
                        this.refreshCartPage();
                    }
                });
            } else {
                if (confirm('Are you sure you want to clear your cart?')) {
                    this.cart = {};
                    this.saveCart();
                    this.refreshCartPage();
                }
            }
        }

        /**
         * Update cart badge in navbar
         */
        updateCartBadge() {
            const badge = document.querySelector('.navbar .badge, .cart-badge');
            if (badge) {
                const count = this.getItemCount();
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline' : 'none';
            }
        }

        /**
         * Show notification using SweetAlert2
         */
        showNotification(type, title, text) {
            // Wait for SweetAlert2 to load if not available
            const showAlert = () => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: title,
                        text: text,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    // Fallback to browser alert
                    alert(`${title}: ${text}`);
                }
            };

            if (typeof Swal === 'undefined') {
                setTimeout(showAlert, 500); // Wait for SweetAlert2 to load
            } else {
                showAlert();
            }
        }

        /**
         * Get all cart items
         */
        getCartItems() {
            return Object.values(this.cart);
        }

        /**
         * Check if product is in cart
         */
        isInCart(productId) {
            return String(productId) in this.cart;
        }

        /**
         * Get specific cart item
         */
        getCartItem(productId) {
            return this.cart[String(productId)] || null;
        }

        /**
         * Refresh cart page if currently viewing it
         */
        refreshCartPage() {
            if (window.location.pathname.includes('cart.php')) {
                this.renderCartPage();
            }
        }

        /**
         * Render cart page content
         */
        renderCartPage() {
            const cartContainer = document.querySelector('.cart-items-container');
            const cartSummary = document.querySelector('.cart-summary');
            
            if (!cartContainer) return;

            const items = this.getCartItems();
            
            if (items.length === 0) {
                cartContainer.innerHTML = this.getEmptyCartHTML();
                if (cartSummary) {
                    cartSummary.style.display = 'none';
                }
                return;
            }

            if (cartSummary) {
                cartSummary.style.display = 'block';
            }

            // Update cart header
            const cartHeader = document.querySelector('.cart-header h5');
            if (cartHeader) {
                cartHeader.textContent = `Cart Items (${this.getItemCount()})`;
            }

            // Render cart items
            const cartItemsHTML = items.map(item => this.getCartItemHTML(item)).join('');
            const cartItemsContainer = document.querySelector('.cart-items');
            if (cartItemsContainer) {
                cartItemsContainer.innerHTML = cartItemsHTML;
            }

            // Update totals
            this.updateCartTotals();
        }

        /**
         * Get HTML for empty cart
         */
        getEmptyCartHTML() {
            return `
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted mb-3">Your cart is empty</h4>
                    <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            `;
        }

        /**
         * Get HTML for cart item
         */
        getCartItemHTML(item) {
            const price = parseFloat(item.price) || 0;
            const quantity = parseInt(item.quantity) || 0;
            const total = price * quantity;
            
            return `
                <div class="cart-item p-3 border-bottom" data-product-id="${item.id}">
                    <div class="row align-items-center">
                        <div class="col-md-2 col-3">
                            <img src="${item.image}" class="img-fluid rounded" alt="${item.name}" 
                                 onerror="this.src='https://via.placeholder.com/100x80/f8f9fa/6c757d?text=No+Image'">
                        </div>
                        <div class="col-md-4 col-9">
                            <h6 class="mb-1">${item.name}</h6>
                            <p class="text-muted small mb-1">by ${item.vendor}</p>
                            <div class="d-md-none mt-2">
                                <span class="fw-bold text-primary">৳${price.toFixed(2)}</span>
                            </div>
                        </div>
                        <div class="col-md-2 d-none d-md-block text-center">
                            <span class="fw-bold text-primary">৳${price.toFixed(2)}</span>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="quantity-selector">
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="cart.updateQuantity('${item.id}', ${quantity - 1})">-</button>
                                <input type="number" class="form-control text-center mx-1" 
                                       value="${quantity}" min="1" style="width: 60px;"
                                       onchange="cart.updateQuantity('${item.id}', this.value)">
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="cart.updateQuantity('${item.id}', ${quantity + 1})">+</button>
                            </div>
                        </div>
                        <div class="col-md-1 col-3 text-center">
                            <span class="fw-bold">৳${total.toFixed(2)}</span>
                        </div>
                        <div class="col-md-1 col-3 text-center">
                            <button class="btn btn-outline-danger btn-sm" 
                                    onclick="cart.removeFromCart('${item.id}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        /**
         * Update cart totals in summary
         */
        updateCartTotals() {
            const subtotal = this.getCartTotal();
            const tax = subtotal * 0.08; // 8% tax
            const shipping = subtotal > 500 ? 0 : 50; // Free shipping over ৳500
            const total = subtotal + tax + shipping;
            const itemCount = this.getItemCount();

            // Update subtotal
            const subtotalElement = document.getElementById('cart-subtotal');
            if (subtotalElement) {
                subtotalElement.textContent = `৳${subtotal.toFixed(2)}`;
            }

            // Update item count in summary
            const itemCountElement = document.querySelector('.cart-summary .item-count');
            if (itemCountElement) {
                itemCountElement.textContent = `(${itemCount} items)`;
            }

            // Update tax
            const taxElement = document.getElementById('cart-tax');
            if (taxElement) {
                taxElement.textContent = `৳${tax.toFixed(2)}`;
            }

            // Update shipping
            const shippingElement = document.getElementById('cart-shipping');
            if (shippingElement) {
                shippingElement.textContent = shipping === 0 ? 'Free' : `৳${shipping.toFixed(2)}`;
                shippingElement.className = shipping === 0 ? 'text-success' : '';
            }

            // Update total
            const totalElement = document.getElementById('cart-total');
            if (totalElement) {
                totalElement.textContent = `৳${total.toFixed(2)}`;
            }
        }
    };
}

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.cart === 'undefined') {
        window.cart = new window.ShoppingCart();
        
        // If we're on the cart page, render it
        if (window.location.pathname.includes('cart.php')) {
            cart.renderCartPage();
        }
    }
});

// Global functions for backward compatibility
function addToCart(productId, productData = {}) {
    if (window.cart) {
        return window.cart.addToCart(productId, productData);
    }
}

function removeFromCart(productId) {
    if (window.cart) {
        return window.cart.removeFromCart(productId);
    }
}

function updateQuantity(productId, quantity) {
    if (window.cart) {
        return window.cart.updateQuantity(productId, quantity);
    }
}

function clearCart() {
    if (window.cart) {
        return window.cart.clearCart();
    }
}