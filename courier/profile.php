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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $vehicle_type = $_POST['vehicle_type'] ?? 'bike';
    $vehicle_number = trim($_POST['vehicle_number'] ?? '');
    $coverage_areas = trim($_POST['coverage_areas'] ?? '');
    $status = $_POST['status'] ?? 'active';

    // Validate required fields
    if (empty($name) || empty($phone)) {
        $message = 'Name and Phone are required';
        $messageType = 'danger';
    } else {
        // Check if courier profile exists
        $check_stmt = $conn->prepare("SELECT id FROM couriers WHERE user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing profile
            $stmt = $conn->prepare("UPDATE couriers SET name = ?, phone = ?, email = ?, vehicle_type = ?, vehicle_number = ?, coverage_areas = ?, status = ? WHERE user_id = ?");
            // Coverage areas should be JSON valid, for now we just store as string or empty array if empty
            $coverage_json = json_encode(array_filter(explode(',', $coverage_areas)));
            $stmt->bind_param("sssssssi", $name, $phone, $email, $vehicle_type, $vehicle_number, $coverage_json, $status, $user_id);
        } else {
            // Insert new profile
            $stmt = $conn->prepare("INSERT INTO couriers (user_id, name, phone, email, vehicle_type, vehicle_number, coverage_areas, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $coverage_json = json_encode(array_filter(explode(',', $coverage_areas)));
            $stmt->bind_param("isssssss", $user_id, $name, $phone, $email, $vehicle_type, $vehicle_number, $coverage_json, $status);
        }

        if ($stmt->execute()) {
            $message = 'Profile updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error updating profile: ' . $conn->error;
            $messageType = 'danger';
        }
        $stmt->close();
        $check_stmt->close();
    }
}

// Fetch current courier profile
$stmt = $conn->prepare("SELECT * FROM couriers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$courier_profile = $result->fetch_assoc();
$stmt->close();

// Fetch user info as fallback
if (!$courier_profile) {
    $stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $courier_profile = [
        'name' => $user_info['name'],
        'email' => $user_info['email'],
        'phone' => $user_info['phone'],
        'vehicle_type' => 'bike',
        'vehicle_number' => '',
        'coverage_areas' => '[]',
        'status' => 'active'
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Profile - MarketPlace</title>
    
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
        .profile-header {
            background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .profile-card {
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
        }
        .btn-success {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-id-card me-3"></i>Courier Profile</h1>
                    <p class="mb-0">Manage your delivery partner information</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="dashboard.php" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success/Error Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card profile-card">
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <h4 class="border-bottom pb-2"><i class="fas fa-user-circle me-2"></i>Personal Information</h4>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?php echo htmlspecialchars($courier_profile['name'] ?? ''); ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number *</label>
                                    <input type="text" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($courier_profile['phone'] ?? ''); ?>" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?php echo htmlspecialchars($courier_profile['email'] ?? ''); ?>">
                                </div>

                                <div class="col-12 mb-4 mt-2">
                                    <h4 class="border-bottom pb-2"><i class="fas fa-truck me-2"></i>Vehicle & Work Information</h4>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vehicle Type</label>
                                    <select class="form-select" name="vehicle_type">
                                        <option value="bike" <?php echo $courier_profile['vehicle_type'] == 'bike' ? 'selected' : ''; ?>>Bike</option>
                                        <option value="car" <?php echo $courier_profile['vehicle_type'] == 'car' ? 'selected' : ''; ?>>Car</option>
                                        <option value="van" <?php echo $courier_profile['vehicle_type'] == 'van' ? 'selected' : ''; ?>>Van</option>
                                        <option value="truck" <?php echo $courier_profile['vehicle_type'] == 'truck' ? 'selected' : ''; ?>>Truck</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vehicle Registration Number</label>
                                    <input type="text" class="form-control" name="vehicle_number" 
                                           value="<?php echo htmlspecialchars($courier_profile['vehicle_number'] ?? ''); ?>">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Coverage Areas (comma separated)</label>
                                    <?php 
                                        $areas = json_decode($courier_profile['coverage_areas'], true);
                                        $areas_str = is_array($areas) ? implode(', ', $areas) : '';
                                    ?>
                                    <input type="text" class="form-control" name="coverage_areas" 
                                           placeholder="e.g. Dhaka, Mirpur, Uttara"
                                           value="<?php echo htmlspecialchars($areas_str); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Availability Status</label>
                                    <select class="form-select" name="status">
                                        <option value="active" <?php echo $courier_profile['status'] == 'active' ? 'selected' : ''; ?>>Active / Ready</option>
                                        <option value="busy" <?php echo $courier_profile['status'] == 'busy' ? 'selected' : ''; ?>>Busy / On Delivery</option>
                                        <option value="inactive" <?php echo $courier_profile['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive / Off Duty</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-save me-2"></i>Update Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
