<?php
session_start();

// Check if user is logged in and is a courier
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'courier') {
    header("Location: ../login.php");
    exit;
}

require "../db/db.php";

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Fetch courier ID
$stmt = $conn->prepare("SELECT id FROM couriers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$courier_id = $stmt->get_result()->fetch_assoc()['id'] ?? null;
$stmt->close();

if (!$courier_id) {
    header("Location: profile.php");
    exit;
}

// Handle order pick-up
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pickup_order'])) {
    $order_id = $_POST['order_id'];
    $delivery_cost = 60.00; // Fixed delivery cost for simplicity
    
    // Check if delivery entry already exists
    $check_stmt = $conn->prepare("SELECT id FROM deliveries WHERE order_id = ?");
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $existing_delivery = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($existing_delivery) {
        $stmt = $conn->prepare("UPDATE deliveries SET courier_id = ?, status = 'assigned' WHERE order_id = ? AND courier_id IS NULL");
        $stmt->bind_param("ii", $courier_id, $order_id);
    } else {
        // Create new delivery record
        $stmt = $conn->prepare("INSERT INTO deliveries (order_id, courier_id, status, delivery_cost, pickup_address, delivery_address) 
                               VALUES (?, ?, 'assigned', ?, '[]', (SELECT shipping_address FROM orders WHERE id = ?))");
        $stmt->bind_param("iidi", $order_id, $courier_id, $delivery_cost, $order_id);
    }
    
    if ($stmt->execute()) {
        $message = "Order successfully assigned to you!";
        $messageType = "success";
        
        // Update order status explicitly if needed
        $update_order = $conn->prepare("UPDATE orders SET order_status = 'shipped' WHERE id = ?");
        $update_order->bind_param("i", $order_id);
        $update_order->execute();
        $update_order->close();
    } else {
        $message = "Error picking up order: " . $conn->error;
        $messageType = "danger";
    }
    $stmt->close();
}

// Fetch available orders
// Available orders are those that are 'processing' or 'confirmed' and have no courier assigned
$query = "SELECT o.* FROM orders o 
          LEFT JOIN deliveries d ON o.id = d.order_id 
          WHERE (d.courier_id IS NULL) 
          AND o.order_status IN ('processing', 'confirmed')
          ORDER BY o.created_at DESC";
$available_orders = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Orders - Courier Dashboard</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .page-header { background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); color: white; padding: 2rem 0; margin-bottom: 2rem; }
        .order-card { border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1); border: none; margin-bottom: 1.5rem; transition: transform 0.2s; }
        .order-card:hover { transform: scale(1.01); }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-8">
                    <h1><i class="fas fa-search-location me-3"></i>Available Orders</h1>
                    <p class="mb-0">Find new delivery opportunities in your area</p>
                </div>
                <div class="col-4 text-end">
                    <a href="dashboard.php" class="btn btn-light"><i class="fas fa-arrow-left me-2"></i>Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($available_orders->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $available_orders->fetch_assoc()): ?>
                    <?php 
                        $address = json_decode($row['shipping_address'], true);
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card order-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0 text-primary">Order #<?php echo $row['order_number']; ?></h5>
                                    <span class="badge bg-info">Ready for Pickup</span>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small"><i class="fas fa-map-marker-alt me-2"></i>Delivery Location:</p>
                                    <p class="mb-0">
                                        <strong><?php echo htmlspecialchars($address['city'] ?? 'N/A'); ?></strong>, <?php echo htmlspecialchars($address['zip'] ?? ''); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($address['address'] ?? ''); ?></small>
                                    </p>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="mb-0 text-muted small">Order Total:</p>
                                        <h6 class="mb-0">৳<?php echo number_format($row['total_amount'], 2); ?></h6>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-0 text-muted small">Payment Type:</p>
                                        <span class="badge <?php echo $row['payment_method'] == 'cod' ? 'bg-danger' : 'bg-success'; ?>">
                                            <?php echo strtoupper($row['payment_method']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-grid mt-3">
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="pickup_order" class="btn btn-primary w-100">
                                            <i class="fas fa-hand-holding-box me-2"></i>Accept Delivery
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-hourglass-half fa-3x text-muted mb-3"></i>
                <h4>No orders available for delivery at the moment.</h4>
                <p class="text-muted">Check back later or refresh the page.</p>
                <button onclick="location.reload()" class="btn btn-outline-primary mt-2">Refresh Page</button>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
