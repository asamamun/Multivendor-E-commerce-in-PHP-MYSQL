<?php
$page = 'All Orders';
$description = 'View and manage customer orders';
$author = 'ASA Al-Mamun';
$title = 'All Orders';
include "inc/head.php"; 
include "../db/db.php";

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];
    
    $update_sql = "UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $new_status, $payment_status, $order_id);
    
    if ($stmt->execute()) {
        $msg = "Order updated successfully";
        $msgType = "success";
    } else {
        $msg = "Error updating order";
        $msgType = "danger";
    }
}

// Pagination
$limit = 10;
$curr_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($curr_page - 1) * $limit;

// Fetch Orders
$sql = "SELECT o.*, u.name as customer_name, p.transaction_id 
        FROM orders o 
        JOIN users u ON o.customer_id = u.id 
        LEFT JOIN payments p ON o.id = p.order_id
        ORDER BY o.created_at DESC 
        LIMIT $start, $limit";
$result = $conn->query($sql);

// Count Total
$total_result = $conn->query("SELECT COUNT(*) as count FROM orders");
$total_rows = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $limit);

?>
<body class="sb-nav-fixed">
    <?php include "inc/navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "inc/sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">All Orders</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>

                    <?php if (isset($msg)): ?>
                        <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Order List
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Order Status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo "#" . $row['order_number']; ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                                        <td>৳<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['payment_status'] == 'paid' ? 'success' : ($row['payment_status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst($row['payment_status']); ?>
                                            </span>
                                            <br><small><?php echo ucfirst($row['payment_method']); ?></small>
                                            <?php if ($row['payment_method'] !== 'cod' && !empty($row['transaction_id'])): ?>
                                                <br><small class="text-muted fw-bold">TrxID: <?php echo htmlspecialchars($row['transaction_id']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark"><?php echo ucfirst($row['order_status']); ?></span>
                                        </td>
                                        <td>
                                            <a href="../invoice.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-print"></i> Invoice</a>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>"><i class="fas fa-edit"></i> Edit</button>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Order #<?php echo $row['order_number']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Payment Status</label>
                                                            <select name="payment_status" class="form-select">
                                                                <option value="pending" <?php echo $row['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="paid" <?php echo $row['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                                <option value="failed" <?php echo $row['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Order Status</label>
                                                            <select name="order_status" class="form-select">
                                                                <option value="pending" <?php echo $row['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="confirmed" <?php echo $row['order_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                                <option value="processing" <?php echo $row['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                                <option value="shipped" <?php echo $row['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                                <option value="delivered" <?php echo $row['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                                <option value="cancelled" <?php echo $row['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="update_order_status" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>

                             <!-- Pagination -->
                             <nav area-label="Page navigation">
                                <ul class="pagination">
                                    <?php if($curr_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $curr_page - 1; ?>">Previous</a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php for($i=1; $i<=$total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i == $curr_page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <?php if($curr_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $curr_page + 1; ?>">Next</a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </main>
            <?php include "inc/footer.php"; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
