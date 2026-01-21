<?php
session_start();

// Check if user is logged in and is a courier
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'courier') {
    header("Location: ../login.php");
    exit;
}

require "../db/db.php";

$user_id = $_SESSION['user_id'];

// Fetch courier profile for display
$stmt = $conn->prepare("SELECT * FROM couriers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$courier_profile = $result->fetch_assoc();
$stmt->close();

// Fetch courier statistics
$stats = [
    'total_deliveries' => 0,
    'pending_deliveries' => 0,
    'total_earnings' => 0
];

if ($courier_profile) {
    $courier_id = $courier_profile['id'];
    
    // Total Deliveries (delivered status)
    $stmt = $conn->prepare("SELECT COUNT(*) as count, SUM(delivery_cost) as earnings FROM deliveries WHERE courier_id = ? AND status = 'delivered'");
    $stmt->bind_param("i", $courier_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stats['total_deliveries'] = $res['count'];
    $stats['total_earnings'] = $res['earnings'] ?? 0;
    $stmt->close();

    // Pending Deliveries (assigned, picked_up, in_transit)
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM deliveries WHERE courier_id = ? AND status IN ('assigned', 'picked_up', 'in_transit')");
    $stmt->bind_param("i", $courier_id);
    $stmt->execute();
    $stats['pending_deliveries'] = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Dashboard - MarketPlace</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stat-card {
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .quick-link {
            text-decoration: none;
            color: inherit;
        }
        .quick-link:hover {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-shipping-fast me-3"></i>Courier Dashboard</h1>
                    <p class="mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="../logout.php" class="btn btn-light">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="stat-icon bg-success text-white mx-auto">
                                    <i class="fas fa-truck"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h4>Delivery Partner Portal</h4>
                                <p class="mb-0">
                                    <?php if ($courier_profile): ?>
                                        Status: <span class="badge bg-<?php echo $courier_profile['status'] == 'active' ? 'success' : ($courier_profile['status'] == 'busy' ? 'warning' : 'secondary'); ?>">
                                            <?php echo ucfirst($courier_profile['status']); ?>
                                        </span> | Vehicle: <?php echo htmlspecialchars($courier_profile['vehicle_type']); ?> (<?php echo htmlspecialchars($courier_profile['vehicle_number']); ?>)
                                    <?php else: ?>
                                        Please complete your courier profile to start receiving delivery assignments.
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="profile.php" class="btn btn-outline-success">
                                    <?php echo $courier_profile ? 'Edit Profile' : 'Setup Profile'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-primary text-white mx-auto mb-3">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h3><?php echo $stats['pending_deliveries']; ?></h3>
                        <p class="text-muted mb-0">Active Assignments</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-success text-white mx-auto mb-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3><?php echo $stats['total_deliveries']; ?></h3>
                        <p class="text-muted mb-0">Completed Deliveries</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-warning text-white mx-auto mb-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h3>৳<?php echo number_format($stats['total_earnings'], 2); ?></h3>
                        <p class="text-muted mb-0">Total Earnings</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">Quick Actions</h3>
            </div>
            
            <div class="col-md-3 mb-4">
                <a href="profile.php" class="quick-link">
                    <div class="card stat-card h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-primary text-white mx-auto mb-3">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <h5>Profile Settings</h5>
                            <p class="text-muted small">Update your vehicle and coverage info</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 mb-4">
                <a href="deliveries.php" class="quick-link">
                    <div class="card stat-card h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-info text-white mx-auto mb-3">
                                <i class="fas fa-route"></i>
                            </div>
                            <h5>My Deliveries</h5>
                            <p class="text-muted small">View and update delivery status</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 mb-4">
                <a href="available-orders.php" class="quick-link">
                    <div class="card stat-card h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-success text-white mx-auto mb-3">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <h5>Available Orders</h5>
                            <p class="text-muted small">Pick up new delivery assignments</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 mb-4">
                <a href="../" class="quick-link">
                    <div class="card stat-card h-100">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-secondary text-white mx-auto mb-3">
                                <i class="fas fa-home"></i>
                            </div>
                            <h5>Go to Site</h5>
                            <p class="text-muted small">Back to the main marketplace</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
