<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
require "inc/cookie.php";
require "db/db.php";

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($product_id <= 0) {
    header("Location: shop.php");
    exit();
}

// Fetch product details
$product_sql = "SELECT p.*, c.name as category_name, u.name as vendor_name, vp.store_name,
                (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND status = 'approved') as review_count,
                (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND status = 'approved') as avg_rating
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.vendor_id = u.id
                LEFT JOIN vendor_profiles vp ON u.id = vp.user_id
                WHERE p.id = $product_id";
                
$product_result = $conn->query($product_sql);

if($product_result->num_rows == 0) {
    header("Location: shop.php");
    exit();
}

$product = $product_result->fetch_assoc();

// Fetch product images
$images_sql = "SELECT * FROM product_images WHERE product_id = $product_id ORDER BY sort_order ASC, is_primary DESC";
$images_result = $conn->query($images_sql);
$images = [];
while($img = $images_result->fetch_assoc()) {
    $images[] = $img;
}

// Fetch reviews
$reviews_sql = "SELECT r.*, u.name as customer_name 
                FROM reviews r
                LEFT JOIN users u ON r.customer_id = u.id
                WHERE r.product_id = $product_id AND r.status = 'approved'
                ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_sql);
$reviews = [];
while($review = $reviews_result->fetch_assoc()) {
    $reviews[] = $review;
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : 0;

// Check if user already reviewed this product
$has_reviewed = false;
if($is_logged_in) {
    $review_check_sql = "SELECT id FROM reviews WHERE product_id = $product_id AND customer_id = $user_id";
    $review_check_result = $conn->query($review_check_sql);
    $has_reviewed = $review_check_result->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - MarketPlace</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Zoom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easyzoom@2.6.0/css/easyzoom.css">
    
    <!-- Lightbox2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    
        
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .product-image-container {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
        }
        
        .thumbnail-container {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            overflow-x: auto;
        }
        
        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .thumbnail:hover, .thumbnail.active {
            border-color: #0d6efd;
        }
        
        .review-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .star-rating {
            direction: rtl;
            unicode-bidi: bidi-override;
            color: #ddd;
        }
        
        .star-rating input[type=radio] {
            display: none;
        }
        
        .star-rating label {
            color: #ddd;
            font-size: 24px;
            padding: 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input[type=radio]:checked ~ label {
            color: #ffc107;
        }
        
        .review-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include "inc/navbar.php"; ?>
    
    <!-- Product Details Section -->
    <div class="container my-5">
        <div class="row" data-aos="fade-up">
            <!-- Product Images Column -->
            <div class="col-lg-6 mb-4">
                <div class="product-image-container" data-aos="zoom-in">
                    <div class="easyzoom easyzoom--overlay">
                        <a href="<?php echo !empty($images[0]['image_path']) ? $images[0]['image_path'] : 'https://via.placeholder.com/500x500/f8f9fa/6c757d?text=' . urlencode($product['name']); ?>" data-lightbox="product-gallery" data-title="<?php echo htmlspecialchars($product['name']); ?>">
                            <img id="mainImage" src="<?php echo !empty($images[0]['image_path']) ? $images[0]['image_path'] : 'https://via.placeholder.com/500x500/f8f9fa/6c757d?text=' . urlencode($product['name']); ?>" 
                                 class="img-fluid rounded" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                    </div>
                    
                    <!-- Thumbnails -->
                    <div class="thumbnail-container">
                        <?php foreach($images as $index => $image): ?>
                        <a href="<?php echo $image['image_path']; ?>" data-lightbox="product-gallery" data-title="<?php echo htmlspecialchars($product['name']); ?>">
                            <img src="<?php echo $image['image_path']; ?>" 
                                 class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 onclick="changeMainImage('<?php echo $image['image_path']; ?>', this)"
                                 alt="<?php echo htmlspecialchars($product['name']); ?> thumbnail">
                        </a>
                        <?php endforeach; ?>
                        
                        <?php if(empty($images)): ?>
                        <a href="https://via.placeholder.com/500x500/f8f9fa/6c757d?text=<?php echo urlencode($product['name']); ?>" data-lightbox="product-gallery" data-title="<?php echo htmlspecialchars($product['name']); ?>">
                            <img src="https://via.placeholder.com/80x80/f8f9fa/6c757d?text=No+Image" 
                                 class="thumbnail active" 
                                 alt="No image available">
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Product Info Column -->
            <div class="col-lg-6">
                <div data-aos="fade-left">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                            <li class="breadcrumb-item"><a href="shop.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
                        </ol>
                    </nav>
                    
                    <h1 class="display-6 mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <!-- Vendor Info -->
                    <div class="d-flex align-items-center mb-3">
                        <span class="text-muted me-2">Sold by:</span>
                        <a href="vendor.php?id=<?php echo $product['vendor_id']; ?>" class="text-decoration-none">
                            <strong><?php echo htmlspecialchars(!empty($product['store_name']) ? $product['store_name'] : $product['vendor_name']); ?></strong>
                        </a>
                    </div>
                    
                    <!-- Rating -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-warning me-2">
                            <?php 
                            $avg_rating = $product['avg_rating'] ? round($product['avg_rating'], 1) : 0;
                            $full_stars = floor($avg_rating);
                            $half_star = ($avg_rating - $full_stars) >= 0.5;
                            
                            for($i = 1; $i <= 5; $i++):
                                if($i <= $full_stars):
                                    echo '<i class="fas fa-star"></i>';
                                elseif($i == $full_stars + 1 && $half_star):
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                else:
                                    echo '<i class="far fa-star"></i>';
                                endif;
                            endfor;
                            ?>
                        </div>
                        <span class="text-muted">(<?php echo $product['review_count']; ?> reviews)</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="mb-4">
                        <span class="h3 text-primary">৳<?php echo number_format($product['price'], 2); ?></span>
                        <?php if($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                        <span class="h5 text-muted text-decoration-line-through ms-2">৳<?php echo number_format($product['compare_price'], 2); ?></span>
                        <span class="badge bg-danger ms-2">Save ৳<?php echo number_format($product['compare_price'] - $product['price'], 2); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p><?php echo htmlspecialchars($product['description'] ?? $product['short_description'] ?? 'No description available'); ?></p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-4">
                        <button class="btn btn-primary btn-lg me-md-2" onclick="addToCart(<?php echo $product['id']; ?>)">
                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                        <button class="btn btn-outline-danger btn-lg" onclick="toggleWishlist(<?php echo $product['id']; ?>)">
                            <i class="far fa-heart me-2"></i>Wishlist
                        </button>
                    </div>
                    
                    <!-- Product Meta -->
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">SKU:</small>
                                <p><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Stock:</small>
                                <p><?php echo $product['stock_quantity'] > 0 ? $product['stock_quantity'] . ' in stock' : 'Out of stock'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div data-aos="fade-up">
                    <h3 class="mb-4">Customer Reviews (<?php echo count($reviews); ?>)</h3>
                    
                    <!-- Review Form (for logged in users who haven't reviewed) -->
                    <?php 
                    // Check if user is logged in and is a customer
                    $show_review_form = false;
                    $already_reviewed = false;
                    
                    if($is_logged_in && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer') {
                        $show_review_form = true;
                        $already_reviewed = $has_reviewed;
                    }
                    ?>
                    <?php if($show_review_form && !$already_reviewed): ?>
                    <div class="review-form">
                        <h5>Write a Review</h5>
                        <form id="reviewForm">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="rating" value="5" />
                                    <label for="star5" title="5 stars">&#9733;</label>
                                    <input type="radio" id="star4" name="rating" value="4" />
                                    <label for="star4" title="4 stars">&#9733;</label>
                                    <input type="radio" id="star3" name="rating" value="3" />
                                    <label for="star3" title="3 stars">&#9733;</label>
                                    <input type="radio" id="star2" name="rating" value="2" />
                                    <label for="star2" title="2 stars">&#9733;</label>
                                    <input type="radio" id="star1" name="rating" value="1" />
                                    <label for="star1" title="1 star">&#9733;</label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="reviewTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="reviewTitle" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="reviewComment" class="form-label">Review</label>
                                <textarea class="form-control" id="reviewComment" name="comment" rows="4" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                    <?php elseif(!$is_logged_in): ?>
                    <div class="alert alert-info">
                        <a href="login.php" class="alert-link">Login</a> to write a review.
                    </div>
                    <?php elseif($show_review_form && $already_reviewed): ?>
                    <div class="alert alert-success">
                        You have already reviewed this product.
                    </div>
                    <?php elseif($is_logged_in && !$show_review_form): ?>
                    <div class="alert alert-info">
                        Only customers can submit reviews.
                    </div>
                    <?php endif; ?>
                    
                    <!-- Reviews List -->
                    <div class="mt-4">
                        <?php if(!empty($reviews)): ?>
                        <?php foreach($reviews as $review): ?>
                        <div class="review-card p-3 bg-white rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6><?php echo htmlspecialchars($review['title'] ?? 'No title'); ?></h6>
                                    <div class="text-warning mb-2">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <?php if($i <= $review['rating']): ?>
                                                <i class="fas fa-star"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($review['comment']); ?></p>
                                    <small class="text-muted">by <?php echo htmlspecialchars($review['customer_name']); ?> 
                                    on <?php echo date('M j, Y', strtotime($review['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comment-dots fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- EasyZoom for Image Zoom -->
    <script src="https://cdn.jsdelivr.net/npm/easyzoom@2.6.0/dist/easyzoom.js"></script>
    
    <!-- Lightbox2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    
    <script>
        // Global variable for easyZoom API
        var api;
        
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Initialize EasyZoom when DOM is ready
        $(document).ready(function() {
            console.log('jQuery loaded:', typeof $ !== 'undefined');
            console.log('EasyZoom available:', typeof $.fn.easyZoom !== 'undefined');
            
            // Check if easyZoom is available
            if (typeof $.fn.easyZoom !== 'undefined') {
                var $easyzoom = $('.easyzoom').easyZoom();
                api = $easyzoom.data('easyZoom');
                console.log('EasyZoom initialized successfully');
            } else {
                console.error('EasyZoom library not loaded');
                // Fallback: try to load from alternative CDN
                $.getScript('https://unpkg.com/easyzoom@2.6.0/dist/easyzoom.js', function() {
                    console.log('EasyZoom loaded from fallback CDN');
                    var $easyzoom = $('.easyzoom').easyZoom();
                    api = $easyzoom.data('easyZoom');
                });
            }
            
            // Configure lightbox
            if (typeof lightbox !== 'undefined') {
                lightbox.option({
                    'albumLabel': 'Image %1 of %2',
                    'wrapAround': true
                });
            }
        });
        
        // Change main image when thumbnail is clicked
        function changeMainImage(imageSrc, clickedElement) {
            document.getElementById('mainImage').src = imageSrc;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            clickedElement.classList.add('active');
            
            // Update zoom if API is available
            if (api && typeof api.swap === 'function') {
                api.swap(imageSrc, imageSrc);
            }
        }
        
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
        
        // Review form submission
        document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if the user is a customer before submitting
            fetch('submit_review.php', {
                method: 'POST',
                body: new FormData(document.getElementById('reviewForm'))
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Review submitted successfully!');
                    location.reload();
                } else {
                    alert('Error submitting review: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting review');
            });
        });
    </script>
</body>
</html>