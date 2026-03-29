<?php
/**
 * POST /apis/register.php
 * Body: { name, email, phone, password, role }
 * role: customer | vendor | courier
 */
require __DIR__ . '/helpers.php';
require __DIR__ . '/../db/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_error('Method not allowed', 405);
}

$name     = trim(input('name', ''));
$email    = trim(input('email', ''));
$phone    = trim(input('phone', ''));
$password = input('password', '');
$role     = input('role', 'customer');

// Validate
if (!$name || !$email || !$password) {
    respond_error('name, email and password are required');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond_error('Invalid email address');
}
if (!in_array($role, ['customer', 'vendor', 'courier'])) {
    respond_error('Invalid role');
}
if (strlen($password) < 6) {
    respond_error('Password must be at least 6 characters');
}

// Check duplicate email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    respond_error('Email already registered', 409);
}
$stmt->close();

// Insert
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('sssss', $name, $email, $phone, $hash, $role);
if (!$stmt->execute()) {
    respond_error('Registration failed. Please try again.', 500);
}
$user_id = $stmt->insert_id;
$stmt->close();

$token = generate_token(['user_id' => $user_id, 'role' => $role, 'email' => $email]);

respond_success([
    'token' => $token,
    'user'  => ['id' => $user_id, 'name' => $name, 'email' => $email, 'role' => $role]
], 'Registration successful');
