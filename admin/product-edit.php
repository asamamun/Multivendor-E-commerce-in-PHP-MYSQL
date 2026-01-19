<?php
$page = 'Edit Product';
$description = 'Edit product page';
$author = 'ASA Al-Mamun';
$title = 'Edit Product';

// Include database connection
include_once "../db/db.php";

$message = "";
$messageType = "";

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: product-all.php");
    exit;
}

$id = (int)$_GET['id'];

// Handle form submission
if ($_POST) {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $category_id = (int)$_POST['category_id'];
    $brand_id = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
    $price = (float)$_POST['price'];
    $compare_price = !empty($_POST['compare_price']) ? (float)$_POST['compare_price'] : 0.00;
    $cost_price = !empty($_POST['cost_price']) ? (float)$_POST['cost_price'] : 0.00;
    $stock_quantity = (int)$_POST['stock_quantity'];
    $sku = trim($_POST['sku']);
    $status = $_POST['status'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    $short_description = trim($_POST['short_description']);
    $description = trim($_POST['description']);
    $meta_title = trim($_POST['meta_title']);
    $meta_description = trim($_POST['meta_description']);
    
    // Basic validation
    if (empty($name) || empty($slug) || empty($price)) {
        $message = "Name, Slug, and Price are required.";
        $messageType = "danger";
    } else {
        // Update product
        $update_sql = "UPDATE products SET 
                       name = ?, 
                       slug = ?, 
                       category_id = ?, 
                       brand_id = ?, 
                       price = ?, 
                       compare_price = ?, 
                       cost_price = ?, 
                       stock_quantity = ?, 
                       sku = ?, 
                       status = ?, 
                       featured = ?, 
                       short_description = ?, 
                       description = ?,
                       meta_title = ?,
                       meta_description = ?,
                       updated_at = NOW()
                       WHERE id = ?";
                       
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssiidddississssi", 
            $name, 
            $slug, 
            $category_id, 
            $brand_id, 
            $price, 
            $compare_price, 
            $cost_price, 
            $stock_quantity, 
            $sku, 
            $status, 
            $featured, 
            $short_description, 
            $description,
            $meta_title,
            $meta_description,
            $id
        );
        
        if ($stmt->execute()) {
            $message = "Product updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating product: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Fetch existing product data
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: product-all.php");
    exit;
}

$product = $result->fetch_assoc();

// Fetch categories for dropdown
$cat_sql = "SELECT id, name FROM categories ORDER BY name";
$cat_result = $conn->query($cat_sql);
$categories = [];
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch brands for dropdown
$brand_sql = "SELECT id, name FROM brands ORDER BY name";
$brand_result = $conn->query($brand_sql);
$brands = [];
while ($row = $brand_result->fetch_assoc()) {
    $brands[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>        
        <?php include "inc/head.php"; ?>
        <!-- Summernote CSS for Rich Text Editor -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    </head>
    <body class="sb-nav-fixed">
        <?php include "inc/navbar.php"; ?>
        <div id="layoutSidenav">
            <?php include "inc/sidebar.php"; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Edit Product</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard / Edit Product / <a href="product-all.php">All Products</a></li>
                        </ol>
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <form method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-xl-8">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-edit me-1"></i>
                                                Product Information
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Product Name *</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="slug" class="form-label">Slug *</label>
                                                    <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($product['slug']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="short_description" class="form-label">Short Description</label>
                                                    <textarea class="form-control" id="short_description" name="short_description" rows="3"><?php echo htmlspecialchars($product['short_description']); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control summernote" id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-tags me-1"></i>
                                                SEO Settings
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="meta_title" class="form-label">Meta Title</label>
                                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?php echo htmlspecialchars($product['meta_title']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="meta_description" class="form-label">Meta Description</label>
                                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?php echo htmlspecialchars($product['meta_description']); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-4">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-cog me-1"></i>
                                                Settings
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select class="form-select" id="status" name="status">
                                                        <option value="active" <?php echo $product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                        <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                        <option value="draft" <?php echo $product['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" class="form-check-input" id="featured" name="featured" <?php echo $product['featured'] == 1 ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="featured">Featured Product</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-list me-1"></i>
                                                Organization
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label">Category</label>
                                                    <select class="form-select" id="category_id" name="category_id" required>
                                                        <option value="">Select Category</option>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($cat['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="brand_id" class="form-label">Brand</label>
                                                    <select class="form-select" id="brand_id" name="brand_id">
                                                        <option value="">Select Brand</option>
                                                        <?php foreach ($brands as $brand): ?>
                                                            <option value="<?php echo $brand['id']; ?>" <?php echo $product['brand_id'] == $brand['id'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($brand['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-money-bill me-1"></i>
                                                Pricing & Inventory
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Price *</label>
                                                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="compare_price" class="form-label">Compare Price</label>
                                                    <input type="number" step="0.01" class="form-control" id="compare_price" name="compare_price" value="<?php echo htmlspecialchars($product['compare_price']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="cost_price" class="form-label">Cost Price</label>
                                                    <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="<?php echo htmlspecialchars($product['cost_price']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="sku" class="form-label">SKU</label>
                                                    <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stock_quantity" class="form-label">Stock Quantity</label>
                                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Update Product</button>
                                            <a href="product-all.php" class="btn btn-secondary">Cancel</a>
                                        </div>
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
        <!-- Summernote JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.summernote').summernote({
                    placeholder: 'Type product description here...',
                    tabsize: 2,
                    height: 200,
                    toolbar: [
                      ['style', ['style']],
                      ['font', ['bold', 'underline', 'clear']],
                      ['color', ['color']],
                      ['para', ['ul', 'ol', 'paragraph']],
                      ['table', ['table']],
                      ['insert', ['link']],
                      ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            });
        </script>
    </body>
</html>
