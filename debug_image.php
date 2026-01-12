<?php
// Debug script to trace image processing issue

// Include the ImageUtility class
require_once 'classes/ImageUtility.php';

echo "<h2>Debug Image Processing Issue</h2>";

// Simulate a typical JPG file upload scenario
$testFiles = [
    ['name' => 'test.jpg', 'type' => 'image/jpeg'],
    ['name' => 'test.JPG', 'type' => 'image/jpeg'],
    ['name' => 'test.JPEG', 'type' => 'image/jpeg'],
    ['name' => 'test.jpeg', 'type' => 'image/jpeg'],
    ['name' => 'test.png', 'type' => 'image/png'],
];

$imageUtility = new ImageUtility();

echo "<h3>Testing isAllowedType method:</h3>";
foreach ($testFiles as $file) {
    $isAllowed = $imageUtility->isAllowedType($file['name']);
    echo "File: {$file['name']} - Is allowed: " . ($isAllowed ? 'Yes' : 'No') . "<br>";
}

echo "<br><h3>Allowed types in ImageUtility:</h3>";
print_r($imageUtility->getAllowedTypes());

echo "<br><h3>Testing extension extraction:</h3>";
foreach ($testFiles as $file) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    echo "File: {$file['name']} -> Extension: {$ext}<br>";
}

echo "<br><h3>Checking if extension is in allowed types:</h3>";
foreach ($testFiles as $file) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = array_map('strtolower', $imageUtility->getAllowedTypes());
    $isInArray = in_array($ext, $allowedTypes);
    echo "Extension: {$ext} -> In allowed types: " . ($isInArray ? 'Yes' : 'No') . "<br>";
}

echo "<br><h3>Testing with sample file paths:</h3>";
$paths = [
    '../assets/uploads/categories/test.jpg',
    '../assets/uploads/categories/test.JPG',
    '../assets/uploads/categories/test.JPEG',
    '../assets/uploads/categories/test.jpeg'
];

foreach ($paths as $path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $isAllowed = $imageUtility->isAllowedType($path);
    echo "Path: {$path} -> Extension: {$ext} -> Is allowed: " . ($isAllowed ? 'Yes' : 'No') . "<br>";
}
?>