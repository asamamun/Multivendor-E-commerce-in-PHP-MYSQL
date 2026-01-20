<?php
$page = 'All Products';
$description = 'Manage all products page';
$author = 'ASA Al-Mamun';
$title = 'All Products';

// Include database connection
include_once "../db/db.php";

$message = "";
$messageType = "";

// Handle delete/restore request
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if ($_GET['action'] == 'delete') {
        $delete_sql = "UPDATE products SET deleted_at = NOW() WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            $message = "Product deleted (archived) successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting product: " . $conn->error;
            $messageType = "danger";
        }
    } elseif ($_GET['action'] == 'restore') {
        $restore_sql = "UPDATE products SET deleted_at = NULL WHERE id = ?";
        $restore_stmt = $conn->prepare($restore_sql);
        $restore_stmt->bind_param("i", $id);
        
        if ($restore_stmt->execute()) {
            $message = "Product restored successfully!";
            $messageType = "success";
        } else {
            $message = "Error restoring product: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Filter Logic
$where_clauses = [];
$params = [];
$types = "";

// Filter by Name
$f_name = isset($_GET['f_name']) ? trim($_GET['f_name']) : '';
if (!empty($f_name)) {
    $where_clauses[] = "p.name LIKE ?";
    $params[] = "%$f_name%";
    $types .= "s";
}

// Filter by Category
$f_category = isset($_GET['f_category']) ? (int)$_GET['f_category'] : '';
if (!empty($f_category)) {
    $where_clauses[] = "p.category_id = ?";
    $params[] = $f_category;
    $types .= "i";
}

// Filter by Featured
$f_featured = isset($_GET['f_featured']) ? $_GET['f_featured'] : '';
if ($f_featured === 'yes') {
    $where_clauses[] = "p.featured = 1";
} elseif ($f_featured === 'no') {
    $where_clauses[] = "p.featured = 0";
}

// Filter by Status
$f_status = isset($_GET['f_status']) ? $_GET['f_status'] : '';
if (!empty($f_status) && $f_status !== 'all') {
    $where_clauses[] = "p.status = ?";
    $params[] = $f_status;
    $types .= "s";
}

// Filter by Deleted Logic
$f_deleted = isset($_GET['f_deleted']) ? $_GET['f_deleted'] : 'active';
if ($f_deleted === 'active') {
    $where_clauses[] = "p.deleted_at IS NULL";
} elseif ($f_deleted === 'deleted') {
    $where_clauses[] = "p.deleted_at IS NOT NULL";
} else {
    // 'all' - no check on deleted_at
}

// Combine WHERE clauses
$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Pagination Logic
$limit = 10;
$page_num = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page_num < 1) $page_num = 1;
$offset = ($page_num - 1) * $limit;

// Count total products based on filters
$count_sql = "SELECT COUNT(*) as total FROM products p $where_sql";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch products with details based on filters
$sql = "SELECT p.*, 
               c.name as category_name, 
               b.name as brand_name,
               u.name as vendor_name,
               vp.store_name,
               (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) as image_path
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN users u ON p.vendor_id = u.id
        LEFT JOIN vendor_profiles vp ON u.id = vp.user_id
        $where_sql
        ORDER BY p.id DESC 
        LIMIT ?, ?";

// Update params for main query
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Fetch categories for filter dropdown
$cat_sql = "SELECT id, name FROM categories ORDER BY name";
$cat_result = $conn->query($cat_sql);
$categories = [];
while ($row = $cat_result->fetch_assoc()) {
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
                        <h1 class="mt-4">All Products</h1>
                        <ol class="breadcrumb mb-4">                            
                            <li class="breadcrumb-item active">Dashboard / All Products</li>
                        </ol>
                        <div class="row">
                            <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-filter me-1"></i>
                                        Filter Products
                                    </div>
                                    <div class="card-body">
                                        <form method="get" class="row g-3">
                                            <div class="col-md-3">
                                                <label for="f_name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="f_name" name="f_name" value="<?php echo htmlspecialchars($f_name); ?>" placeholder="Product Name">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="f_category" class="form-label">Category</label>
                                                <select class="form-select" id="f_category" name="f_category">
                                                    <option value="">All Categories</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>" <?php echo $f_category == $cat['id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="f_featured" class="form-label">Featured</label>
                                                <select class="form-select" id="f_featured" name="f_featured">
                                                    <option value="">Any</option>
                                                    <option value="yes" <?php echo $f_featured === 'yes' ? 'selected' : ''; ?>>Yes</option>
                                                    <option value="no" <?php echo $f_featured === 'no' ? 'selected' : ''; ?>>No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="f_status" class="form-label">Status</label>
                                                <select class="form-select" id="f_status" name="f_status">
                                                    <option value="">Any</option>
                                                    <option value="active" <?php echo $f_status === 'active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo $f_status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                    <option value="draft" <?php echo $f_status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="f_deleted" class="form-label">Deleted</label>
                                                <select class="form-select" id="f_deleted" name="f_deleted">
                                                    <option value="active" <?php echo $f_deleted === 'active' ? 'selected' : ''; ?>>Not Deleted</option>
                                                    <option value="deleted" <?php echo $f_deleted === 'deleted' ? 'selected' : ''; ?>>Deleted Only</option>
                                                    <option value="all" <?php echo $f_deleted === 'all' ? 'selected' : ''; ?>>All</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-box me-1"></i>
                                        All Products (Total: <?php echo $total_rows; ?>)
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="productsTable">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Image</th>
                                                        <th>Name</th>
                                                        <th>Vendor</th>
                                                        <th>Category</th>
                                                        <th>Price</th>
                                                        <th>Status</th>
                                                        <th>Featured</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (count($products) > 0): ?>
                                                        <?php foreach ($products as $product): ?>
                                                        <?php $is_deleted = !empty($product['deleted_at']); ?>
                                                        <tr class="<?php echo $is_deleted ? 'table-danger' : ''; ?>">
                                                            <td><?php echo $product['id']; ?></td>
                                                            <td>
                                                                <?php if (!empty($product['image_path'])): ?>
                                                                    <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" width="50" style="object-fit: cover; height: 50px;">
                                                                <?php else: ?>
                                                                    <span class="text-muted">No Image</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="../product.php?id=<?php echo $product['id']; ?>" target="_blank" title="View as User">
                                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                                </a>
                                                                <?php if ($is_deleted): ?>
                                                                    <br><span class="badge bg-danger">DELETED</span>
                                                                <?php endif; ?>
                                                                <br>
                                                                <small class="text-muted">SKU: <?php echo htmlspecialchars($product['sku']); ?></small>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                $displayName = !empty($product['store_name']) ? $product['store_name'] : $product['vendor_name'];
                                                                echo htmlspecialchars($displayName); 
                                                                ?>
                                                                <br>
                                                                <a href="../vendor.php?id=<?php echo $product['vendor_id']; ?>" target="_blank" class="small"><i class="fas fa-external-link-alt"></i> Store</a>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                                            <td>
                                                                ৳<?php echo number_format($product['price'], 2); ?>
                                                                <?php if($product['stock_quantity'] <= $product['min_stock_level']): ?>
                                                                    <br><span class="badge bg-danger">Low Stock: <?php echo $product['stock_quantity']; ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                    <?php
                                                                $badgeClass = 'secondary';
                                                                $statusIcon = 'fa-circle'; // Default

                                                                if ($product['status'] === 'active') {
                                                                    $badgeClass = 'success';
                                                                    $statusIcon = 'fa-check-circle';
                                                                } elseif ($product['status'] === 'inactive') {
                                                                    $badgeClass = 'secondary';
                                                                    $statusIcon = 'fa-eye-slash';
                                                                } elseif ($product['status'] === 'draft') {
                                                                    $badgeClass = 'info';
                                                                    $statusIcon = 'fa-pen-nib';
                                                                } elseif ($product['status'] === 'section') {
                                                                    $badgeClass = 'warning text-dark';
                                                                    $statusIcon = 'fa-th-large';
                                                                }
                                                                ?>
                                                                <span class="badge bg-<?php echo $badgeClass; ?>">
                                                                    <i class="fas <?php echo $statusIcon; ?> me-1"></i>
                                                                    <?php echo ucfirst($product['status']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if ($product['featured']): ?>
                                                                    <span class="badge bg-primary">Yes</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-light text-dark">No</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <?php if (!$is_deleted): ?>
                                                                        <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit/Approve">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <a href="?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger confirm-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this product?')">
                                                                            <i class="fas fa-trash"></i>
                                                                        </a>
                                                                        <a href="../product.php?id=<?php echo $product['id']; ?>" target="_blank" class="btn btn-sm btn-outline-info" title="View">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <a href="?action=restore&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-success" title="Restore" onclick="return confirm('Are you sure you want to restore this product?')">
                                                                            <i class="fas fa-trash-restore"></i> Restore
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="9" class="text-center">No products found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Pagination -->
                                        <?php if ($total_pages > 1): ?>
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination justify-content-center">
                                                <?php
                                                // Build query string for pagination links
                                                $query_params = $_GET;
                                                unset($query_params['page']);
                                                $query_string = http_build_query($query_params);
                                                $query_string = !empty($query_string) ? '&' . $query_string : '';
                                                ?>
                                                
                                                <li class="page-item <?php if($page_num <= 1) echo 'disabled'; ?>">
                                                    <a class="page-link" href="<?php if($page_num > 1) echo "?page=" . ($page_num - 1) . $query_string; else echo "#"; ?>">Previous</a>
                                                </li>
                                                
                                                <?php
                                                $range = 2;
                                                $start = max(1, $page_num - $range);
                                                $end = min($total_pages, $page_num + $range);
                                                
                                                if ($start > 1) {
                                                    echo '<li class="page-item"><a class="page-link" href="?page=1' . $query_string . '">1</a></li>';
                                                    if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                }
                                                
                                                for ($i = $start; $i <= $end; $i++) {
                                                    $active = ($i == $page_num) ? 'active' : '';
                                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . $query_string . '">' . $i . '</a></li>';
                                                }
                                                
                                                if ($end < $total_pages) {
                                                    if ($end < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . $query_string . '">' . $total_pages . '</a></li>';
                                                }
                                                ?>
                                                
                                                <li class="page-item <?php if($page_num >= $total_pages) echo 'disabled'; ?>">
                                                    <a class="page-link" href="<?php if($page_num < $total_pages) echo "?page=" . ($page_num + 1) . $query_string; else echo "#"; ?>">Next</a>
                                                </li>
                                            </ul>
                                        </nav>
                                        <?php endif; ?>
                                        
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
