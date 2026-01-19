<?php
$page = 'All Brands';
$description = 'Manage all brands page';
$author = 'ASA Al-Mamun';
$title = 'All Brands';

// Include database connection
include_once "../db/db.php";

$message = "";
$messageType = "";

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check if brand is associated with any products
    $check_products = "SELECT COUNT(*) as count FROM products WHERE brand_id = ?";
    $stmt1 = $conn->prepare($check_products);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $products_count = $result1->fetch_assoc()['count'];
    
    if ($products_count > 0) {
        $message = "Cannot delete brand because it is associated with " . $products_count . " product(s).";
        $messageType = "danger";
    } else {
        // Get brand image to delete file
        $get_img = "SELECT image FROM brands WHERE id = ?";
        $stmt_img = $conn->prepare($get_img);
        $stmt_img->bind_param("i", $id);
        $stmt_img->execute();
        $res_img = $stmt_img->get_result();
        $brand_data = $res_img->fetch_assoc();

        // Proceed with deletion
        $delete_sql = "DELETE FROM brands WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            // Delete image file if exists
            if ($brand_data && !empty($brand_data['image']) && file_exists("../" . $brand_data['image'])) {
                unlink("../" . $brand_data['image']);
            }
            $message = "Brand deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting brand: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Pagination Logic
$limit = 10; // Records per page
$page_num = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page_num < 1) $page_num = 1;
$offset = ($page_num - 1) * $limit;

// Count total brands
$count_sql = "SELECT COUNT(*) as total FROM brands";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch brands for current page
$sql = "SELECT * FROM brands ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
$brands = [];
while ($row = $result->fetch_assoc()) {
    $brands[] = $row;
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
                        <h1 class="mt-4">All Brands</h1>
                        <ol class="breadcrumb mb-4">                            
                            <li class="breadcrumb-item active">Dashboard / All Brands / <a href="brand-add.php">Add Brand</a></li>
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
                                        All Brands (Total: <?php echo $total_rows; ?>)
                                        <a href="brand-add.php" class="btn btn-primary float-end">
                                            <i class="fas fa-plus"></i> Add New
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <!-- Note: Using ID 'brandsTable' to avoid conflict with simple-datatables auto-init if present on #datatablesSimple -->
                                            <table class="table table-bordered table-striped" id="brandsTable">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Image</th>
                                                        <th>Name</th>
                                                        <th>Slug</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (count($brands) > 0): ?>
                                                        <?php foreach ($brands as $brand): ?>
                                                        <tr>
                                                            <td><?php echo $brand['id']; ?></td>
                                                            <td>
                                                                <?php if (!empty($brand['image'])): ?>
                                                                    <img src="../<?php echo htmlspecialchars($brand['image']); ?>" alt="<?php echo htmlspecialchars($brand['name']); ?>" width="50">
                                                                <?php else: ?>
                                                                    <span>No Image</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($brand['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($brand['slug']); ?></td>
                                                            <td>
                                                                <span class="badge bg-<?php echo $brand['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                                    <?php echo ucfirst($brand['status']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="brand-edit.php?id=<?php echo $brand['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="?action=delete&id=<?php echo $brand['id']; ?>" class="btn btn-sm btn-outline-danger confirm-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this brand?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No brands found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Pagination -->
                                        <?php if ($total_pages > 1): ?>
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination justify-content-center">
                                                <!-- Previous Link -->
                                                <li class="page-item <?php if($page_num <= 1) echo 'disabled'; ?>">
                                                    <a class="page-link" href="<?php if($page_num > 1) echo "?page=" . ($page_num - 1); else echo "#"; ?>">Previous</a>
                                                </li>
                                                
                                                <!-- Page Numbers -->
                                                <?php
                                                // Simple pagination range logic
                                                $range = 2; // Number of pages around current page
                                                $start = max(1, $page_num - $range);
                                                $end = min($total_pages, $page_num + $range);
                                                
                                                if ($start > 1) {
                                                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                                                    if ($start > 2) {
                                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                    }
                                                }
                                                
                                                for ($i = $start; $i <= $end; $i++) {
                                                    $active = ($i == $page_num) ? 'active' : '';
                                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                                }
                                                
                                                if ($end < $total_pages) {
                                                    if ($end < $total_pages - 1) {
                                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                    }
                                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                                }
                                                ?>
                                                
                                                <!-- Next Link -->
                                                <li class="page-item <?php if($page_num >= $total_pages) echo 'disabled'; ?>">
                                                    <a class="page-link" href="<?php if($page_num < $total_pages) echo "?page=" . ($page_num + 1); else echo "#"; ?>">Next</a>
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
