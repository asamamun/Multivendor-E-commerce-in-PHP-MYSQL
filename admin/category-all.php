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

// Fetch all categories with full parent chain
$sql = "SELECT c.id, c.name, c.slug, c.description, c.parent_id, c.image, c.status, 
               p1.name as parent_name, p2.name as grandparent_name
         FROM categories c 
         LEFT JOIN categories p1 ON c.parent_id = p1.id
         LEFT JOIN categories p2 ON p1.parent_id = p2.id
         ORDER BY COALESCE(c.parent_id, c.id), c.name";
$result = $conn->query($sql);
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Categories are displayed directly without grouping

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
                                                    <?php foreach ($categories as $category): ?>
                                                    <tr>
                                                        <td><?php echo $category['id']; ?></td>
                                                        <td>
                                                            <?php 
                                                            // Show hierarchy with proper indentation
                                                            if (!is_null($category['parent_id'])) {
                                                                if (!is_null($category['grandparent_name'])) {
                                                                    // Level 3 category
                                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└ ';
                                                                } else {
                                                                    // Level 2 category
                                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;└ ';
                                                                }
                                                            }
                                                            echo htmlspecialchars($category['name']); 
                                                            ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                                        <td><?php echo htmlspecialchars(substr($category['description'], 0, 50)) . (strlen($category['description']) > 50 ? '...' : ''); ?></td>
                                                        <td>
                                                            <?php 
                                                            if (!is_null($category['parent_id'])) {
                                                                if (!is_null($category['grandparent_name'])) {
                                                                    // Show grandparent → parent format for level 3
                                                                    echo htmlspecialchars($category['grandparent_name'] . ' → ' . $category['parent_name']);
                                                                } else {
                                                                    // Show parent name for level 2
                                                                    echo htmlspecialchars($category['parent_name']);
                                                                }
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
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
