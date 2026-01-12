<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
//if session has login info, redirect to index or vendor or admin page
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role'] == 'customer'){
        header("Location: index.php");
        exit;
    }
    if($_SESSION['user_role'] == 'vendor'){
        header("Location: vendor/");
        exit;
    }
    if($_SESSION['user_role'] == 'admin'){
        header("Location: admin/");
        exit;
    }
}

require "inc/cookie.php";
require "db/db.php";

//if user has submitted login form
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = $_POST['remember']??false;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            if($remember){
                // Set cookies with proper domain, path, and security settings
                setcookie('user_id', $user['id'], time() + (86400 * 30), "/", '', false, true);
                setcookie('user_name', $user['name'], time() + (86400 * 30), "/", '', false, true);
                setcookie('user_email', $user['email'], time() + (86400 * 30), "/", '', false, true);
                setcookie('user_role', $user['role'], time() + (86400 * 30), "/", '', false, true);
            }
            // Only redirect after setting cookies
            if($user['role'] == 'customer'){
                header("Location: index.php");
                exit;
            }
            if($user['role'] == 'vendor'){
                // Check if vendor directory exists, otherwise redirect to index
                if(is_dir('vendor/')) {
                    header("Location: vendor/");
                } else {
                    header("Location: index.php");
                }
                exit;
            }
            if($user['role'] == 'admin'){
                header("Location: admin/");
                exit;
            }
            
        }else{
            //dismissible alert in error variable
            $error = "Incorrect password";
        }
    } else
    {
        //dismissible alert in error variable
        $error = "User not found";

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MarketPlace</title>
    
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
    <div class="auth-container d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="auth-card card p-4">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="text-center mb-4">
                                <h2 class="text-primary fw-bold">
                                    <i class="fas fa-store me-2"></i>MarketPlace
                                </h2>
                                <p class="text-muted">Welcome back! Please sign in to your account</p>
                            </div>
                            <!-- Flash message in dismissible alert -->
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION['success']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Login Form -->
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">                                
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="forget-password.php" class="text-decoration-none">Forgot password?</a>
                                </div>

                                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </button>
                            </form>

                            <!-- Social Login -->
                            <div class="text-center mb-3">
                                <p class="text-muted">Or sign in with</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn social-btn flex-fill">
                                        <i class="fab fa-google text-danger"></i>
                                    </button>
                                    <button class="btn social-btn flex-fill">
                                        <i class="fab fa-facebook text-primary"></i>
                                    </button>
                                    <button class="btn social-btn flex-fill">
                                        <i class="fab fa-twitter text-info"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Register Link -->
                            <div class="text-center">
                                <p class="mb-0">Don't have an account? 
                                    <a href="register.php" class="text-decoration-none fw-semibold">Create one here</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Back to Home -->
                    <div class="text-center mt-3">
                        <a href="index.html" class="text-white text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>