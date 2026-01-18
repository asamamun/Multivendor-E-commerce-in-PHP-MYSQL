<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
require "inc/cookie.php";
require "db/db.php";

// Fetch categories from database
$categories_sql = "SELECT c1.id, c1.name, c1.slug, c1.parent_id, 
                  (SELECT COUNT(*) FROM products p WHERE p.category_id = c1.id OR p.category_id IN (SELECT id FROM categories WHERE parent_id = c1.id)) as product_count,
                  (SELECT GROUP_CONCAT(c2.id) FROM categories c2 WHERE c2.parent_id = c1.id) as child_ids
                  FROM categories c1 
                  WHERE c1.status = 'active'
                  ORDER BY c1.parent_id ASC, c1.sort_order ASC, c1.name ASC";
$categories_result = $conn->query($categories_sql);
$categories_array = [];
$parent_categories = [];
$child_categories = [];

while($row = $categories_result->fetch_assoc()) {
    if($row['parent_id'] === null || $row['parent_id'] === 0) {
        $parent_categories[$row['id']] = $row;
    } else {
        $child_categories[$row['parent_id']][] = $row;
    }
}
$categories_result->data_seek(0); // Reset pointer for display

// Get current page and category from URL parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build the base query with optional category filter
if($category_filter > 0) {
    // Include products from both parent and child categories
    $where_clause = "WHERE (p.category_id = $category_filter OR p.category_id IN (
        SELECT id FROM categories WHERE parent_id = $category_filter
    ))";
} else {
    $where_clause = "WHERE 1=1"; // Include all products when no category filter
}

// Count total products for pagination
$count_sql = "SELECT COUNT(*) as total FROM products p $where_clause";
$count_result = $conn->query($count_sql);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Fetch products for current page
$products_sql = "SELECT p.*, c.name as category_name, 
                 (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) as primary_image,
                 (SELECT COUNT(*) FROM reviews WHERE product_id = p.id) as review_count,
                 (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) as avg_rating
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 $where_clause
                 ORDER BY p.created_at DESC
                 LIMIT $limit OFFSET $offset";
