<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// Include database connection
include_once "db/db.php";

// Fetch active categories with images (limit to top 10)
$categories_sql = "SELECT id, name, description, slug, image FROM categories where status='active' ";
$categories_result = $conn->query($categories_sql);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch featured products
$featured_sql = "SELECT p.*, 
                 u.name as vendor_name, 
                 vp.store_name,
                 (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) as image_path,
                 (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND status = 'approved') as review_count,
                 (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND status = 'approved') as avg_rating
                 FROM products p
                 LEFT JOIN users u ON p.vendor_id = u.id
                 LEFT JOIN vendor_profiles vp ON u.id = vp.user_id
                 WHERE p.status = 'active' AND p.featured = 1 AND p.deleted_at IS NULL
                 ORDER BY p.id DESC";
$featured_result = $conn->query($featured_sql);
$featured_products = [];

if ($featured_result) {
    while ($row = $featured_result->fetch_assoc()) {
        $featured_products[] = $row;
    }
} else {
    // Fallback or debug: Log error if needed, for now just empty array
    // error_log("Featured products query failed: " . $conn->error);
}
// Fetch top vendors
$vendors_sql = "SELECT u.id, u.name, vp.store_name, vp.store_logo 
                FROM users u
                JOIN vendor_profiles vp ON u.id = vp.user_id
                WHERE u.role = 'vendor' AND u.status = 'active'
                LIMIT 6";
