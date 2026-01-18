<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - MarketPlace</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .cart-item {
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background-color: #f8f9fa;
        }
        
        .quantity-selector {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-selector button {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }
        
        .cart-summary {
            background: #f8f9fa;
            border-radius: 10px;
            position: sticky;
            top: 100px;
        }
        
        .empty-cart {
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-card {
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'inc/navbar.php'; ?>

    <!-- Cart Content -->
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i>Shopping Cart
                </h2>
            </div>
        </div>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="cart-table bg-white rounded shadow-sm">
                    <!-- Cart Header -->
                    <div class="cart-header d-flex justify-content-between align-items-center p-3 border-bottom">
                        <h5 class="mb-0">Cart Items (0)</h5>
                        <button class="btn btn-outline-danger btn-sm" onclick="cart.clearCart()">
                            <i class="fas fa-trash me-2"></i>Clear Cart
                        </button>
                    </div>

                    <!-- Cart Items Container -->
                    <div class="cart-items-container">
                        <div class="cart-items">
                            <!-- Cart items will be loaded here by JavaScript -->
                        </div>
                    </div>

                    <!-- Continue Shopping -->
                    <div class="p-3 border-top">
                        <a href="shop.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="cart-summary p-4">
                    <h5 class="mb-4">Order Summary</h5>
                    
                    <!-- Promo Code -->
                    <div class="mb-4">
                        <label for="promoCode" class="form-label">Promo Code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="promoCode" placeholder="Enter code">
                            <button class="btn btn-outline-primary" type="button" onclick="applyPromoCode()">Apply</button>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal <span class="item-count">(0 items)</span>:</span>
                            <span id="cart-subtotal">৳0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span id="cart-shipping" class="text-success">Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (8%):</span>
                            <span id="cart-tax">৳0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount:</span>
                            <span id="cart-discount">-৳0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary" id="cart-total">৳0.00</strong>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg" onclick="proceedToCheckout()">
                            <i class="fas fa-lock me-2"></i>Proceed to Checkout
                        </button>
                        <button class="btn btn-outline-secondary" onclick="payWithPayPal()">
                            <i class="fab fa-paypal me-2"></i>PayPal Express
                        </button>
                    </div>

                    <!-- Security Info -->
                    <div class="mt-4 text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure checkout with SSL encryption
                        </small>
                    </div>

                    <!-- Shipping Info -->
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-truck text-success me-2"></i>Free Shipping
                        </h6>
                        <small class="text-muted">
                            Free shipping on orders over ৳500 (5-7 business days)
                        </small>
                    </div>

                    <!-- Return Policy -->
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2">
                            <i class="fas fa-undo text-info me-2"></i>Easy Returns
                        </h6>
                        <small class="text-muted">
                            30-day return policy on all items
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Products -->
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-4">You might also like</h4>
                <div class="row g-4" id="recommended-products">
                    <!-- Recommended products will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Cart JS -->
    <script src="assets/js/cart.js"></script>
    <script src="assets/js/cart-init.js"></script>
    
    <script>
        // Load recommended products
        function loadRecommendedProducts() {
            // This would typically fetch from your database
            // For now, using static data
            const recommendedProducts = [
                {
                    id: 'rec1',
                    name: 'RGB Gaming Mouse',
                    vendor: 'GameGear',
                    price: 79.99,
                    image: 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Gaming+Mouse'
                },
                {
                    id: 'rec2',
                    name: 'Portable Bluetooth Speaker',
                    vendor: 'AudioTech',
                    price: 129.99,
                    image: 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Bluetooth+Speaker'
                },
                {
                    id: 'rec3',
                    name: 'Fast Wireless Charger',
                    vendor: 'ChargeTech',
                    price: 39.99,
                    image: 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Wireless+Charger'
                },
                {
                    id: 'rec4',
                    name: 'Premium Phone Case',
                    vendor: 'CaseWorld',
                    price: 24.99,
                    image: 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Phone+Case'
                }
            ];

            const container = document.getElementById('recommended-products');
            container.innerHTML = recommendedProducts.map(product => `
                <div class="col-lg-3 col-md-6">
                    <div class="product-card card h-100 shadow-sm">
                        <img src="${product.image}" class="card-img-top" alt="${product.name}">
                        <div class="card-body">
                            <h6 class="card-title">${product.name}</h6>
                            <p class="text-muted small">${product.vendor}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 text-primary mb-0">৳${product.price}</span>
                                <button class="btn btn-primary btn-sm" 
                                        onclick="addToCart('${product.id}', ${JSON.stringify(product).replace(/"/g, '&quot;')})">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Apply promo code
        function applyPromoCode() {
            const promoCode = document.getElementById('promoCode').value.trim();
            
            if (!promoCode) {
                Swal.fire('Error', 'Please enter a promo code', 'error');
                return;
            }

            // Simulate promo code validation
            const validCodes = {
                'SAVE10': 10,
                'WELCOME5': 5,
                'NEWUSER': 15
            };

            if (validCodes[promoCode.toUpperCase()]) {
                const discount = validCodes[promoCode.toUpperCase()];
                Swal.fire('Success!', `Promo code applied! You saved ${discount}%`, 'success');
                
                // Update discount in UI
                const subtotal = cart.getCartTotal();
                const discountAmount = subtotal * (discount / 100);
                document.getElementById('cart-discount').textContent = `-৳${discountAmount.toFixed(2)}`;
                
                // Recalculate total
                cart.updateCartTotals();
            } else {
                Swal.fire('Invalid Code', 'The promo code you entered is not valid', 'error');
            }
        }

        // Proceed to checkout
        function proceedToCheckout() {
            if (cart.getItemCount() === 0) {
                Swal.fire('Empty Cart', 'Please add some items to your cart before checkout', 'warning');
                return;
            }

            // Check if user is logged in (you can implement this check)
            Swal.fire({
                title: 'Proceed to Checkout?',
                text: 'You will be redirected to the checkout page',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to checkout page
                    window.location.href = 'checkout.php';
                }
            });
        }

        // PayPal payment
        function payWithPayPal() {
            if (cart.getItemCount() === 0) {
                Swal.fire('Empty Cart', 'Please add some items to your cart before payment', 'warning');
                return;
            }

            Swal.fire('PayPal', 'PayPal integration coming soon!', 'info');
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadRecommendedProducts();
        });
    </script>
</body>
</html>