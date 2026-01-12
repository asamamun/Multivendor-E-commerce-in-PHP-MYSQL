<?php
// Test script for ImageUtility class

// Include the ImageUtility class
require_once 'classes/ImageUtility.php';

echo "<h2>ImageUtility Class Test</h2>";

// Create an instance of ImageUtility
$imageUtility = new ImageUtility(75, 800, 600);

echo "<p>ImageUtility class created successfully!</p>";
echo "<p>Default quality: " . $imageUtility->getQuality() . "%</p>";
echo "<p>Default max dimensions: " . $imageUtility->getMaxWidth() . "x" . $imageUtility->getMaxHeight() . "</p>";

// Test the allowed file types
echo "<p>Allowed file types: " . implode(', ', $imageUtility->getAllowedTypes()) . "</p>";

// Check if GD library is available (needed for image processing)
if (extension_loaded('gd') && function_exists('gd_info')) {
    echo "<p>GD library is available - Image processing is supported!</p>";
} else {
    echo "<p>GD library is NOT available - Image processing will not work!</p>";
}

// Test with sample image if available
$sampleImages = [
    'assets/css/style.css', // Not an image
    'admin/inc/head.php',   // Not an image
];

echo "<h3>Testing file type detection:</h3>";
foreach ($sampleImages as $file) {
    if (file_exists($file)) {
        $isAllowed = $imageUtility->isAllowedType($file);
        echo "<p>File: $file - Is allowed: " . ($isAllowed ? 'Yes' : 'No') . "</p>";
    }
}

echo "<br><p><strong>ImageUtility class is ready for use!</strong></p>";
?>