<?php
$page = 'Edit Brand';
$description = 'Edit brand page';
$author = 'ASA Al-Mamun';
$title = 'Edit Brand';

// Include database connection
include_once "../db/db.php";

// Include ImageUtility class
include_once "../classes/ImageUtility.php";

$message = "";
$messageType = "";

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: brand-all.php");
    exit;
}

$id = (int)$_GET['id'];

// Fetch existing brand data
$sql = "SELECT * FROM brands WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: brand-all.php");
    exit;
}

$brand = $result->fetch_assoc();

if ($_POST) {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $status = trim($_POST['status']);
    
    // Validate required fields
    if (empty($name) || empty($slug)) {
        $message = "Name and slug are required.";
        $messageType = "danger";
    } else {
        // Check if slug already exists (excluding current brand)
        $check_sql = "SELECT id FROM brands WHERE slug = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $slug, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $message = "Brand slug already exists. Please choose a different slug.";
            $messageType = "danger";
        } else {
            // Handle image upload if provided
            $image = $brand['image']; // Default to existing image
            
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
                    // Delete old image if exists
                    if (!empty($brand['image']) && file_exists("../" . $brand['image'])) {
                        unlink("../" . $brand['image']);
                    }
                    $image = "assets/uploads/brands/" . $image_name;
                } else {
                    $message = "Error processing image: " . $result['message'];
                    $messageType = "danger";
                }
            }
            
            if (empty($message)) { // If no error occurred
                // Update brand
                $sql = "UPDATE brands SET name = ?, slug = ?, image = ?, status = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $name, $slug, $image, $status, $id);
                
                if ($stmt->execute()) {
                    $message = "Brand updated successfully!";
                    $messageType = "success";
                    // Update current brand variable for display
                    $brand['name'] = $name;
                    $brand['slug'] = $slug;
                    $brand['image'] = $image;
                    $brand['status'] = $status;
                } else {
                    $message = "Error updating brand: " . $conn->error;
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
                        <h1 class="mt-4">Edit Brand</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard / Edit Brand / <a href="brand-all.php">All Brands</a></li>
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
                                        <i class="fas fa-edit me-1"></i>
                                        Edit Brand
                                    </div>
                                    <div class="card-body">
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Brand Name *</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($brand['name']); ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="slug" class="form-label">Slug *</label>
                                                    <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($brand['slug']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Brand Image</label>
                                                <?php if (!empty($brand['image'])): ?>
                                                    <div class="mb-2">
                                                        <img src="../<?php echo htmlspecialchars($brand['image']); ?>" alt="Current Image" style="max-height: 100px;">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                                <div class="form-text">Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Upload new to replace existing.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="active" <?php echo ($brand['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo ($brand['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Update Brand</button>
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
