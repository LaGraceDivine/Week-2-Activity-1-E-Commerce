<?php
// Suppress warnings to prevent JSON corruption
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
// Start output buffering to catch any unexpected output
ob_start();

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../helpers/auth.php';

if (!isloggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$image_path = null;

// Handle file upload if present
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    
    // Validate file size (max 5MB)
    $maxBytes = 5 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        echo json_encode(['success' => false, 'error' => 'File too large (max 5MB)']);
        exit;
    }
    
    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp'
    ];
    
    if (!isset($allowed[$mime])) {
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Only PNG, JPG, and WebP are allowed.']);
        exit;
    }
    
    $ext = $allowed[$mime];
    
    // Define upload base directory
    $basePath = __DIR__ . '/../uploads';
    if (!is_dir($basePath)) {
        if (!mkdir($basePath, 0755, true)) {
            echo json_encode(['success' => false, 'error' => 'Failed to create uploads folder']);
            exit;
        }
    }
    $base = realpath($basePath);
    if ($base === false) {
        echo json_encode(['success' => false, 'error' => 'Uploads folder path invalid']);
        exit;
    }
    
    // Create user directory
    $uDir = $base . DIRECTORY_SEPARATOR . "u{$user_id}";
    if (!is_dir($uDir)) {
        mkdir($uDir, 0755, true);
    }
    
    // Use temp directory for new products (will move after product is created)
    $pDir = $uDir . DIRECTORY_SEPARATOR . "temp";
    if (!is_dir($pDir)) {
        mkdir($pDir, 0755, true);
    }
    
    // Securely generate file name
    $filename = 'image_' . time() . '_' . substr(bin2hex(random_bytes(6)), 0, 12) . '.' . $ext;
    $destination = $pDir . DIRECTORY_SEPARATOR . $filename;
    
    // Prevent directory traversal
    $realDestDir = realpath(dirname($destination));
    if (strpos($realDestDir, $base) !== 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid destination']);
        exit;
    }
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save file']);
        exit;
    }
    
    // Build relative file path
    $image_path = 'uploads/u' . $user_id . '/temp/' . $filename;
}

// Get form data (from FormData or JSON)
$data = [];

// Check if it's FormData (multipart/form-data) or JSON
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'multipart/form-data') !== false || !empty($_POST)) {
    // FormData submission - data comes in $_POST
    $data = $_POST;
} else {
    // JSON submission (backward compatibility)
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }
}

// Validate required fields - use isset() to avoid warnings
if (!isset($data['product_cat']) || !isset($data['product_brand']) || !isset($data['product_title']) || !isset($data['product_price']) ||
    empty($data['product_cat']) || empty($data['product_brand']) || empty($data['product_title']) || empty($data['product_price'])) {
    $missing = [];
    if (!isset($data['product_cat']) || empty($data['product_cat'])) $missing[] = 'product_cat';
    if (!isset($data['product_brand']) || empty($data['product_brand'])) $missing[] = 'product_brand';
    if (!isset($data['product_title']) || empty($data['product_title'])) $missing[] = 'product_title';
    if (!isset($data['product_price']) || empty($data['product_price'])) $missing[] = 'product_price';
    echo json_encode(['success' => false, 'error' => 'Missing required fields: ' . implode(', ', $missing)]);
    exit;
}

$args = [
    'product_cat' => isset($data['product_cat']) ? (int)$data['product_cat'] : null,
    'product_brand' => isset($data['product_brand']) ? (int)$data['product_brand'] : null,
    'product_title' => isset($data['product_title']) ? trim($data['product_title']) : '',
    'product_price' => isset($data['product_price']) ? (float)$data['product_price'] : null,
    'product_desc' => isset($data['product_desc']) ? trim($data['product_desc']) : '',
    'product_image' => $image_path, // Use uploaded image path or null
    'product_keywords' => isset($data['product_keywords']) ? trim($data['product_keywords']) : ''
];

error_log("Add product args: " . print_r($args, true));

try {
    $ctr = new ProductController();
    $result = $ctr->add_product_ctr($args);
    
    // Check if product was successfully added (even if there was a warning)
    if ($result && isset($result['success']) && $result['success']) {
        $product_id = $result['product_id'] ?? null;
        
        // If image was uploaded to temp folder and product was created, move it to product folder
        if ($image_path && $product_id && strpos($image_path, '/temp/') !== false) {
            $basePath = __DIR__ . '/../uploads';
            if (!is_dir($basePath)) {
                mkdir($basePath, 0755, true);
            }
            $base = realpath($basePath);
            if ($base === false) {
                $base = $basePath; // Fallback to relative path
            }
            $oldPath = $base . DIRECTORY_SEPARATOR . str_replace('uploads/', '', $image_path);
            $newDir = $base . DIRECTORY_SEPARATOR . "u{$user_id}" . DIRECTORY_SEPARATOR . "p{$product_id}";
            
            if (!is_dir($newDir)) {
                mkdir($newDir, 0755, true);
            }
            
            $filename = basename($oldPath);
            $newPath = $newDir . DIRECTORY_SEPARATOR . $filename;
            
            if (file_exists($oldPath) && rename($oldPath, $newPath)) {
                // Update image path in database
                $image_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
                $updateArgs = ['product_image' => $image_path];
                $ctr->update_product_ctr($product_id, $updateArgs);
            }
        }
        
        // Success - don't show foreign key warnings if product was added
        // Clean any output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        echo json_encode(['success' => true, 'message' => 'Product added successfully', 'product_id' => $product_id]);
        exit;
    } else {
        // If product creation failed but image was uploaded, clean up temp file
        if ($image_path && strpos($image_path, '/temp/') !== false) {
            $basePath = __DIR__ . '/../uploads';
            $base = realpath($basePath) ?: $basePath;
            $filePath = $base . DIRECTORY_SEPARATOR . str_replace('uploads/', '', $image_path);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        $error_msg = $result['error'] ?? 'Failed to add product';
        // Filter out foreign key constraint errors if they're just warnings
        if (strpos($error_msg, 'foreign key') !== false && strpos($error_msg, '1452') !== false) {
            // Check if product was actually inserted by checking the database
            // For now, just show a generic message
            $error_msg = 'There was an issue with the category or brand selection. Please verify they exist.';
        }
        echo json_encode(['success' => false, 'error' => $error_msg]);
    }
} catch (Exception $e) {
    // Clean up uploaded file on error
    if ($image_path && strpos($image_path, '/temp/') !== false) {
        $basePath = __DIR__ . '/../uploads';
        $base = realpath($basePath) ?: $basePath;
        $filePath = $base . DIRECTORY_SEPARATOR . str_replace('uploads/', '', $image_path);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
    
    $errorMsg = $e->getMessage();
    // Don't show foreign key constraint errors if they're just warnings
    if (strpos($errorMsg, 'foreign key') !== false || strpos($errorMsg, '1452') !== false) {
        // Check if we can verify the product was actually inserted
        // For now, show a more user-friendly message
        $errorMsg = 'There was an issue with the category or brand selection.';
    }
    
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $errorMsg]);
}
?>
