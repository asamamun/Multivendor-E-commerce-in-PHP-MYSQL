<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
require "db/db.php";

// Check if user is logged in and is a customer
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
    exit();
}

// Check user role from session to ensure only customers can review
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Only customers can submit reviews']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if request is POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Validate data
if($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit();
}

if($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit();
}

if(empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Title is required']);
    exit();
}

if(empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Review comment is required']);
    exit();
}

if(strlen($comment) < 10) {
    echo json_encode(['success' => false, 'message' => 'Review comment must be at least 10 characters long']);
    exit();
}

// Check if user already reviewed this product
$check_sql = "SELECT id FROM reviews WHERE product_id = $product_id AND customer_id = $user_id";
$check_result = $conn->query($check_sql);

if($check_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this product']);
    exit();
}

// Check if product exists
$product_check_sql = "SELECT id FROM products WHERE id = $product_id";
$product_check_result = $conn->query($product_check_sql);

if($product_check_result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

// Insert review
$insert_sql = "INSERT INTO reviews (product_id, customer_id, rating, title, comment, status, created_at) 
               VALUES ($product_id, $user_id, $rating, '" . $conn->real_escape_string($title) . "', 
                      '" . $conn->real_escape_string($comment) . "', 'pending', NOW())";

if($conn->query($insert_sql)) {
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully. It will be visible after approval.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error submitting review: ' . $conn->error]);
}

$conn->close();
?>