<?php
/**
 * ImageUtility Class
 * Handles image compression, resizing and other image manipulation tasks
 */

class ImageUtility {
    
    private $quality = 80; // Default quality percentage (1-100)
    private $maxWidth = 1920; // Default max width
    private $maxHeight = 1080; // Default max height
    private $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    // Public getters for accessing properties
    public function getQuality() {
        return $this->quality;
    }
    
    public function getMaxWidth() {
        return $this->maxWidth;
    }
    
    public function getMaxHeight() {
        return $this->maxHeight;
    }
    
    public function getAllowedTypes() {
        return $this->allowedTypes;
    }
    
    /**
     * Constructor to set default values
     * @param int $quality Compression quality (1-100)
     * @param int $maxWidth Maximum width for resized images
     * @param int $maxHeight Maximum height for resized images
     */
    public function __construct($quality = 80, $maxWidth = 1920, $maxHeight = 1080) {
        $this->quality = $quality;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
    }
    
    /**
     * Set compression quality
     * @param int $quality Quality percentage (1-100)
     * @return self
     */
    public function setQuality($quality) {
        $this->quality = min(100, max(1, $quality));
        return $this;
    }
    
    /**
     * Set maximum dimensions for images
     * @param int $width Maximum width
     * @param int $height Maximum height
     * @return self
     */
    public function setMaxDimensions($width, $height) {
        $this->maxWidth = $width;
        $this->maxHeight = $height;
        return $this;
    }
    
    /**
     * Check if file type is allowed
     * @param string $filename File name
     * @return bool
     */
    public function isAllowedType($filename) {
        $basename = basename($filename);
        $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
        return in_array($extension, array_map('strtolower', $this->allowedTypes));
    }
    
    /**
     * Get image info from file
     * @param string $filePath Path to image file
     * @return array|bool Image info or false on failure
     */
    public function getImageInfo($filePath) {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $info = getimagesize($filePath);
        if ($info === false) {
            return false;
        }
        
        return [
            'width' => $info[0],
            'height' => $info[1],
            'type' => $info[2],
            'mime' => $info['mime'],
            'extension' => image_type_to_extension($info[2], false)
        ];
    }
    
    /**
     * Calculate new dimensions maintaining aspect ratio
     * @param int $width Original width
     * @param int $height Original height
     * @param int $maxWidth Maximum width
     * @param int $maxHeight Maximum height
     * @return array New dimensions [width, height]
     */
    private function calculateDimensions($width, $height, $maxWidth, $maxHeight) {
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        
        if ($ratio >= 1) {
            // Image is smaller than max dimensions, no need to resize
            return [$width, $height];
        }
        
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);
        
