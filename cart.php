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
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
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
                <div class="cart-table">
                    <!-- Cart Header -->
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <h5 class="mb-0">Cart Items (3)</h5>
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-2"></i>Clear Cart
                        </button>
                    </div>

                    <!-- Cart Item 1 -->
                    <div class="cart-item p-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-md-2 col-3">
                                <img src="https://via.placeholder.com/100x80/f8f9fa/6c757d?text=Headphones" 
                                     class="img-fluid rounded" alt="Product">
                            </div>
                            <div class="col-md-4 col-9">
                                <h6 class="mb-1">Premium Wireless Headphones</h6>
                                <p class="text-muted small mb-1">by TechVendor</p>
                                <p class="text-muted small mb-0">Color: Black</p>
                                <div class="d-md-none mt-2">
                                    <span class="fw-bold text-primary">$89.99</span>
                                </div>
                            </div>
                            <div class="col-md-2 d-none d-md-block text-center">
                                <span class="fw-bold text-primary">$89.99</span>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="quantity-selector">
                                    <button type="button" onclick="updateQuantity('item1', -1)">-</button>
                                    <input type="number" id="item1-qty" value="2" min="1" readonly>
                                    <button type="button" onclick="updateQuantity('item1', 1)">+</button>
                                </div>
                            </div>
                            <div class="col-md-1 col-3 text-center">
                                <span class="fw-bold" id="item1-total">$179.98</span>
                            </div>
                            <div class="col-md-1 col-3 text-center">
                                <button class="btn btn-outline-danger btn-sm" onclick="removeItem('item1')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Item 2 -->
                    <div class="cart-item p-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-md-2 col-3">
                                <img src="https://via.placeholder.com/100x80/f8f9fa/6c757d?text=Smart+Watch" 
                                     class="img-fluid rounded" alt="Product">
                            </div>
                            <div class="col-md-4 col-9">
                                <h6 class="mb-1">Smart Fitness Watch</h6>
                                <p class="text-muted small mb-1">by GadgetStore</p>
                                <p class="text-muted small mb-0">Color: Silver</p>
                                <div class="d-md-none mt-2">
                                    <span class="fw-bold text-primary">$199.99</span>
                                </div>
                            </div>
                            <div class="col-md-2 d-none d-md-block text-center">
                                <span class="fw-bold text-primary">$199.99</span>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="quantity-selector">
                                    <button type="button" onclick="updateQuantity('item2', -1)">-</button>
                                    <input type="number" id="item2-qty" value="1" min="1" readonly>
                                    <button type="button" onclick="updateQuantity('item2', 1)">+</button>
                                </div>
                            </div>
                            <div class="col-md-1 col-3 text-center">
                                <span class="fw-bold" id="item2-total">$199.99</span>
                            </div>
                            <div class="col-md-1 col-3 text-center">
                                <button class="btn btn-outline-danger btn-sm" onclick="removeItem('item2')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Item 3 -->
                    <div class="cart-item p-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-md-2 col-3">
                                <img src="https://via.placeholder.com/100x80/f8f9fa/6c757d?text=Backpack" 
                                     class="img-fluid rounded" alt="Product">
                            </div>
                            <div class="col-md-4 col-9">
                                <h6 class="mb-1">Professional Laptop Backpack</h6>
                                <p class="text-muted small mb-1">by BagWorld</p>
                                <p class="text-muted small mb-0">Color: Navy Blue</p>
                                <div class="d-md-none mt-2">
                                    <span class="fw-bold text-primary">$49.99</span>
                                </div>
                            </div>
                            <div class="col-md-2 d-none d-md-block text-center">
                                <span class="fw-bold text-primary">$49.99</span>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="quantity-selector">
                                    <button type="button" onclick="updateQuantity('item3', -1)">-</button>
                                    <input type="number" id="item3-qty" value="1" min="1" readonly>
                                    <button type="button" onclick="updateQuantity('item3', 1)">+</button>
                                </div>
                            </div>
                            <div class="col-md-1 col-3 text-center">
                                <span class="fw-bold" id="item3-total">$49.99</span>
                            </div>
                            <div class="col-md-1 col-3 text-center">
                                <button class="btn btn-outline-danger btn-sm" onclick="removeItem('item3')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Continue Shopping -->
                    <div class="p-3">
                        <a href="shop.html" class="btn btn-outline-primary">
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
                            <button class="btn btn-outline-primary" type="button">Apply</button>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal (4 items):</span>
                            <span id="subtotal">$429.96</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span id="tax">$34.40</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount:</span>
                            <span>-$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary" id="total">$464.36</strong>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <div class="d-grid gap-2">
                        <a href="checkout.html" class="btn btn-primary btn-lg">
                            <i class="fas fa-lock me-2"></i>Proceed to Checkout
                        </a>
                        <button class="btn btn-outline-secondary">
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
                            Your order qualifies for free standard shipping (5-7 business days)
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
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card card h-100 shadow-sm">
                            <img src="https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Gaming+Mouse" class="card-img-top" alt="Product">
                            <div class="card-body">
                                <h6 class="card-title">RGB Gaming Mouse</h6>
                                <p class="text-muted small">GameGear</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 text-primary mb-0">$79.99</span>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card card h-100 shadow-sm">
                            <img src="https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Bluetooth+Speaker" class="card-img-top" alt="Product">
                            <div class="card-body">
                                <h6 class="card-title">Portable Bluetooth Speaker</h6>
                                <p class="text-muted small">AudioTech</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 text-primary mb-0">$129.99</span>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card card h-100 shadow-sm">
                            <img src="https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Wireless+Charger" class="card-img-top" alt="Product">
                            <div class="card-body">
                                <h6 class="card-title">Fast Wireless Charger</h6>
                                <p class="text-muted small">ChargeTech</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 text-primary mb-0">$39.99</span>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card card h-100 shadow-sm">
                            <img src="https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Phone+Case" class="card-img-top" alt="Product">
                            <div class="card-body">
                                <h6 class="card-title">Premium Phone Case</h6>
                                <p class="text-muted small">CaseWorld</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 text-primary mb-0">$24.99</span>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Product prices
        const prices = {
            'item1': 89.99,
            'item2': 199.99,
            'item3': 49.99
        };

        function updateQuantity(itemId, change) {
            const qtyInput = document.getElementById(itemId + '-qty');
            const totalSpan = document.getElementById(itemId + '-total');
            
            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + change;
            
            if (newQty >= 1) {
                qtyInput.value = newQty;
                const newTotal = (prices[itemId] * newQty).toFixed(2);
                totalSpan.textContent = '$' + newTotal;
                
                updateCartTotals();
            }
        }

        function removeItem(itemId) {
            const cartItem = document.querySelector(`#${itemId}-qty`).closest('.cart-item');
            cartItem.remove();
            updateCartTotals();
            
            // Update cart badge
            const cartBadge = document.querySelector('.badge');
            const currentCount = parseInt(cartBadge.textContent);
            cartBadge.textContent = currentCount - 1;
        }

        function updateCartTotals() {
            let subtotal = 0;
            
            // Calculate subtotal
            Object.keys(prices).forEach(itemId => {
                const qtyInput = document.getElementById(itemId + '-qty');
                if (qtyInput) {
                    const qty = parseInt(qtyInput.value);
                    subtotal += prices[itemId] * qty;
                }
            });
            
            const tax = subtotal * 0.08; // 8% tax
            const total = subtotal + tax;
            
            // Update display
            document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('tax').textContent = '$' + tax.toFixed(2);
            document.getElementById('total').textContent = '$' + total.toFixed(2);
        }
    </script>
</body>
</html>