<?php
$page = 'Edit Category';
$description = 'Edit category page';
$author = 'ASA Al-Mamun';
$title = 'Edit Category';

// Include database connection
include_once "../db/db.php";

// Include ImageUtility class
include_once "../classes/ImageUtility.php";

$message = "";
$messageType = "";

// Get category ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: category-all.php");
    exit();
}

$category_id = (int)$_GET['id'];

// Fetch the category to edit
$sql = "SELECT * FROM categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    header("Location: category-all.php");
    exit();
}

// Fetch all categories for parent selection (excluding current category to avoid circular reference)
$categories_sql = "SELECT id, name FROM categories WHERE status = 'active' AND id != ? ORDER BY name ASC";
$categories_stmt = $conn->prepare($categories_sql);
$categories_stmt->bind_param("i", $category_id);
$categories_stmt->execute();
$categories_result = $categories_stmt->get_result();
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Process form submission
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
        // Check if slug already exists (excluding current category)
        $check_sql = "SELECT id FROM categories WHERE slug = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $slug, $category_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $message = "Category slug already exists. Please choose a different slug.";
            $messageType = "danger";
        } else {
            // Handle image upload if provided
            $image = $category['image']; // Keep existing image by default
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
                
                // Delete old image if exists
                if ($category['image'] && file_exists("../" . $category['image'])) {
                    unlink("../" . $category['image']);
                }
                
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
                // Update category
                $update_sql = "UPDATE categories SET name = ?, slug = ?, description = ?, parent_id = ?, image = ?, status = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ssssssi", $name, $slug, $description, $parent_id, $image, $status, $category_id);
                
                if ($update_stmt->execute()) {
                    $message = "Category updated successfully!";
                    $messageType = "success";
                    
                    // Refresh category data
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $category = $result->fetch_assoc();
                } else {
                    $message = "Error updating category: " . $conn->error;
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
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="row">
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
                                        Edit Category
                                    </div>
                                    <div class="card-body">
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Category Name *</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($category['name']); ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="slug" class="form-label">Slug *</label>
                                                    <input type="text" class="form-control" id="slug" name="slug" value="<?php echo isset($_POST['slug']) ? htmlspecialchars($_POST['slug']) : htmlspecialchars($category['slug']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($category['description']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="parent_id" class="form-label">Parent Category</label>
                                                <select class="form-select" id="parent_id" name="parent_id">
                                                    <option value="">Select Parent Category (Leave blank for root category)</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['parent_id']) ? $_POST['parent_id'] : $category['parent_id']) == $cat['id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Category Image</label>
                                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                                <div class="form-text">Leave blank to keep current image. Allowed formats: JPG, JPEG, PNG, GIF</div>
                                                <?php if ($category['image']): ?>
                                                <div class="mt-2">
                                                    <p>Current Image:</p>
                                                    <img src="../<?php echo $category['image']; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" style="max-width: 150px; max-height: 150px;">
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="active" <?php echo (isset($_POST['status']) ? $_POST['status'] : $category['status']) == 'active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo (isset($_POST['status']) ? $_POST['status'] : $category['status']) == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Update Category</button>
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
