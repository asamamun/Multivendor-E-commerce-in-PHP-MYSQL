<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if(isset($_POST['register'])){
    //get db connection
    include_once 'db/db.php';
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $name = $firstName . ' ' . $lastName;
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $userRole = $_POST['userRole'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $terms = $_POST['terms'];
    $newsletter = $_POST['newsletter'];
    
    // Check if the passwords match
    if ($password !== $confirmPassword) {
        $error = "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Passwords do not match.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        ";
    }

    // Check if terms and conditions are accepted
    if (!$terms) {
        $error = "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                You must accept the terms and conditions.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        ";
    }
    
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        $error = "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Email already exists. Please use a different email.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        ";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $userRole);
        $stmt->execute();        
        $stmt->close();
        //if newsletter is checked, add the email in newsletter table
        if ($newsletter) {
            $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();
        }
        $conn->close();
        $_SESSION['success'] = "Registration successful. Please login.";
        // Redirect to login page
        header("Location: login.php");
        exit();
    }

        


    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MarketPlace</title>
    
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
    <div class="auth-container d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-card card p-4">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="text-center mb-4">
                                <h2 class="text-primary fw-bold">
                                    <i class="fas fa-store me-2"></i>MarketPlace
                                </h2>
                                <p class="text-muted">Create your account and start shopping</p>
                            </div>
                            <?php if (isset($error)) echo $error; ?>

                            <!-- Registration Form -->
                            <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">                                        
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">                                        
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last name" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    
                                    <select class="form-select" id="userRole" name="userRole" required>
                                        <option value="">Select account type</option>
                                        <option value="customer" selected>Customer - I want to shop</option>
                                        <option value="vendor">Seller - I want to sell products</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Create password" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleIcon1')">
                                                <i class="fas fa-eye" id="toggleIcon1"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Password must be at least 8 characters long
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                                                <i class="fas fa-eye" id="toggleIcon2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> 
                                            and <a href="#" class="text-decoration-none">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                        <label class="form-check-label" for="newsletter">
                                            Subscribe to our newsletter for updates and offers
                                        </label>
                                    </div>
                                </div>

                                <button name="register" type="submit" class="btn btn-primary w-100 mb-3">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </form>

                            <!-- Social Registration -->
                            <div class="text-center mb-3">
                                <p class="text-muted">Or register with</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn social-btn flex-fill">
                                        <i class="fab fa-google text-danger me-2"></i>Google
                                    </button>
                                    <button class="btn social-btn flex-fill">
                                        <i class="fab fa-facebook text-primary me-2"></i>Facebook
                                    </button>
                                </div>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <p class="mb-0">Already have an account? 
                                    <a href="login.php" class="text-decoration-none fw-semibold">Sign in here</a>
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
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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

        // Password validation
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>