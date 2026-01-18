<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
require "inc/cookie.php";
require "db/db.php";

// Get vendor ID from URL
$vendor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($vendor_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch vendor details
$vendor_sql = "SELECT u.*, vp.* 
               FROM users u
               LEFT JOIN vendor_profiles vp ON u.id = vp.user_id
               WHERE u.id = $vendor_id AND u.role = 'vendor'";
               
$vendor_result = $conn->query($vendor_sql);

if($vendor_result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$vendor = $vendor_result->fetch_assoc();

// Fetch vendor's products
$products_sql = "SELECT p.*, 
                 (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) as primary_image,
                 (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND status = 'approved') as review_count,
                 (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND status = 'approved') as avg_rating
                 FROM products p
                 WHERE p.vendor_id = $vendor_id
                 ORDER BY p.created_at DESC
                 LIMIT 12";
                 
$products_result = $conn->query($products_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(!empty($vendor['store_name']) ? $vendor['store_name'] : $vendor['name']); ?> - MarketPlace</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include "inc/navbar.php"; ?>
    
    <!-- Vendor Header -->
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-5 mb-3">
                            <?php echo htmlspecialchars(!empty($vendor['store_name']) ? $vendor['store_name'] : $vendor['name']); ?>
                        </h1>
                        <?php if(!empty($vendor['store_description'])): ?>
                        <p class="lead"><?php echo htmlspecialchars($vendor['store_description']); ?></p>
                        <?php endif; ?>
                        <div class="d-flex justify-content-center gap-4 mt-3">
                            <div>
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <span class="text-muted">Location: <?php echo !empty($vendor['business_address']) ? htmlspecialchars($vendor['business_address']) : 'Not specified'; ?></span>
                            </div>
                            <div>
                                <i class="fas fa-star text-warning me-2"></i>
                                <span class="text-muted">Rating: <?php echo $vendor['rating'] ? number_format($vendor['rating'], 1) : 'No ratings'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Vendor Products -->
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="mb-4">Products from this vendor</h2>
                
                <?php if($products_result->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while($product = $products_result->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 shadow-sm">
                            <div class="position-relative">
                                <a href="product.php?id=<?php echo $product['id']; ?>">
                                    <img src="<?php echo !empty($product['primary_image']) ? $product['primary_image'] : 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=' . urlencode($product['name']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         style="height: 200px; object-fit: cover;">
                                </a>
                                <?php if($product['featured']): ?>
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-danger">Featured</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars(substr($product['name'], 0, 50)); ?><?php echo strlen($product['name']) > 50 ? '...' : ''; ?>
                                    </a>
                                </h6>
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
                                    <small class="text-muted">(<?php echo $product['review_count']; ?>)</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 text-primary mb-0">৳<?php echo number_format($product['price'], 2); ?></span>
                                    <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No products available</h4>
                    <p class="text-muted">This vendor hasn't added any products yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add to cart function
        function addToCart(productId) {
            // Add your cart logic here
            alert('Added to cart: Product ID ' + productId);
        }
    </script>
</body>
</html>