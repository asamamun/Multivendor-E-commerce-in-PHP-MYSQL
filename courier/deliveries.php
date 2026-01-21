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

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $delivery_id = $_POST['delivery_id'];
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE deliveries SET status = ? WHERE id = ? AND courier_id = ?");
    $stmt->bind_param("sii", $new_status, $delivery_id, $courier_id);
    
    if ($stmt->execute()) {
        $message = "Delivery status updated to " . ucfirst($new_status);
        $messageType = "success";
        
        // If delivered, update order status as well
        if ($new_status == 'delivered') {
            $stmt_order = $conn->prepare("UPDATE orders SET order_status = 'delivered', payment_status = 'paid' WHERE id = (SELECT order_id FROM deliveries WHERE id = ?)");
            $stmt_order->bind_param("i", $delivery_id);
            $stmt_order->execute();
            $stmt_order->close();
        }
    } else {
        $message = "Error updating status: " . $conn->error;
        $messageType = "danger";
    }
    $stmt->close();
}

// Fetch assigned deliveries
$query = "SELECT d.*, o.order_number, o.total_amount, o.payment_method, o.shipping_address 
          FROM deliveries d 
          JOIN orders o ON d.order_id = o.id 
          WHERE d.courier_id = ? 
          ORDER BY d.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $courier_id);
$stmt->execute();
$deliveries = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Deliveries - Courier Dashboard</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .page-header { background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); color: white; padding: 2rem 0; margin-bottom: 2rem; }
        .delivery-card { border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1); border: none; margin-bottom: 1.5rem; transition: transform 0.2s; }
        .delivery-card:hover { transform: scale(1.01); }
        .status-badge { font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 20px; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-8">
                    <h1><i class="fas fa-truck-loading me-3"></i>My Deliveries</h1>
                    <p class="mb-0">Track and manage your assigned deliveries</p>
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

        <?php if ($deliveries->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $deliveries->fetch_assoc()): ?>
                    <?php 
                        $address = json_decode($row['shipping_address'], true);
                        $status_class = [
                            'assigned' => 'secondary',
                            'picked_up' => 'info',
                            'in_transit' => 'primary',
                            'delivered' => 'success',
                            'failed' => 'danger',
                            'returned' => 'warning'
                        ][$row['status']] ?? 'secondary';
                    ?>
                    <div class="col-md-6">
                        <div class="card delivery-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0 text-primary">Order #<?php echo $row['order_number']; ?></h5>
                                    <span class="badge bg-<?php echo $status_class; ?> status-badge">
                                        <?php echo str_replace('_', ' ', ucfirst($row['status'])); ?>
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small"><i class="fas fa-map-marker-alt me-2"></i>Delivery Address:</p>
                                    <p class="mb-0 fw-500">
                                        <?php echo htmlspecialchars($address['name'] ?? ''); ?><br>
                                        <?php echo htmlspecialchars($address['address'] ?? ''); ?>, 
                                        <?php echo htmlspecialchars($address['city'] ?? ''); ?> 
                                        <?php echo htmlspecialchars($address['zip'] ?? ''); ?><br>
                                        Phone: <?php echo htmlspecialchars($address['phone'] ?? ''); ?>
                                    </p>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="mb-0 text-muted small">Amount to Collect:</p>
                                        <h6 class="mb-0">৳<?php echo number_format($row['total_amount'], 2); ?></h6>
                                        <small class="text-<?php echo $row['payment_method'] == 'cod' ? 'danger' : 'success'; ?>">
                                            <?php echo strtoupper($row['payment_method']); ?>
                                        </small>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-0 text-muted small">Delivery Fee:</p>
                                        <h6 class="mb-0">৳<?php echo number_format($row['delivery_cost'], 2); ?></h6>
                                    </div>
                                </div>

                                <?php if ($row['status'] != 'delivered' && $row['status'] != 'returned'): ?>
                                    <form method="POST" class="mt-3 pt-3 border-top">
                                        <input type="hidden" name="delivery_id" value="<?php echo $row['id']; ?>">
                                        <div class="input-group">
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="picked_up" <?php echo $row['status'] == 'picked_up' ? 'selected' : ''; ?>>Picked Up</option>
                                                <option value="in_transit" <?php echo $row['status'] == 'in_transit' ? 'selected' : ''; ?>>In Transit</option>
                                                <option value="delivered">Delivered</option>
                                                <option value="failed">Failed</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-success btn-sm">Update</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4>No deliveries assigned yet.</h4>
                <a href="available-orders.php" class="btn btn-primary mt-3">Browse Available Orders</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
