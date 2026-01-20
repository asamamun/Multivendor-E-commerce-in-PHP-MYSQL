<?php
$page = 'System Settings';
$description = 'Manage general system settings';
$author = 'ASA Al-Mamun';
$title = 'System Settings';

include "inc/head.php"; 
include "../db/db.php";
include_once "../classes/ImageUtility.php";

$message = "";
$messageType = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Handle Text Fields
    $updatable_fields = [
        'company_name', 'company_address', 'company_email', 'company_phone', 
        'vat_rate', 'commission_rate', 'currency_symbol', 'footer_text',
        'social_facebook', 'social_twitter', 'social_instagram'
    ];

    foreach ($updatable_fields as $key) {
        if (isset($_POST[$key])) {
            $value = $conn->real_escape_string($_POST[$key]);
            
            // Check if key exists
            $check = $conn->query("SELECT id FROM system_settings WHERE setting_key = '$key'");
            if ($check->num_rows > 0) {
                $sql = "UPDATE system_settings SET setting_value = '$value' WHERE setting_key = '$key'";
            } else {
                $sql = "INSERT INTO system_settings (setting_key, setting_value) VALUES ('$key', '$value')";
            }
            $conn->query($sql);
        }
    }

    // 2. Handle File Uploads
    $upload_dir = "../assets/uploads/settings/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $imageUtility = new ImageUtility(80, 800, 600); // 80% quality

    $file_fields = ['site_logo', 'site_favicon'];

    foreach ($file_fields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            $file = $_FILES[$field];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = ($field == 'site_logo' ? 'logo' : 'favicon') . '.' . $ext;
            $final_path = $upload_dir . $filename;
            
            // For logo/favicon, we might want to keep original dimensions or restrict lightly
            // Bypassing strict resizing for favicon usually, but ImageUtility handles it.
            // Let's just move file for favicon if it's .ico, or process if image
            
            if ($field === 'site_favicon' && strtolower($ext) === 'ico') {
                move_uploaded_file($file['tmp_name'], $final_path);
                $db_path = "assets/uploads/settings/" . $filename;
            } else {
                // Process standard images
                $result = $imageUtility->processUploadedFile(
                    $file, 
                    $final_path, 
                    ['quality' => 90] // High quality for UI elements
                );
                
                if ($result['success']) {
                    $db_path = "assets/uploads/settings/" . $filename;
                }
            }

            if (isset($db_path)) {
                // Remove old entry relative path if different (optional, here we overwrite file essentially)
                
                // Update DB
                $check = $conn->query("SELECT id FROM system_settings WHERE setting_key = '$field'");
                if ($check->num_rows > 0) {
                    $conn->query("UPDATE system_settings SET setting_value = '$db_path' WHERE setting_key = '$field'");
                } else {
                    $conn->query("INSERT INTO system_settings (setting_key, setting_value) VALUES ('$field', '$db_path')");
                }
            }
        }
    }

    $message = "Settings updated successfully!";
    $messageType = "success";
}

// Fetch Settings
$settings = [];
$result = $conn->query("SELECT * FROM system_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Helper function to safely get setting
function get_setting($key, $data) {
    return isset($data[$key]) ? htmlspecialchars($data[$key]) : '';
}

?>
<body class="sb-nav-fixed">
    <?php include "inc/navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "inc/sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">System Settings</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- General Settings -->
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-building me-1"></i>
                                        General Information
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" class="form-control" name="company_name" value="<?php echo get_setting('company_name', $settings); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="company_address" rows="3"><?php echo get_setting('company_address', $settings); ?></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="company_email" value="<?php echo get_setting('company_email', $settings); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control" name="company_phone" value="<?php echo get_setting('company_phone', $settings); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-money-bill me-1"></i>
                                        Financial Settings
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Currency Symbol</label>
                                                <input type="text" class="form-control" name="currency_symbol" value="<?php echo get_setting('currency_symbol', $settings); ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">VAT Rate (%)</label>
                                                <input type="number" step="0.01" class="form-control" name="vat_rate" value="<?php echo get_setting('vat_rate', $settings); ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Commission (%)</label>
                                                <input type="number" step="0.01" class="form-control" name="commission_rate" value="<?php echo get_setting('commission_rate', $settings); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Media & Social -->
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-images me-1"></i>
                                        Media
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-md-8">
                                                <label class="form-label">Site Logo</label>
                                                <input type="file" class="form-control" name="site_logo">
                                                <div class="form-text">Recommended height: 50px</div>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <?php if(isset($settings['site_logo']) && !empty($settings['site_logo'])): ?>
                                                    <img src="../<?php echo $settings['site_logo']; ?>" alt="Logo" class="img-thumbnail" style="max-height: 50px;">
                                                <?php else: ?>
                                                    <span class="text-muted">No uploaded logo</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <label class="form-label">Favicon</label>
                                                <input type="file" class="form-control" name="site_favicon">
                                            </div>
                                            <div class="col-md-4 text-center">
                                                <?php if(isset($settings['site_favicon']) && !empty($settings['site_favicon'])): ?>
                                                    <img src="../<?php echo $settings['site_favicon']; ?>" alt="Favicon" class="img-thumbnail" style="max-height: 32px;">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-share-alt me-1"></i>
                                        Social & Footer
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Footer Text</label>
                                            <input type="text" class="form-control" name="footer_text" value="<?php echo get_setting('footer_text', $settings); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fab fa-facebook text-primary me-1"></i> Facebook URL</label>
                                            <input type="url" class="form-control" name="social_facebook" value="<?php echo get_setting('social_facebook', $settings); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter URL</label>
                                            <input type="url" class="form-control" name="social_twitter" value="<?php echo get_setting('social_twitter', $settings); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fab fa-instagram text-danger me-1"></i> Instagram URL</label>
                                            <input type="url" class="form-control" name="social_instagram" value="<?php echo get_setting('social_instagram', $settings); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Save Settings</button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
            <?php include "inc/footer.php"; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
