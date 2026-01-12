<?php
$page = 'Add Category';
$description = 'Add new category page';
$author = 'ASA Al-Mamun';
$title = 'Add Category';

// Include database connection
include_once "../db/db.php";

// Include ImageUtility class
include_once "../classes/ImageUtility.php";

$message = "";
$messageType = "";

if ($_POST) {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $status = trim($_POST['status']);
    
    // Validate required fields
    if (empty($name) || empty($slug)) {
        $message = "Name and slug are required.";
        $messageType = "danger";
    } else {
        // Check if slug already exists
        $check_sql = "SELECT id FROM categories WHERE slug = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $slug);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        // echo $check_result->num_rows;
        // exit;
        
        if ($check_result->num_rows > 0) {
            $message = "Category slug already exists. Please choose a different slug.";
            $messageType = "danger";
        } else {
            // Handle image upload if provided
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Create ImageUtility instance
                $imageUtility = new ImageUtility(75, 800, 600); // 75% quality, max 800x600
                
                $upload_dir = "../assets/uploads/categories/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $image_file = $_FILES['image'];
                $image_name = time() . "_" . basename($image_file['name']);
                $final_path = $upload_dir . $image_name;
                
                // Debug: Log file information for troubleshooting
                error_log("File upload debug - Name: " . $image_file['name'] . ", Type: " . $image_file['type'] . ", Size: " . $image_file['size']);
                
                // Process image using ImageUtility (includes file type validation)
                $result = $imageUtility->processUploadedFile(
                    $image_file,
                    $final_path,
                    ['quality' => 75, 'max_width' => 800, 'max_height' => 600]
                );
                
                if ($result['success']) {
                    $image = "assets/uploads/categories/" . $image_name;
                    
                    // Optional: Add information about compression to debug
                    // Uncomment the next line to see compression details
                    // error_log("Image compressed from {$result['original_size']} bytes to {$result['processed_size']} bytes");
                } else {
                    $message = "Error processing image: " . $result['message'];
                    $messageType = "danger";
                }
            }
            
            if (empty($message)) { // If no error occurred during image upload
                // Insert new category
                $sql = "INSERT INTO categories (name, slug, description, parent_id, image, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $name, $slug, $description, $parent_id, $image, $status);
                
                if ($stmt->execute()) {
                    $message = "Category added successfully!";
                    $messageType = "success";
                    // Reset form values
                    $_POST = array();
                } else {
                    $message = "Error adding category: " . $conn->error;
                    $messageType = "danger";
                }
            }
        }
    }
}

// Fetch all categories for parent selection
$categories_sql = "SELECT id, name FROM categories WHERE status = 'active' ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
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
                        <h1 class="mt-4">Add Category</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard / Add Category / <a href="category-all.php">All Categories</a></li>
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
                                        Add New Category
                                    </div>
                                    <div class="card-body">
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Category Name *</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="slug" class="form-label">Slug *</label>
                                                    <input type="text" class="form-control" id="slug" name="slug" value="<?php echo isset($_POST['slug']) ? htmlspecialchars($_POST['slug']) : ''; ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="parent_id" class="form-label">Parent Category</label>
                                                <select class="form-select" id="parent_id" name="parent_id">
                                                    <option value="">Select Parent Category (Leave blank for root category)</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['parent_id']) && $_POST['parent_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Category Image</label>
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
                                            <button type="submit" class="btn btn-primary">Add Category</button>
                                            <a href="category-all.php" class="btn btn-secondary">Cancel</a>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
