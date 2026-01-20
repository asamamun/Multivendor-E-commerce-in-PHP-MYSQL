<?php
session_start();
include_once "db/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login to view your orders");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$messageType = "";

// Handle Order Cancellation
if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['id'])) {
    $cancel_id = (int)$_GET['id'];
    
    // Validate that the order belongs to the user and is pending
    $check_sql = "SELECT id, payment_status FROM orders WHERE id = ? AND customer_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $cancel_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        if ($order['payment_status'] == 'pending') {
            // Proceed to cancel
            // Assuming 'cancelled' is a valid status. If 'payment_status' captures order status too.
            // If there's a separate 'status' column for order flow vs payment, we might need to check.
            // Based on checkout.php, only 'payment_status' (enum likely or varchar) is used for status tracking?
            // checkout.php uses `payment_status` = 'pending'. Let's assume this column tracks the main status.
            
            $update_sql = "UPDATE orders SET payment_status = 'cancelled' WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $cancel_id);
            
            if ($update_stmt->execute()) {
                $message = "Order #$cancel_id has been cancelled successfully.";
                $messageType = "success";
            } else {
                $message = "Error cancelling order.";
                $messageType = "danger";
            }
        } else {
            $message = "You can only cancel pending orders.";
            $messageType = "warning";
        }
    } else {
        $message = "Order not found or access denied.";
        $messageType = "danger";
    }
}

// Pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Count Total Orders
$count_sql = "SELECT COUNT(*) as total FROM orders WHERE customer_id = ?";
$stmt = $conn->prepare($count_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_rows = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch Orders
$sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $offset, $limit);
$stmt->execute();
$orders_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - MarketPlace</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .status-badge {
            width: 100px;
            display: inline-block;
            text-align: center;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <?php include 'inc/navbar.php'; ?>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-9">
                <h2 class="mb-0"><i class="fas fa-shopping-bag text-primary me-2"></i>My Orders</h2>
                <p class="text-muted">Track and manage your recent purchases</p>
            </div>
            <div class="col-md-3 text-md-end align-self-center">
                <a href="shop.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Back to Shop</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show shadow-sm" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <?php if ($orders_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Order #</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Total</th>
                                    <th class="py-3">Payment</th>
                                    <th class="py-3">Status</th>
                                    <th class="text-end pe-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = $orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">
                                            #<?php echo $order['order_number'] ?? $order['id']; ?>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?>
                                        </td>
                                        <td class="fw-bold">
                                            ৳<?php echo number_format($order['total_amount'], 2); ?>
                                        </td>
                                        <td>
                                            <span class="text-uppercase small fw-semibold text-muted">
                                                <?php echo htmlspecialchars($order['payment_method']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                                $status = strtolower($order['payment_status']);
                                                $badgeClass = 'bg-secondary';
                                                
                                                if ($status == 'completed' || $status == 'paid') $badgeClass = 'bg-success';
                                                elseif ($status == 'pending') $badgeClass = 'bg-warning text-dark';
                                                elseif ($status == 'processing') $badgeClass = 'bg-info text-dark';
                                                elseif ($status == 'cancelled' || $status == 'failed') $badgeClass = 'bg-danger';
                                                elseif ($status == 'shipped') $badgeClass = 'bg-primary';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?> rounded-pill status-badge">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <div class="collapse" id="details-<?php echo $order['id']; ?>">
                                           <!-- Ideally we could load order items via AJAX or present a mini table here -->
                                        </div>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <a href="invoice.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View Invoice">
                                                    <i class="fas fa-file-invoice"></i> Invoice
                                                </a>
                                                <?php if ($status == 'pending'): ?>
                                                    <a href="orders.php?action=cancel&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-danger" title="Cancel Order" onclick="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                                                        <i class="fas fa-times-circle"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-box-open fa-3x text-muted"></i>
                        </div>
                        <h4>No Orders Found</h4>
                        <p class="text-muted">Looks like you haven't placed any orders yet.</p>
                        <a href="shop.php" class="btn btn-primary mt-2">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="card-footer bg-white py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <!-- Pagination Logic similar to other pages -->
                            <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                                <a class="page-link" href="<?php echo ($page > 1) ? '?page='.($page-1) : '#'; ?>">Previous</a>
                            </li>
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
                                <a class="page-link" href="<?php echo ($page < $total_pages) ? '?page='.($page+1) : '#'; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'inc/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
