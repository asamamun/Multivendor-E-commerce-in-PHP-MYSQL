    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="fas fa-store me-2"></i>MarketPlace
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar -->
                <div class="mx-auto d-flex search-container">
                    <form method="GET" action="shop.php" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search products..." 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Right Side Menu -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="vendors.php">Shops</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php">
                            <i class="fas fa-shopping-cart fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge" style="display: none;">
                                0
                            </span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fs-5"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- show dashboard link only if user is logged in and the user is admin or seller . admin will go to admin dashboard and seller will go to vendor dashboard-->
                            <?php
                                if(isset($_SESSION['user_id'])){
                                    if($_SESSION['user_role'] == 'admin'){
                                        ?>
                                            <li><a class="dropdown-item" href="admin/">Dashboard</a></li>
                                        <?php
                                    }
                                    else if($_SESSION['user_role'] == 'vendor'){
                                        ?>
                                            <li><a class="dropdown-item" href="vendor/">Dashboard</a></li>
                                        <?php
                                    }
                                    else{

                                    }
                                }
                            ?>
                            <?php
                            //if user id vendor profile like will be vendor/profile.php                            
                            if(isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'vendor'){ ?>
                                <li><a class="dropdown-item" href="vendor/profile.php">My Profile</a></li>
                            <?php } ?>
                            <?php
                            //if user id vendor profile like will be vendor/profile.php                            
                            if(isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer'){ ?>
                                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <?php } ?>
                            
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <?php
                                if(isset($_SESSION['user_id'])){
                                    ?>
                                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                    <?php
                                }else{
                                    ?>
                                        <li><a class="dropdown-item" href="register.php">Sign Up</a></li>
                                        <li><a class="dropdown-item" href="login.php">Sign In</a></li>
                                    <?php
                                }
                            ?>
                           
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Cart Badge Update Script -->
    <script>
        // Simple cart badge updater that doesn't conflict with main cart.js
        (function() {
            function updateCartBadge() {
                try {
                    const cartData = localStorage.getItem('marketplace_cart');
                    const cart = cartData ? JSON.parse(cartData) : {};
                    const count = Object.values(cart).reduce((total, item) => total + (item.quantity || 0), 0);
                    
                    const badge = document.querySelector('.cart-badge');
                    if (badge) {
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'inline' : 'none';
                    }
                } catch (error) {
                    console.error('Error updating cart badge:', error);
                }
            }
            
            // Update badge on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', updateCartBadge);
            } else {
                updateCartBadge();
            }
            
            // Listen for storage changes
            window.addEventListener('storage', function(e) {
                if (e.key === 'marketplace_cart') {
                    updateCartBadge();
                }
            });
        })();
    </script>