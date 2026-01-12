<?php
// Simulate the exact upload process that might be causing the issue

// Include the ImageUtility class
require_once 'classes/ImageUtility.php';

echo "<h2>Simulating Upload Process</h2>";

// Create a simulated $_FILES array that mimics what PHP creates during upload
$simulatedFile = [
    'name' => '0OFDTcE64kw.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => 'sample-images/0OFDTcE64kw.jpg', // Using one of our sample files as temp file
    'error' => UPLOAD_ERR_OK,
    'size' => filesize('sample-images/0OFDTcE64kw.jpg')
];

echo "<h3>Simulated Upload Array:</h3>";
echo "Name: " . $simulatedFile['name'] . "<br>";
echo "Type: " . $simulatedFile['type'] . "<br>";
echo "Temp name: " . $simulatedFile['tmp_name'] . "<br>";
echo "Error: " . $simulatedFile['error'] . "<br>";
echo "Size: " . $simulatedFile['size'] . "<br>";

$imageUtility = new ImageUtility(75, 800, 600);

echo "<h3>Step-by-step processUploadedFile simulation:</h3>";

// Step 1: Validate upload error
echo "1. Upload error check: " . ($simulatedFile['error'] === UPLOAD_ERR_OK ? 'PASS' : 'FAIL') . "<br>";

// Step 2: is_uploaded_file check - this would fail with our simulated file since it's not actually a temp upload
echo "2. is_uploaded_file check: " . (is_uploaded_file($simulatedFile['tmp_name']) ? 'PASS' : 'FAIL (expected with simulated file)') . "<br>";

// Step 3: isAllowedType check on the name
$isAllowed = $imageUtility->isAllowedType($simulatedFile['name']);
echo "3. isAllowedType check on name '" . $simulatedFile['name'] . "': " . ($isAllowed ? 'PASS' : 'FAIL') . "<br>";

// Let's manually run the same check that's in processUploadedFile
$basename = basename($simulatedFile['name']);
$extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
$allowedTypes = array_map('strtolower', $imageUtility->getAllowedTypes());
$isInAllowed = in_array($extension, $allowedTypes);

echo "Manual check details:<br>";
echo "&nbsp;&nbsp;- Basename: $basename<br>";
echo "&nbsp;&nbsp;- Extension: $extension<br>";
echo "&nbsp;&nbsp;- Allowed types: " . implode(', ', $allowedTypes) . "<br>";
echo "&nbsp;&nbsp;- Is in allowed: " . ($isInAllowed ? 'YES' : 'NO') . "<br>";

// Now let's try to simulate what might happen with a real temporary file
echo "<h3>Creating a copy to simulate temp file:</h3>";
$tempFile = sys_get_temp_dir() . '/temp_test_' . uniqid() . '.jpg';
copy('sample-images/0OFDTcE64kw.jpg', $tempFile);
echo "Created temp file: $tempFile<br>";

$realSimulatedFile = [
    'name' => 'test_image.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $tempFile,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($tempFile)
];

echo "Real simulated upload array:<br>";
echo "&nbsp;&nbsp;- Name: " . $realSimulatedFile['name'] . "<br>";
echo "&nbsp;&nbsp;- Temp name: " . $realSimulatedFile['tmp_name'] . "<br>";

$isAllowedReal = $imageUtility->isAllowedType($realSimulatedFile['name']);
echo "isAllowedType check: " . ($isAllowedReal ? 'ALLOWED' : 'NOT ALLOWED') . "<br>";

// Now try to run processUploadedFile with this simulated file
echo "<h3>Trying processUploadedFile with simulated temp file:</h3>";
$outputFile = 'output_test.jpg';
$result = $imageUtility->processUploadedFile($realSimulatedFile, $outputFile, ['quality' => 75]);

if ($result['success']) {
    echo "SUCCESS: Image processed!<br>";
    if (isset($result['original_size']) && isset($result['processed_size'])) {
        echo "Original size: " . $result['original_size'] . " bytes<br>";
        echo "Processed size: " . $result['processed_size'] . " bytes<br>";
    }
} else {
    echo "FAILED: " . $result['message'] . "<br>";
}

// Clean up
if (file_exists($outputFile)) {
    unlink($outputFile);
}
if (file_exists($tempFile)) {
    unlink($tempFile);
}

echo "<h3>Potential Issues:</h3>";
echo "<ul>";
echo "<li>Check if the uploaded filename has special characters or non-standard formatting</li>";
echo "<li>Verify that the extension extraction works correctly with the exact filename from the upload</li>";
echo "<li>Check if the file upload is actually successful (error code is UPLOAD_ERR_OK)</li>";
echo "</ul>";
?>