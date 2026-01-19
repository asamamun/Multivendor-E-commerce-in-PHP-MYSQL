<?php
$page = 'Add Brand';
$description = 'Add new brand page';
$author = 'ASA Al-Mamun';
$title = 'Add Brand';

// Include database connection
include_once "../db/db.php";

// Include ImageUtility class
include_once "../classes/ImageUtility.php";

$message = "";
$messageType = "";

if ($_POST) {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $status = trim($_POST['status']);
    
    // Validate required fields
    if (empty($name) || empty($slug)) {
        $message = "Name and slug are required.";
        $messageType = "danger";
    } else {
        // Check if slug already exists
        $check_sql = "SELECT id FROM brands WHERE slug = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $slug);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $message = "Brand slug already exists. Please choose a different slug.";
            $messageType = "danger";
        } else {
            // Handle image upload if provided
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Create ImageUtility instance
                $imageUtility = new ImageUtility(75, 800, 600); // 75% quality, max 800x600
                
                $upload_dir = "../assets/uploads/brands/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $image_file = $_FILES['image'];
                $image_name = time() . "_" . basename($image_file['name']);
                $final_path = $upload_dir . $image_name;
                
                // Process image using ImageUtility
                $result = $imageUtility->processUploadedFile(
                    $image_file,
                    $final_path,
                    ['quality' => 75, 'max_width' => 800, 'max_height' => 600]
                );
                
                if ($result['success']) {
                    $image = "assets/uploads/brands/" . $image_name;
                } else {
                    $message = "Error processing image: " . $result['message'];
                    $messageType = "danger";
                }
            }
            
            if (empty($message)) { // If no error occurred
                // Insert new brand
                $sql = "INSERT INTO brands (name, slug, image, status) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $name, $slug, $image, $status);
                
                if ($stmt->execute()) {
                    $message = "Brand added successfully!";
                    $messageType = "success";
                    // Reset form values
                    $_POST = array();
                } else {
                    $message = "Error adding brand: " . $conn->error;
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
        <?php include "inc/head.php"; ?>
    </head>
    <body class="sb-nav-fixed">
        <?php include "inc/navbar.php"; ?>
        <div id="layoutSidenav">
            <?php include "inc/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Add Brand</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard / Add Brand / <a href="brand-all.php">All Brands</a></li>
                        </ol>
                        <div class="row">
                            <!-- Flash message display -->
                            <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-xl-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-plus me-1"></i>
                                        Add New Brand
                                    </div>
                                    <div class="card-body">
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Brand Name *</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="slug" class="form-label">Slug *</label>
                                                    <input type="text" class="form-control" id="slug" name="slug" value="<?php echo isset($_POST['slug']) ? htmlspecialchars($_POST['slug']) : ''; ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Brand Image</label>
                                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                                <div class="form-text">Allowed formats: JPG, JPEG, PNG, GIF, WEBP</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Add Brand</button>
                                            <a href="brand-all.php" class="btn btn-secondary">Cancel</a>
                                        </form>
                                    </div>
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