$vendors_result = $conn->query($vendors_sql);
$top_vendors = [];
if ($vendors_result) {
    while ($row = $vendors_result->fetch_assoc()) {
        $top_vendors[] = $row;
    }
}
/* var_dump($categories);
exit; */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPlace - Multi-Vendor E-commerce</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .hero-swiper {
            width: 100%;
            height: 500px;
        }
        .swiper-slide {
            position: relative;
            overflow: hidden;
        }
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-content-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
            display: flex;
            align-items: center;
            z-index: 2;
        }
        .hero-text-box {
            max-width: 600px;
            padding: 40px;
            color: white;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .swiper-slide-active .hero-text-box {
            opacity: 1;
            transform: translateY(0);
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .hero-desc {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
        }
        .btn-premium {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-premium:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: #fff;
            opacity: 0.5;
        }
        .swiper-pagination-bullet-active {
            opacity: 1;
            width: 30px;
            border-radius: 6px;
        }
        .swiper-button-next, .swiper-button-prev {
            color: #fff;
            background: rgba(255,255,255,0.1);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            backdrop-filter: blur(5px);
        }
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 20px;
        }
        @media (max-width: 768px) {
            .hero-swiper { height: 400px; }
            .hero-title { font-size: 2rem; }
            .hero-desc { font-size: 1rem; }
            .hero-content-wrapper { background: rgba(0,0,0,0.5); }
        }
    </style>
</head>
<body>
<?php include 'inc/navbar.php'; ?>

    <!-- Hero Section (Swiper Carousel) -->
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper" id="hero-carousel-wrapper">
            <!-- Slides will be loaded dynamically -->
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
    <!-- Hero Section end -->

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Shop by Categories</h2>
            
            <?php if (!empty($categories)): ?>
            <div class="categories-carousel owl-carousel owl-theme" data-aos="fade-up" data-aos-delay="200">
                <?php foreach ($categories as $category): ?>
                <div class="item">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <?php if ($category['image']): ?>
                            <div class="category-image mb-3">
                                <img src="<?php echo htmlspecialchars($category['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                     class="img-fluid rounded-circle" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <i class="fas fa-tag fs-1 text-primary mb-3"></i>
                        <?php endif; ?>
                        <h5><?php echo htmlspecialchars($category['name']); ?></h5>
                        <?php if ($category['description']): ?>
                            <p class="text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                        <?php endif; ?>
                        <a href="shop.php?category=<?php echo urlencode($category['slug']); ?>" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Fallback static categories if no categories in database -->
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <i class="fas fa-laptop fs-1 text-primary mb-3"></i>
                        <h5>Electronics</h5>
                        <p class="text-muted">Laptops, Phones, Gadgets</p>
                        <a href="shop.php" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <i class="fas fa-tshirt fs-1 text-primary mb-3"></i>
                        <h5>Fashion</h5>
                        <p class="text-muted">Clothing, Shoes, Accessories</p>
                        <a href="shop.php" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <i class="fas fa-home fs-1 text-primary mb-3"></i>
                        <h5>Home & Garden</h5>
                        <p class="text-muted">Furniture, Decor, Tools</p>
                        <a href="shop.php" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <i class="fas fa-gamepad fs-1 text-primary mb-3"></i>
                        <h5>Sports & Games</h5>
                        <p class="text-muted">Equipment, Toys, Games</p>
                        <a href="shop.php" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Featured Products</h2>
            
            <?php if (!empty($featured_products)): ?>
            <div class="featured-carousel owl-carousel owl-theme" data-aos="fade-up" data-aos-delay="100">
                <?php foreach ($featured_products as $product): ?>
                <div class="item">
                    <div class="product-card card h-100 shadow-sm">
                        <div class="position-relative">
                            <a href="product.php?id=<?php echo $product['id']; ?>">
                                <img src="<?php echo !empty($product['image_path']) ? $product['image_path'] : 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=' . urlencode($product['name']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     style="height: 200px; object-fit: cover;">
                            </a>
                            <?php if($product['compare_price'] > $product['price']): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title text-truncate">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h6>
                            <p class="text-muted small mb-2">
                                <a href="vendor.php?id=<?php echo $product['vendor_id']; ?>" class="text-decoration-none text-muted">
                                    <i class="fas fa-store me-1"></i><?php echo htmlspecialchars(!empty($product['store_name']) ? $product['store_name'] : $product['vendor_name']); ?>
                                </a>
                            </p>
                            
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-warning me-2 small">
                                    <?php 
                                    $rating = round($product['avg_rating'] ?? 0);
                                    for($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                </div>
                                <small class="text-muted">(<?php echo $product['review_count'] ?? 0; ?>)</small>
                            </div>
                            
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h6 text-primary mb-0">৳<?php echo number_format($product['price'], 2); ?></span>
                                    <?php if($product['compare_price'] > $product['price']): ?>
                                        <small class="text-muted text-decoration-line-through ms-1">৳<?php echo number_format($product['compare_price'], 2); ?></small>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-outline-primary btn-sm rounded-circle" onclick="addToCart(<?php echo $product['id']; ?>)" title="Add to Cart">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">No featured products available at the moment.</p>
                </div>
            <?php endif; ?>
            <div class="text-center mt-5">
                <a href="shop.php" class="btn btn-outline-primary btn-lg">View All Products</a>
            </div>
        </div>
    </section>
    
    <!-- Top Vendors Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Our Top Vendors</h2>
            <div class="row g-4">
                <?php foreach ($top_vendors as $v): ?>
                <div class="col-lg-2 col-md-4 col-6" data-aos="zoom-in">
                    <a href="vendor.php?id=<?php echo $v['id']; ?>" class="text-decoration-none text-center d-block">
                        <div class="card border-0 shadow-sm h-100 hover-elevate">
                            <div class="card-body">
                                <div class="mb-3 mx-auto" style="width: 80px; height: 80px;">
                                    <?php if ($v['store_logo']): ?>
                                        <img src="assets/uploads/vendor/<?php echo htmlspecialchars($v['store_logo']); ?>" 
                                             class="img-fluid rounded-circle shadow-sm w-100 h-100" style="object-fit: cover;"
                                             alt="<?php echo htmlspecialchars($v['store_name']); ?>">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center w-100 h-100 text-primary fw-bold">
                                            <?php echo strtoupper(substr($v['store_name'] ?? $v['name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <h6 class="text-dark mb-0"><?php echo htmlspecialchars($v['store_name'] ?? $v['name']); ?></h6>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="vendors.php" class="btn btn-outline-primary btn-lg">View All Shops</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3">
                        <i class="fas fa-store me-2"></i>MarketPlace
                    </h5>
                    <p class="text-muted">
                        Your trusted multi-vendor e-commerce platform connecting buyers with quality sellers worldwide.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">FAQ</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Support</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Electronics</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Fashion</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Home & Garden</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Sports</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="mb-3">Account</h6>
                    <ul class="list-unstyled">
                        <?php if(isset($_SESSION['user_id'])){ ?>
                            <li><a href="logout.php" class="text-muted text-decoration-none">Logout</a></li>
                        <?php }else{ ?>
                            <li><a href="login.php" class="text-muted text-decoration-none">Login</a></li>
                            <li><a href="register.php" class="text-muted text-decoration-none">Register</a></li>
                        <?php } ?>
                        <!-- <li><a href="login.php" class="text-muted text-decoration-none">Login</a></li>
                        <li><a href="register.html" class="text-muted text-decoration-none">Register</a></li> -->
                        <li><a href="profile.html" class="text-muted text-decoration-none">My Account</a></li>
                        <li><a href="orders.html" class="text-muted text-decoration-none">Orders</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="mb-3">Policies</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Return Policy</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Shipping Info</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0 text-muted">&copy; 2024 MarketPlace. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (required for Owl Carousel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Fetch Carousel Data
        fetch('assets/js/carousel.json')
            .then(response => response.json())
            .then(data => {
                const wrapper = document.getElementById('hero-carousel-wrapper');
                data.forEach(slide => {
                    const slideHTML = `
                        <div class="swiper-slide">
                            <img src="${slide.image}" alt="${slide.title}">
                            <div class="hero-content-wrapper">
                                <div class="container">
                                    <div class="hero-text-box">
                                        <h1 class="hero-title">${slide.title}</h1>
                                        <p class="hero-desc">${slide.description}</p>
                                        <a href="${slide.link}" class="btn btn-primary btn-premium">
                                            Shop Now <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    wrapper.innerHTML += slideHTML;
                });

                // Initialize Swiper after data is loaded
                new Swiper(".hero-swiper", {
                    loop: true,
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                });
            })
            .catch(error => console.error('Error loading carousel:', error));

        // Initialize Categories Carousel
        $(document).ready(function(){
            $('.categories-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplayHoverPause: true,
                navText: [
                    '<i class="fas fa-chevron-left"></i>',
                    '<i class="fas fa-chevron-right"></i>'
                ],
                responsive: {
                    0: {
                        items: 1,
                        nav: false
                    },
                    576: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 4
                    },
                    1200: {
                        items: 5
                    }
                }
            });

            // Initialize Featured Products Carousel
            $('.featured-carousel').owlCarousel({
                loop: false,
                margin: 20,
                nav: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                rewind: true,
                navText: [
                    '<i class="fas fa-chevron-left"></i>',
                    '<i class="fas fa-chevron-right"></i>'
                ],
                responsive: {
                    0: {
                        items: 1,
                        nav: false
                    },
                    576: {
                        items: 2
                    },
                    768: {
                        items: 2
                    },
                    992: {
                        items: 3
                    },
                    1200: {
                        items: 4
                    }
                }
            });
        });
    </script>
</body>
</html>