$products_result = $conn->query($products_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - MarketPlace</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include "inc/navbar.php"; ?>

    <!-- Shop Content -->
    <div class="container my-4">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="filter-sidebar p-3">
                    <h5 class="mb-3">
                        <i class="fas fa-filter me-2 text-primary"></i>Filters
                    </h5>

                    <!-- Categories -->
                    <div class="filter-section">
                        <h6 class="fw-semibold">Categories</h6>
                        <div class="list-group list-group-flush">
                            <?php foreach($parent_categories as $category): ?>
                            <a href="?category=<?php echo $category['id']; ?>&page=1" class="list-group-item list-group-item-action border-0 px-0">
                                <i class="fas fa-tag me-2"></i><?php echo htmlspecialchars($category['name']); ?>
                                <span class="badge bg-light text-dark ms-auto"><?php echo $category['product_count']; ?></span>
                            </a>
                            
                            <!-- Child categories -->
                            <?php if(isset($child_categories[$category['id']])): ?>
                            <div class="ms-3 collapse" id="children_<?php echo $category['id']; ?>">
                                <?php foreach($child_categories[$category['id']] as $child): ?>
                                <a href="?category=<?php echo $child['id']; ?>&page=1" class="list-group-item list-group-item-action border-0 px-0 py-1 small">
                                    <i class="fas fa-angle-right ms-2 me-2"></i><?php echo htmlspecialchars($child['name']); ?>
                                    <span class="badge bg-light text-dark ms-auto"><?php echo $child['product_count']; ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <a href="#children_<?php echo $category['id']; ?>" class="list-group-item list-group-item-action border-0 px-0 small text-muted" data-bs-toggle="collapse">
                                <i class="fas fa-plus me-2"></i>Show subcategories
                            </a>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="filter-section">
                        <h6 class="fw-semibold">Price Range</h6>
                        <div class="mb-3">
                            <label for="priceRange" class="form-label">৳0 - ৳1000</label>
                            <input type="range" class="form-range" id="priceRange" min="0" max="1000" value="500">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" placeholder="Min" value="0">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" placeholder="Max" value="1000">
                            </div>
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="filter-section">
                        <h6 class="fw-semibold">Brands</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="apple">
                            <label class="form-check-label" for="apple">Apple (45)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="samsung">
                            <label class="form-check-label" for="samsung">Samsung (38)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sony">
                            <label class="form-check-label" for="sony">Sony (29)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="nike">
                            <label class="form-check-label" for="nike">Nike (67)</label>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="filter-section">
                        <h6 class="fw-semibold">Customer Rating</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rating5">
                            <label class="form-check-label" for="rating5">
                                <div class="text-warning">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rating4">
                            <label class="form-check-label" for="rating4">
                                <div class="text-warning">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                                    & Up
                                </div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rating3">
                            <label class="form-check-label" for="rating3">
                                <div class="text-warning">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                    & Up
                                </div>
                            </label>
                        </div>
                    </div>

                    <button class="btn btn-outline-primary w-100 mt-3">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Sort and View Options -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">All Products</h4>
                        <p class="text-muted mb-0">Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $total_products); ?> of <?php echo $total_products; ?> results</p>
                    </div>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>Sort by: Popularity</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Customer Rating</option>
                            <option>Newest First</option>
                        </select>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm active">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row g-4">
                    <?php if($products_result->num_rows > 0): ?>
                    <?php while($product = $products_result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card card h-100 shadow-sm" data-aos="fade-up">
                            <div class="position-relative">
                                <a href="product.php?id=<?php echo $product['id']; ?>">
                                    <img src="<?php echo !empty($product['primary_image']) ? $product['primary_image'] : 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=' . urlencode($product['name']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </a>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <button class="btn btn-light btn-sm rounded-circle" onclick="toggleWishlist(<?php echo $product['id']; ?>)">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                                <?php if($product['featured']): ?>
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-danger">Featured</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h6>
                                <p class="text-muted small mb-2"><?php echo !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Uncategorized'; ?></p>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-warning me-2">
                                        <?php 
                                        $avg_rating = $product['avg_rating'] ? round($product['avg_rating']) : 0;
                                        for($i = 1; $i <= 5; $i++):
                                            if($i <= $avg_rating):
                                                echo '<i class="fas fa-star"></i>';
                                            else:
                                                echo '<i class="far fa-star"></i>';
                                            endif;
                                        endfor;
                                        ?>
                                    </div>
                                    <small class="text-muted">(<?php echo $product['review_count']; ?> reviews)</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="h6 text-primary mb-0">৳<?php echo number_format($product['price'], 2); ?></span>
                                        <?php if($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                        <small class="text-muted text-decoration-line-through ms-2">৳<?php echo number_format($product['compare_price'], 2); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <h5>No products found</h5>
                            <p>There are currently no products available in this category.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php
                        $current_page = $page;
                        $total_pages = $total_pages;
                        $adjacents = 2;
                        $category_param = isset($_GET['category']) ? '&category=' . (int)$_GET['category'] : '';
                        
                        // Previous button
                        if($current_page > 1) {
                            $prev_page = $current_page - 1;
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $prev_page . $category_param . '"><i class="fas fa-chevron-left"></i></a></li>';
                        } else {
                            echo '<li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>';
                        }
                        
                        // Pages
                        $start = max(1, $current_page - $adjacents);
                        $end = min($total_pages, $current_page + $adjacents);
                        
                        if($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1' . $category_param . '">1</a></li>';
                            if($start > 2) {
                                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                            }
                        }
                        
                        for($i = $start; $i <= $end; $i++) {
                            if($i == $current_page) {
                                echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . $category_param . '">' . $i . '</a></li>';
                            } else {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $i . $category_param . '">' . $i . '</a></li>';
                            }
                        }
                        
                        if($end < $total_pages) {
                            if($end < $total_pages - 1) {
                                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . $category_param . '">' . $total_pages . '</a></li>';
                        }
                        
                        // Next button
                        if($current_page < $total_pages) {
                            $next_page = $current_page + 1;
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $next_page . $category_param . '"><i class="fas fa-chevron-right"></i></a></li>';
                        } else {
                            echo '<li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/jquery-4.0.0.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Add to cart function
        function addToCart(productId) {
            // Add your cart logic here
            alert('Added to cart: Product ID ' + productId);
        }
        
        // Toggle wishlist function
        function toggleWishlist(productId) {
            // Add your wishlist logic here
            alert('Toggled wishlist: Product ID ' + productId);
        }
    </script>
</body>
</html>