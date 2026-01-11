<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'inc/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold text-dark mb-4">
                        Discover Amazing Products from Multiple Vendors
                    </h1>
                    <p class="lead text-muted mb-4">
                        Shop from thousands of verified sellers and find the best deals on electronics, fashion, home & garden, and more.
                    </p>
                    <a href="shop.html" class="btn btn-primary btn-lg">
                        Start Shopping <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <img src="https://via.placeholder.com/600x400/6c63ff/ffffff?text=E-commerce+Hero" 
                         class="img-fluid rounded shadow" alt="Hero Image">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Shop by Categories</h2>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <i class="fas fa-laptop fs-1 text-primary mb-3"></i>
                        <h5>Electronics</h5>
                        <p class="text-muted">Laptops, Phones, Gadgets</p>
                        <a href="shop.html" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <i class="fas fa-tshirt fs-1 text-primary mb-3"></i>
                        <h5>Fashion</h5>
                        <p class="text-muted">Clothing, Shoes, Accessories</p>
                        <a href="shop.html" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <i class="fas fa-home fs-1 text-primary mb-3"></i>
                        <h5>Home & Garden</h5>
                        <p class="text-muted">Furniture, Decor, Tools</p>
                        <a href="shop.html" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="category-card text-center p-4 rounded shadow-sm h-100">
                        <i class="fas fa-gamepad fs-1 text-primary mb-3"></i>
                        <h5>Sports & Games</h5>
                        <p class="text-muted">Equipment, Toys, Games</p>
                        <a href="shop.html" class="btn btn-outline-primary">Browse</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Featured Products</h2>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="product-card card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Product+1" class="card-img-top" alt="Product">
                        <div class="card-body">
                            <h6 class="card-title">Wireless Headphones</h6>
                            <p class="text-muted small">TechVendor</p>
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <small class="text-muted">(124)</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 text-primary mb-0">$89.99</span>
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Repeat for more products -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="product-card card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Product+2" class="card-img-top" alt="Product">
                        <div class="card-body">
                            <h6 class="card-title">Smart Watch</h6>
                            <p class="text-muted small">GadgetStore</p>
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <small class="text-muted">(89)</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 text-primary mb-0">$199.99</span>
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="product-card card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Product+3" class="card-img-top" alt="Product">
                        <div class="card-body">
                            <h6 class="card-title">Laptop Backpack</h6>
                            <p class="text-muted small">BagWorld</p>
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <small class="text-muted">(67)</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 text-primary mb-0">$49.99</span>
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="product-card card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x200/f8f9fa/6c757d?text=Product+4" class="card-img-top" alt="Product">
                        <div class="card-body">
                            <h6 class="card-title">Gaming Mouse</h6>
                            <p class="text-muted small">GameGear</p>
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <small class="text-muted">(156)</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 text-primary mb-0">$79.99</span>
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="shop.php" class="btn btn-outline-primary btn-lg">View All Products</a>
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
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>