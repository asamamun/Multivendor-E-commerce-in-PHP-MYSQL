<?php
session_start();
include_once "db/db.php";

// 1. Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php?msg=Please login to checkout");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$messageType = "";
$order_placed = false;
$placed_order_id = 0;

// 2. Handle Order Placement (Processing JSON from POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    
    // Decode Cart JSON
    $cart_data = json_decode($_POST['cart_data'], true);
    
    if (empty($cart_data)) {
        $message = "Your cart is missing or empty. Please try again.";
        $messageType = "danger";
    } else {
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $zip = trim($_POST['zip']);
        $payment_method = $_POST['payment_method'];
        $transaction_id = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : null;
        $notes = trim($_POST['notes']);

        // Validation
        if (empty($full_name) || empty($phone) || empty($address) || empty($city)) {
            $message = "Please fill in all required shipping fields.";
            $messageType = "danger";
        } elseif (($payment_method == 'bkash' || $payment_method == 'nagad' || $payment_method == 'rocket') && empty($transaction_id)) {
            $message = "Transaction ID is required for " . ucfirst($payment_method) . ".";
            $messageType = "danger";
        } else {
            // Calculate Totals Server Side for Security (Use Product IDs to fetch real prices)
            $product_ids = array_keys($cart_data);
            if (empty($product_ids)) {
                 $message = "Invalid cart data.";
                 $messageType = "danger";
            } else {
                $ids = implode(',', array_map('intval', $product_ids));
                // Fetch valid product info (price, vendor_id, updated stock)
                $sql = "SELECT id, vendor_id, price, name, stock_quantity FROM products WHERE id IN ($ids)";
                $result = $conn->query($sql);
                
                $order_items = [];
                $subtotal = 0;
                
                while($p = $result->fetch_assoc()) {
                    $pid = $p['id'];
                    // Get quantity from submitted cart data
                    if (isset($cart_data[$pid])) {
                        $qty = $cart_data[$pid]['quantity'];
                        $price = $p['price'];
                        $total = $price * $qty;
                        $subtotal += $total;
                        
                        $order_items[] = [
                            'product_id' => $pid,
                            'vendor_id' => $p['vendor_id'],
                            'name' => $p['name'],
                            'quantity' => $qty,
                            'price' => $price,
                            'total' => $total
                        ];
                    }
                }
                
                $shipping_address = json_encode([
                    'name' => $full_name,
                    'phone' => $phone,
                    'address' => $address,
                    'city' => $city,
                    'zip' => $zip
                ]);
                $billing_address = $shipping_address; 

                $shipping_cost = 60.00;
                $total_amount = $subtotal + $shipping_cost;
                
                $order_number = 'ORD-' . strtoupper(uniqid());
                $payment_status = 'pending';
                
                // Start Transaction
                $conn->begin_transaction();

                try {
                    // A. Insert Order
                    $order_sql = "INSERT INTO orders (order_number, customer_id, subtotal, shipping_cost, total_amount, payment_method, payment_status, shipping_address, billing_address, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($order_sql);
                    $stmt->bind_param("sidddsssss", $order_number, $user_id, $subtotal, $shipping_cost, $total_amount, $payment_method, $payment_status, $shipping_address, $billing_address, $notes);
                    $stmt->execute();
                    $order_id = $conn->insert_id;

                    // B. Insert Order Items
                    $item_sql = "INSERT INTO order_items (order_id, product_id, vendor_id, product_name, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $item_stmt = $conn->prepare($item_sql);

                    foreach ($order_items as $item) {
                        $item_stmt->bind_param("iiisidd", $order_id, $item['product_id'], $item['vendor_id'], $item['name'], $item['quantity'], $item['price'], $item['total']);
                        $item_stmt->execute();
                        
                        // Decrease stock
                        $conn->query("UPDATE products SET stock_quantity = stock_quantity - {$item['quantity']} WHERE id = {$item['product_id']}");
                    }

                    // C. Insert Payment Info
                    $pay_sql = "INSERT INTO payments (order_id, payment_method, transaction_id, amount, status) VALUES (?, ?, ?, ?, 'pending')";
                    $pay_stmt = $conn->prepare($pay_sql);
                    $pay_stmt->bind_param("issd", $order_id, $payment_method, $transaction_id, $total_amount);
                    $pay_stmt->execute();

                    // NOTE: Cart clearing will happen on client side via JS since it's LocalStorage

                    $conn->commit();
                    
                    $order_placed = true;
                    $placed_order_id = $order_id;
                    
                } catch (Exception $e) {
                    $conn->rollback();
                    $message = "Order failed: " . $e->getMessage();
                    $messageType = "danger";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - MarketPlace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/cart.js"></script>
</head>
<body class="bg-light">

    <?php include 'inc/navbar.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Checkout</h2>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($order_placed): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Wait for cart to be initialized
                    setTimeout(function() {
                        if(window.cart) {
                            window.cart.clearCart(); 
                            localStorage.removeItem('marketplace_cart'); // Force clean raw just in case
                        }
                        
                        Swal.fire({
                            title: 'Order Placed Successfully!',
                            text: 'Thank you for your order. Your Order ID is #<?php echo $placed_order_id; ?>',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Download Invoice',
                            cancelButtonText: 'Continue Shopping',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'invoice.php?id=<?php echo $placed_order_id; ?>';
                            } else {
                                window.location.href = 'index.php';
                            }
                        });
                    }, 500);
                });
            </script>
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                <h3>Order Placed!</h3>
                <p>Order #<?php echo $placed_order_id; ?></p>
                <div class="mt-4">
                    <a href="invoice.php?id=<?php echo $placed_order_id; ?>" class="btn btn-primary me-2"><i class="fas fa-file-invoice"></i> View Invoice</a>
                    <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <form action="" method="POST" id="checkout-form">
                <input type="hidden" name="cart_data" id="cart_data_input">
                
                <div class="row g-5">
                    <!-- Billing & Shipping Details -->
                    <div class="col-md-7 col-lg-8">
                        <h4 class="mb-3">Shipping Address</h4>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
                            </div>

                            <div class="col-12">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" required placeholder="017...">
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" required placeholder="1234 Main St">
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>

                            <div class="col-md-6">
                                <label for="zip" class="form-label">Zip / Postal Code</label>
                                <input type="text" class="form-control" id="zip" name="zip">
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h4 class="mb-3">Payment Method</h4>

                        <div class="my-3">
                            <div class="form-check">
                                <input id="bkash" name="payment_method" type="radio" class="form-check-input" value="bkash" required onclick="toggleTransactionId(true)">
                                <label class="form-check-label" for="bkash">bKash <span class="badge bg-danger">Send Money</span></label>
                            </div>
                            <div class="form-check">
                                <input id="nagad" name="payment_method" type="radio" class="form-check-input" value="nagad" required onclick="toggleTransactionId(true)">
                                <label class="form-check-label" for="nagad">Nagad <span class="badge bg-warning text-dark">Send Money</span></label>
                            </div>
                            <div class="form-check">
                                <input id="rocket" name="payment_method" type="radio" class="form-check-input" value="rocket" required onclick="toggleTransactionId(true)">
                                <label class="form-check-label" for="rocket">Rocket <span class="badge bg-primary">Send Money</span></label>
                            </div>
                            <div class="form-check">
                                <input id="cod" name="payment_method" type="radio" class="form-check-input" value="cod" required onclick="toggleTransactionId(false)">
                                <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
                            </div>
                        </div>

                        <div id="transaction-id-section" class="mb-3" style="display:none;">
                            <label for="transaction_id" class="form-label">Transaction ID / TrxID</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="e.g. 8N7A6D5...">
                            <small class="text-muted">Please complete the payment to our merchant number <strong>017XXXXXXXX</strong> and enter the TrxID here.</small>
                        </div>
                    </div>

                    <!-- Order Summary (populated via JS) -->
                    <div class="col-md-5 col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0 fs-5"><i class="fas fa-shopping-cart me-2"></i>Order Summary</h4>
                            </div>
                            <ul class="list-group list-group-flush" id="checkout-summary-list">
                                <!-- JS will populate items here -->
                                <li class="list-group-item text-center">Loading cart...</li>
                            </ul>
                            <div class="card-footer">
                                <button class="btn btn-primary w-100 btn-lg" type="submit" id="place-order-btn" disabled>Place Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for cart to be initialized
            setTimeout(initCheckout, 500); 
        });

        function initCheckout() {
            if (!window.cart) return;
            
            const items = window.cart.getCartItems();
            
            // Redirect if empty
            if (items.length === 0) {
                window.location.href = 'shop.php?msg=Your cart is empty';
                return;
            }

            // Populate Summary
            const listContainer = document.getElementById('checkout-summary-list');
            listContainer.innerHTML = '';
            
            let subtotal = 0;
            
            items.forEach(item => {
                const total = item.price * item.quantity;
                subtotal += total;
                
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between lh-sm';
                li.innerHTML = `
                    <div class="d-flex align-items-center">
                        <img src="${item.image}" alt="product" width="40" class="me-2 rounded" onerror="this.src='https://via.placeholder.com/40'">
                        <div>
                            <h6 class="my-0 text-truncate" style="max-width: 150px;">${item.name}</h6>
                            <small class="text-muted">Qty: ${item.quantity}</small>
                        </div>
                    </div>
                    <span class="text-muted">৳${total.toFixed(2)}</span>
                `;
                listContainer.appendChild(li);
            });
            
            const shipping = 60;
            const total = subtotal + shipping;

            // Summary Totals
            listContainer.innerHTML += `
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <span>Subtotal</span>
                    <strong>৳${subtotal.toFixed(2)}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <span>Shipping (Flat Rate)</span>
                    <strong>৳${shipping.toFixed(2)}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between active text-white">
                    <span>Total (BDT)</span>
                    <strong>৳${total.toFixed(2)}</strong>
                </li>
            `;

            // Prepare Input Data for form submission
            // We send the cart JSON string to PHP to process
            // Note: In a real app, you should rely on DB cart for pricing security, 
            // but we'll validate prices on server against DB using IDs from this JSON.
            const cartData = {};
            items.forEach(item => {
                cartData[item.id] = {
                    quantity: item.quantity,
                    // We don't trust client price, we just need ID and Qty
                };
            });
            
            document.getElementById('cart_data_input').value = JSON.stringify(cartData);
            document.getElementById('place-order-btn').disabled = false;
        }

        function toggleTransactionId(show) {
            const el = document.getElementById('transaction-id-section');
            const input = document.getElementById('transaction_id');
            if (show) {
                el.style.display = 'block';
                input.required = true;
            } else {
                el.style.display = 'none';
                input.required = false;
                input.value = '';
            }
        }
    </script>
</body>
</html>
