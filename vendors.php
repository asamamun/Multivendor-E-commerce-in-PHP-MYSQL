<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
require "inc/cookie.php";
require "db/db.php";

// Fetch all active vendors
$vendors_sql = "SELECT u.id, u.name, u.email, vp.store_name, vp.store_logo, vp.store_description, vp.rating, vp.business_address,
                (SELECT COUNT(*) FROM products WHERE vendor_id = u.id AND status = 'active' AND deleted_at IS NULL) as product_count
                FROM users u
                JOIN vendor_profiles vp ON u.id = vp.user_id
                WHERE u.role = 'vendor' AND u.status = 'active'
                ORDER BY vp.rating DESC, product_count DESC";

$vendors_result = $conn->query($vendors_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Vendors - MarketPlace</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .vendor-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
        }
        .vendor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        }
        .vendor-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 4px solid #fff;
            margin-top: -50px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include "inc/navbar.php"; ?>

    <div class="bg-primary text-white py-5 mb-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Our Trusted Vendors</h1>
            <p class="lead">Discover quality products from verified sellers</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4">
            <?php if ($vendors_result->num_rows > 0): ?>
                <?php while ($v = $vendors_result->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card vendor-card h-100 border-0 shadow-sm">
                        <div class="bg-light" style="height: 100px;"></div>
                        <div class="card-body text-center pt-0">
                            <?php if ($v['store_logo']): ?>
                                <img src="assets/uploads/vendor/<?php echo htmlspecialchars($v['store_logo']); ?>" 
                                     class="vendor-logo rounded-circle shadow-sm" alt="<?php echo htmlspecialchars($v['store_name']); ?>">
                            <?php else: ?>
                                <div class="vendor-logo rounded-circle shadow-sm bg-white d-flex align-items-center justify-content-center mx-auto text-primary fw-bold fs-2">
                                    <?php echo strtoupper(substr($v['store_name'] ?? $v['name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>

                            <h4 class="mt-3 mb-1 fw-bold"><?php echo htmlspecialchars($v['store_name'] ?? $v['name']); ?></h4>
                            <div class="text-warning mb-2">
                                <?php 
                                $rating = round($v['rating']);
                                for($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                }
                                ?>
                                <span class="text-muted small ms-1">(<?php echo number_format($v['rating'], 1); ?>)</span>
                            </div>
                            
                            <p class="text-muted small mb-3 text-truncate-2" style="height: 3rem; overflow: hidden;">
                                <?php echo htmlspecialchars($v['store_description'] ?? 'No description available.'); ?>
                            </p>

                            <div class="d-flex justify-content-center gap-3 mb-4">
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-box me-1"></i> <?php echo $v['product_count']; ?> Products
                                </span>
                                <?php if ($v['business_address']): ?>
                                <span class="badge bg-light text-dark text-truncate" style="max-width: 15rem;">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i> <?php echo htmlspecialchars($v['business_address']); ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <a href="vendor.php?id=<?php echo $v['id']; ?>" class="btn btn-primary w-100 rounded-pill">
                                Visit Store <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-store-slash fa-4x text-muted mb-3"></i>
                    <h3>No Vendors Found</h3>
                    <p class="text-muted">We currently don't have any registered vendors.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include "inc/footer.php"; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
