<?php
$page = 'All Categories';
$description = 'Manage all categories page';
$author = 'ASA Al-Mamun';
$title = 'All Categories';

// Include database connection
include_once "../db/db.php";

$message = "";
$messageType = "";

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check if category has child categories or products
    $check_children = "SELECT COUNT(*) as count FROM categories WHERE parent_id = ?";
    $check_products = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
    
    $stmt1 = $conn->prepare($check_children);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $children_count = $result1->fetch_assoc()['count'];
    
    $stmt2 = $conn->prepare($check_products);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $products_count = $result2->fetch_assoc()['count'];
    
    if ($children_count > 0 || $products_count > 0) {
        $message = "Cannot delete category because it has child categories or products associated with it.";
        $messageType = "danger";
    } else {
        // Proceed with deletion
        $delete_sql = "DELETE FROM categories WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            $message = "Category deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting category: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Fetch all categories with parent names
$sql = "SELECT c.id, c.name, c.slug, c.description, c.parent_id, c.image, c.status, p.name as parent_name 
         FROM categories c 
         LEFT JOIN categories p ON c.parent_id = p.id 
         ORDER BY c.parent_id, c.name";
$result = $conn->query($sql);
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Group categories by parent for better display
$categorized = [];
foreach ($categories as $cat) {
    if (is_null($cat['parent_id'])) {
        $categorized['root'][] = $cat;
    } else {
        $categorized['sub'][$cat['parent_id']][] = $cat;
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
                        <h1 class="mt-4">All Categories</h1>
                        <ol class="breadcrumb mb-4">                            
                            <li class="breadcrumb-item active">Dashboard / All Categories / <a href="category-add.php">Add Category</a></li>
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
                                        <i class="fas fa-list me-1"></i>
                                        All Categories
                                        <a href="category-add.php" class="btn btn-primary float-end">
                                            <i class="fas fa-plus"></i> Add New
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="datatablesSimple">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Slug</th>
                                                        <th>Description</th>
                                                        <th>Parent Category</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($categorized['root'] ?? [] as $category): ?>
                                                    <tr>
                                                        <td><?php echo $category['id']; ?></td>
                                                        <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                                        <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                                        <td><?php echo htmlspecialchars(substr($category['description'], 0, 50)) . (strlen($category['description']) > 50 ? '...' : ''); ?></td>
                                                        <td>-</td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $category['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                                <?php echo ucfirst($category['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="category-edit.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="?action=delete&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-danger confirm-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this category?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php 
                                                    // Show subcategories under this root category
                                                    if (isset($categorized['sub'][$category['id']])) {
                                                        foreach ($categorized['sub'][$category['id']] as $subcategory):
                                                    ?> 
                                                    <tr>
                                                        <td><?php echo $subcategory['id']; ?></td>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;└ <?php echo htmlspecialchars($subcategory['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($subcategory['slug']); ?></td>
                                                        <td><?php echo htmlspecialchars(substr($subcategory['description'], 0, 50)) . (strlen($subcategory['description']) > 50 ? '...' : ''); ?></td>
                                                        <td><?php echo htmlspecialchars($subcategory['parent_name']); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $subcategory['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                                <?php echo ucfirst($subcategory['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="category-edit.php?id=<?php echo $subcategory['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="?action=delete&id=<?php echo $subcategory['id']; ?>" class="btn btn-sm btn-outline-danger confirm-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this category?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; } ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
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
