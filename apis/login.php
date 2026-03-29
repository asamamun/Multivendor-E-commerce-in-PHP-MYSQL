<?php
/**
 * POST /apis/login.php
 * Body: { email, password }
 * Returns: { token, user }
 */
require __DIR__ . '/helpers.php';
require __DIR__ . '/../db/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_error('Method not allowed', 405);
}

$email    = trim(input('email', ''));
$password = input('password', '');

if (!$email || !$password) {
    respond_error('email and password are required');
}

$stmt = $conn->prepare("SELECT id, name, email, phone, role, password FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    respond_error('Invalid email or password', 401);
}

$user = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password, $user['password'])) {
    respond_error('Invalid email or password', 401);
}

$token = generate_token([
    'user_id' => $user['id'],
    'role'    => $user['role'],
    'email'   => $user['email'],
]);

respond_success([
    'token' => $token,
    'user'  => [
        'id'    => $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'role'  => $user['role'],
    ]
], 'Login successful');
