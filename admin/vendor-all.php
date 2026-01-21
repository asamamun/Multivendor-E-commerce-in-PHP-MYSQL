<?php
$page = 'All Vendors';
$description = 'Manage all vendors page';
$author = 'ASA Al-Mamun';
$title = 'All Vendors';

// Include database connection
include_once "../db/db.php";

$message = "";
$messageType = "";

// Handle status change
if (isset($_GET['action']) && $_GET['action'] == 'status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    
    $update_sql = "UPDATE users SET status = ? WHERE id = ? AND role = 'vendor'";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        $message = "Vendor status updated to " . ucfirst($status) . " successfully!";
        $messageType = "success";
    } else {
        $message = "Error updating status: " . $conn->error;
        $messageType = "danger";
    }
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $vendor_id = (int)$_POST['vendor_id'];
    $msg_title = $_POST['msg_title'];
    $msg_body = $_POST['msg_body'];
    
    $msg_sql = "INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'system', ?, ?)";
    $stmt = $conn->prepare($msg_sql);
    $stmt->bind_param("iss", $vendor_id, $msg_title, $msg_body);
    
    if ($stmt->execute()) {
        $message = "Message sent to vendor successfully!";
        $messageType = "success";
    } else {
        $message = "Error sending message: " . $conn->error;
        $messageType = "danger";
    }
}

// Fetch vendors with stats
$sql = "SELECT u.id, u.name, u.email, u.status, vp.store_name, vp.commission_rate,
        (SELECT SUM(oi.total_price) FROM order_items oi 
         JOIN orders o ON oi.order_id = o.id 
         WHERE oi.vendor_id = u.id AND o.payment_status = 'paid') as total_sales
        FROM users u
        LEFT JOIN vendor_profiles vp ON u.id = vp.user_id
        WHERE u.role = 'vendor'
        ORDER BY u.id DESC";

$result = $conn->query($sql);
$vendors = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vendors[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>        
        <?php include "inc/head.php"; ?>
    </head>
    <body class="sb-nav-fixed">
        <?php include "inc/navbar.php"; ?>
        <div id="layoutSidenav">
            <?php include "inc/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">All Vendors</h1>
                        <ol class="breadcrumb mb-4">                            
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">All Vendors</li>
                        </ol>

                        <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-users me-1"></i>
                                Registered Vendors
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="vendorsTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Vendor Details</th>
                                                <th>Store Info</th>
                                                <th>Stats (Paid Orders)</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($vendors) > 0): ?>
                                                <?php foreach ($vendors as $vendor): 
                                                    $total_sale = $vendor['total_sales'] ?? 0;
                                                    $comm_rate = $vendor['commission_rate'] ?? 10.00;
                                                    $earning = $total_sale * (1 - ($comm_rate / 100));
                                                ?>
                                                <tr>
                                                    <td><?php echo $vendor['id']; ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($vendor['name']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($vendor['email']); ?></small>
                                                    </td>
                                                    <td>
                                                        <a href="../vendor.php?id=<?php echo $vendor['id']; ?>" target="_blank" class="text-decoration-none fw-bold">
                                                            <?php echo htmlspecialchars($vendor['store_name'] ?? 'N/A'); ?>
                                                        </a><br>
                                                        <small class="text-primary">Comm: <?php echo number_format($comm_rate, 2); ?>%</small>
                                                    </td>
                                                    <td>
                                                        Sale: ৳<?php echo number_format($total_sale, 2); ?><br>
                                                        <span class="text-success">Earn: ৳<?php echo number_format($earning, 2); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            if($vendor['status'] == 'active') echo 'success';
                                                            else if($vendor['status'] == 'inactive') echo 'warning';
                                                            else echo 'danger';
                                                        ?>">
                                                            <?php echo ucfirst($vendor['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                                Status
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="?action=status&id=<?php echo $vendor['id']; ?>&status=active">Active</a></li>
                                                                <li><a class="dropdown-item" href="?action=status&id=<?php echo $vendor['id']; ?>&status=inactive">Inactive</a></li>
                                                                <li><a class="dropdown-item" href="?action=status&id=<?php echo $vendor['id']; ?>&status=suspended">Suspend</a></li>
                                                            </ul>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $vendor['id']; ?>" title="Message">
                                                            <i class="fas fa-envelope"></i>
                                                        </button>
                                                        
                                                        <!-- Message Modal -->
                                                        <div class="modal fade" id="messageModal<?php echo $vendor['id']; ?>" tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form method="POST">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Message to <?php echo htmlspecialchars($vendor['name']); ?></h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="vendor_id" value="<?php echo $vendor['id']; ?>">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Subject</label>
                                                                                <input type="text" name="msg_title" class="form-control" placeholder="e.g. Account Verification Required" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Message</label>
                                                                                <textarea name="msg_body" class="form-control" rows="4" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No vendors found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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
