<?php
// Test script to identify the exact issue with file processing

// Include the ImageUtility class
require_once 'classes/ImageUtility.php';

echo "<h2>Testing Real JPG Files from Sample Images</h2>";

// Get all JPG files from the sample-images directory
$sampleDir = 'sample-images/';
$imageFiles = glob($sampleDir . "*.jpg");

if (empty($imageFiles)) {
    echo "<p>No JPG files found in sample-images directory.</p>";
    exit;
}

echo "<p>Found " . count($imageFiles) . " JPG files to test:</p>";

$imageUtility = new ImageUtility(75, 800, 600);

foreach ($imageFiles as $file) {
    echo "<h3>Testing file: $file</h3>";
    
    // Get file info
    $size = filesize($file);
    $sizeKB = round($size / 1024, 2);
    echo "File size: {$sizeKB} KB<br>";
    
    // Test isAllowedType method
    $isAllowed = $imageUtility->isAllowedType($file);
    echo "isAllowedType result: " . ($isAllowed ? 'ALLOWED' : 'NOT ALLOWED') . "<br>";
    
    // Show extension extraction
    $basename = basename($file);
    $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
    echo "Basename: $basename<br>";
    echo "Extracted extension: $extension<br>";
    
    // Show what the upload array would look like
    $mockUpload = [
        'name' => $basename,
        'type' => mime_content_type($file), // This might fail if fileinfo extension is not loaded
        'tmp_name' => $file, // For testing purposes only
        'error' => UPLOAD_ERR_OK,
        'size' => $size
    ];
    
    echo "Mock upload name: " . $mockUpload['name'] . "<br>";
    
    // Test the same validation as in processUploadedFile
    $isAllowedByName = $imageUtility->isAllowedType($mockUpload['name']);
    echo "isAllowedType on upload name: " . ($isAllowedByName ? 'ALLOWED' : 'NOT ALLOWED') . "<br>";
    
    // Try to process the image file directly
    $testOutput = "test_output_" . basename($file);
    $result = $imageUtility->processImage($file, $testOutput);
    echo "Direct processImage result: " . ($result['success'] ? 'SUCCESS' : 'FAILED - ' . $result['message']) . "<br>";
    
    // Clean up test output
    if (file_exists($testOutput)) {
        unlink($testOutput);
    }
    
    echo "<hr>";
}

echo "<h3>Allowed file types in ImageUtility:</h3>";
print_r($imageUtility->getAllowedTypes());
?>