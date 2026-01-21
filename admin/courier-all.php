<?php
$page = 'All Couriers';
$description = 'Manage all couriers page';
$author = 'ASA Al-Mamun';
$title = 'All Couriers';

// Include database connection
include_once "../db/db.php";

$message = "";
$messageType = "";

// Handle status change
if (isset($_GET['action']) && $_GET['action'] == 'status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    
    $update_sql = "UPDATE users SET status = ? WHERE id = ? AND role = 'courier'";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        $message = "Courier status updated to " . ucfirst($status) . " successfully!";
        $messageType = "success";
    } else {
        $message = "Error updating status: " . $conn->error;
        $messageType = "danger";
    }
}

// Fetch couriers with stats
$sql = "SELECT u.id, u.name, u.email, u.status, c.vehicle_type, c.vehicle_number, c.status as courier_status,
        (SELECT COUNT(*) FROM deliveries WHERE courier_id = c.id AND status = 'delivered') as total_deliveries
        FROM users u
        LEFT JOIN couriers c ON u.id = c.user_id
        WHERE u.role = 'courier'
        ORDER BY u.id DESC";

$result = $conn->query($sql);
$couriers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $couriers[] = $row;
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
                        <h1 class="mt-4">All Couriers</h1>
                        <ol class="breadcrumb mb-4">                            
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">All Couriers</li>
                        </ol>

                        <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-truck me-1"></i>
                                Registered Couriers
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="couriersTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Courier Details</th>
                                                <th>Vehicle Info</th>
                                                <th>Deliveries</th>
                                                <th>User Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($couriers) > 0): ?>
                                                <?php foreach ($couriers as $courier): ?>
                                                <tr>
                                                    <td><?php echo $courier['id']; ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($courier['name']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($courier['email']); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if ($courier['vehicle_type']): ?>
                                                            <span class="badge bg-secondary"><?php echo ucfirst($courier['vehicle_type']); ?></span><br>
                                                            <small><?php echo htmlspecialchars($courier['vehicle_number']); ?></small>
                                                        <?php else: ?>
                                                            <span class="text-muted">Profile incomplete</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo $courier['total_deliveries'] ?? 0; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            if($courier['status'] == 'active') echo 'success';
                                                            else if($courier['status'] == 'inactive') echo 'warning';
                                                            else echo 'danger';
                                                        ?>">
                                                            <?php echo ucfirst($courier['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                                Status
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="?action=status&id=<?php echo $courier['id']; ?>&status=active">Active</a></li>
                                                                <li><a class="dropdown-item" href="?action=status&id=<?php echo $courier['id']; ?>&status=inactive">Inactive</a></li>
                                                                <li><a class="dropdown-item" href="?action=status&id=<?php echo $courier['id']; ?>&status=suspended">Suspend</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No couriers found.</td>
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