        return [$newWidth, $newHeight];
    }
    
    /**
     * Create image resource from file
     * @param string $filePath Path to image file
     * @return resource|GdImage|null Image resource or null on failure
     */
    private function createImageResource($filePath) {
        $info = $this->getImageInfo($filePath);
        if (!$info) {
            return null;
        }
        
        switch ($info['type']) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($filePath);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($filePath);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($filePath);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($filePath);
            default:
                return null;
        }
    }
    
    /**
     * Save image with specified quality and format
     * @param resource|GdImage $image Image resource
     * @param string $destination Path to save image
     * @param int $quality Quality percentage (1-100)
     * @return bool Success status
     */
    private function saveImage($image, $destination, $quality = 80) {
        $extension = strtolower(pathinfo($destination, PATHINFO_EXTENSION));
        
        // Ensure destination directory exists
        $dir = dirname($destination);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return imagejpeg($image, $destination, $quality);
            case 'png':
                // Preserve transparency for PNG
                $quality = min(9, (int)(($quality / 100) * 10)); // Convert quality for PNG (0-9)
                return imagepng($image, $destination, $quality);
            case 'gif':
                return imagegif($image, $destination);
            case 'webp':
                return imagewebp($image, $destination, $quality);
            default:
                return false;
        }
    }
    
    /**
     * Resize and compress image
     * @param string $source Path to source image
     * @param string $destination Path to save processed image
     * @return array Result array with status and message
     */
    public function processImage($source, $destination) {
        // Validate source file
        if (!file_exists($source)) {
            return ['success' => false, 'message' => 'Source file does not exist'];
        }
        
        // Get image info
        $info = $this->getImageInfo($source);
        if (!$info) {
            return ['success' => false, 'message' => 'Invalid image file'];
        }
        
        // Note: File type validation is handled by processUploadedFile() for uploaded files
        // For direct processImage() calls, validation should be done by the caller
        
        // Calculate new dimensions
        list($newWidth, $newHeight) = $this->calculateDimensions(
            $info['width'], 
            $info['height'], 
            $this->maxWidth, 
            $this->maxHeight
        );
        
        // Create image resource
        $sourceImage = $this->createImageResource($source);
        if (!$sourceImage) {
            return ['success' => false, 'message' => 'Failed to create image resource'];
        }
        
        // Create destination image with new dimensions
        $destinationImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($info['type'] == IMAGETYPE_PNG || $info['type'] == IMAGETYPE_GIF) {
            imagealphablending($destinationImage, false);
            imagesavealpha($destinationImage, true);
            
            $transparent = imagecolorallocatealpha($destinationImage, 255, 255, 255, 127);
            imagefilledrectangle($destinationImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize the image
        imagecopyresampled(
            $destinationImage, 
            $sourceImage, 
            0, 0, 0, 0, 
            $newWidth, 
            $newHeight, 
            $info['width'], 
            $info['height']
        );
        
        // Free up memory - In PHP 8.0+, GD resources are objects (GdImage) and auto-freed
        // But imagedestroy still works for both resource and GdImage
        if ($sourceImage instanceof GdImage || is_resource($sourceImage)) {
            imagedestroy($sourceImage);
        }
        
        // Save the processed image
        $result = $this->saveImage($destinationImage, $destination, $this->quality);
        
        // Free up memory - In PHP 8.0+, GD resources are objects (GdImage) and auto-freed
        // But imagedestroy still works for both resource and GdImage
        if ($destinationImage instanceof GdImage || is_resource($destinationImage)) {
            imagedestroy($destinationImage);
        }
        
        if ($result) {
            return [
                'success' => true, 
                'message' => 'Image processed successfully',
                'path' => $destination,
                'original_size' => filesize($source),
                'processed_size' => filesize($destination),
                'dimensions' => ['width' => $newWidth, 'height' => $newHeight]
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to save processed image'];
        }
    }
    
    /**
     * Process uploaded file directly
     * @param array $uploadedFile $_FILES array for the uploaded file
     * @param string $destination Path to save processed image
     * @param array $options Options for processing
     * @return array Result array with status and message
     */
    public function processUploadedFile($uploadedFile, $destination, $options = []) {
        // Validate upload
        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error: ' . $this->getUploadErrorMessage($uploadedFile['error'])];
        }
        
        // Check file type first
        if (!$this->isAllowedType($uploadedFile['name'])) {
            return ['success' => false, 'message' => 'File type not allowed'];
        }
        
        if (!is_uploaded_file($uploadedFile['tmp_name'])) {
            return ['success' => false, 'message' => 'Invalid uploaded file'];
        }
        
        // Apply options
        if (isset($options['quality'])) {
            $this->setQuality($options['quality']);
        }
        
        if (isset($options['max_width']) && isset($options['max_height'])) {
            $this->setMaxDimensions($options['max_width'], $options['max_height']);
        }
        
        // Process the uploaded file
        return $this->processImage($uploadedFile['tmp_name'], $destination);
    }
    
    /**
     * Get human-readable upload error message
     * @param int $errorCode Upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive in HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
    
    /**
     * Get file size in human readable format
     * @param string $filePath Path to file
     * @return string Human readable file size
     */
    public function getFileSizeFormatted($filePath) {
        if (!file_exists($filePath)) {
            return 'File not found';
        }
        
        $size = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        $convertedSize = $size / pow(1024, $power);
        
        return round($convertedSize, 2) . ' ' . $units[$power];
    }
}
?>