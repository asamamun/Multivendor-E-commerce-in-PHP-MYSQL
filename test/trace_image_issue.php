<?php
// More comprehensive debug script to trace the exact issue

// Include the ImageUtility class
require_once 'classes/ImageUtility.php';

echo "<h2>Tracing Image Processing Issue</h2>";

// Create a mock $_FILES array to simulate an uploaded JPG file
$mockFile = [
    'name' => 'test.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => '/tmp/php12345', // This is just a mock
    'error' => UPLOAD_ERR_OK,
    'size' => 102400 // 100KB
];

$imageUtility = new ImageUtility();

echo "<h3>Mock file details:</h3>";
echo "Name: " . $mockFile['name'] . "<br>";
echo "Type: " . $mockFile['type'] . "<br>";
echo "Tmp name: " . $mockFile['tmp_name'] . "<br>";
echo "Error: " . $mockFile['error'] . "<br>";

echo "<h3>Testing isAllowedType on the mock file name:</h3>";
$isAllowed = $imageUtility->isAllowedType($mockFile['name']);
echo "isAllowedType('" . $mockFile['name'] . "') = " . ($isAllowed ? 'true' : 'false') . "<br>";

echo "<h3>Extension extraction test:</h3>";
$basename = basename($mockFile['name']);
$extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
echo "Original name: " . $mockFile['name'] . "<br>";
echo "Basename: " . $basename . "<br>";
echo "Extension: " . $extension . "<br>";

echo "<h3>Allowed types:</h3>";
$allowedTypes = $imageUtility->getAllowedTypes();
print_r($allowedTypes);

echo "<h3>Checking if extension is in allowed types:</h3>";
$lowercaseAllowed = array_map('strtolower', $allowedTypes);
echo "Lowercase allowed types: ";
print_r($lowercaseAllowed);
echo "Extension '$extension' in array: " . (in_array($extension, $lowercaseAllowed) ? 'YES' : 'NO') . "<br>";

// Now test with various capitalizations
$testNames = [
    'test.jpg',
    'test.JPG',
    'test.Jpg',
    'test.JPEG',
    'test.jpeg',
    'Test.JPG',
    'TEST.JPEG'
];

echo "<h3>Testing various filename casings:</h3>";
foreach ($testNames as $name) {
    $isAllowed = $imageUtility->isAllowedType($name);
    $ext = strtolower(pathinfo(basename($name), PATHINFO_EXTENSION));
    echo "Name: $name -> Extension: $ext -> Allowed: " . ($isAllowed ? 'YES' : 'NO') . "<br>";
}

// Test the actual processUploadedFile method with a real temp file (if possible)
echo "<h3>Simulating the processUploadedFile call...</h3>";
echo "Since we can't create a real uploaded file, let's simulate the validation chain:<br>";

echo "1. Upload error check: " . ($mockFile['error'] === UPLOAD_ERR_OK ? 'PASS' : 'FAIL') . "<br>";
echo "2. is_uploaded_file check: SKIPPED (can't test without real upload)<br>";
echo "3. isAllowedType check on name '" . $mockFile['name'] . "': " . ($imageUtility->isAllowedType($mockFile['name']) ? 'PASS' : 'FAIL') . "<br>";

// Let's also test with some problematic names that might cause issues
$problematicNames = [
    'test with spaces.jpg',
    'test-with-dashes.JPG',
    'test_with_underscores.JPEG',
    'test(file).jpg',
    'test[file].jpeg',
    'café.jpg', // with special character
    'test.jpg.php', // potentially malicious
    'test.php.jpg'  // potentially malicious
];

echo "<h3>Testing potentially problematic filenames:</h3>";
foreach ($problematicNames as $name) {
    $isAllowed = $imageUtility->isAllowedType($name);
    $ext = strtolower(pathinfo(basename($name), PATHINFO_EXTENSION));
    echo "Name: $name -> Extension: $ext -> Allowed: " . ($isAllowed ? 'YES' : 'NO') . "<br>";
}
?>