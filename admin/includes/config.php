<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nsct');

// Base URL - Update this to your actual domain
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$script_path = rtrim($script_path, '/admin');
define('BASE_URL', $base_url . $script_path);
define('ADMIN_URL', $base_url . $script_path . '/admin');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("set names utf8");
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Authentication check function
function requireLogin($redirect = 'login.php') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . $redirect);
        exit;
    }
}

// CSRF Protection
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Helper function to set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Helper function to display flash message
function displayFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        
        echo '<div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        // Clear the flash message
        unset($_SESSION['flash']);
    }
}

// File upload function
function uploadFile($file, $target_dir = '../uploads/') {
    $errors = [];
    $file_name = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Generate unique filename
    $new_filename = uniqid() . '.' . $file_ext;
    $target_file = $target_dir . $new_filename;
    
    // Check file size (5MB max)
    $max_size = 5 * 1024 * 1024;
    if ($file_size > $max_size) {
        $errors[] = 'File is too large. Maximum size is 5MB.';
    }
    
    // Allow certain file formats
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    if (!in_array($file_ext, $allowed_extensions)) {
        $errors[] = 'Only JPG, JPEG, PNG, GIF, PDF, DOC & DOCX files are allowed.';
    }
    
    // If no errors, upload file
    if (empty($errors)) {
        if (move_uploaded_file($file_tmp, $target_file)) {
            return [
                'success' => true,
                'file_path' => $target_file,
                'file_name' => $new_filename
            ];
        } else {
            $errors[] = 'There was an error uploading your file.';
        }
    }
    
    return [
        'success' => false,
        'errors' => $errors
    ];
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Get setting value
function getSetting($key, $default = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['setting_value'] : $default;
    } catch(PDOException $e) {
        // Log error
        error_log("Error getting setting: " . $e->getMessage());
        return $default;
    }
}

// Update setting
function updateSetting($key, $value) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        return $stmt->execute([$key, $value]);
    } catch(PDOException $e) {
        // Log error
        error_log("Error updating setting: " . $e->getMessage());
        return false;
    }
}

// Log activity
function logActivity($user_id, $action, $details = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        return true;
    } catch(PDOException $e) {
        // Log error
        error_log("Error logging activity: " . $e->getMessage());
        return false;
    }
}

// Generate slug from string
function generateSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    $slug = preg_replace('/-+/', '-', $slug); // Replace multiple dashes with single dash
    return trim($slug, '-');
}

// Format date
function formatDate($date, $format = 'd M, Y') {
    if (empty($date) || $date == '0000-00-00 00:00:00') {
        return 'N/A';
    }
    $date = new DateTime($date);
    return $date->format($format);
}

// Generate random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Check if email is valid
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Redirect to previous page
function redirectBack() {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: index.php');
    }
    exit;
}

// Get current page URL
function currentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// Check if request is AJAX
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Check if user has permission
function hasPermission($permission) {
    if (!isset($_SESSION['user_permissions'])) {
        return false;
    }
    return in_array($permission, $_SESSION['user_permissions']);
}

// Require specific permission
function requirePermission($permission, $redirect = 'index.php') {
    if (!hasPermission($permission)) {
        setFlashMessage('danger', 'You do not have permission to access this page.');
        header('Location: ' . $redirect);
        exit;
    }
}

// Generate pagination
function paginate($total_items, $per_page, $current_page, $url) {
    $total_pages = ceil($total_items / $per_page);
    
    $pagination = [
        'total_items' => $total_items,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'has_previous' => $current_page > 1,
        'has_next' => $current_page < $total_pages,
        'previous_page' => $current_page > 1 ? $current_page - 1 : 1,
        'next_page' => $current_page < $total_pages ? $current_page + 1 : $total_pages,
        'start_item' => (($current_page - 1) * $per_page) + 1,
        'end_item' => min($current_page * $per_page, $total_items)
    ];
    
    $pagination['html'] = '<ul class="pagination">';
    
    // Previous button
    if ($pagination['has_previous']) {
        $pagination['html'] .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $pagination['previous_page'], $url) . '">&laquo; Previous</a></li>';
    } else {
        $pagination['html'] .= '<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($start + 4, $total_pages);
    
    if ($start > 1) {
        $pagination['html'] .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', 1, $url) . '">1</a></li>';
        if ($start > 2) {
            $pagination['html'] .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $pagination['html'] .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $pagination['html'] .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $i, $url) . '">' . $i . '</a></li>';
        }
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $pagination['html'] .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $pagination['html'] .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $total_pages, $url) . '">' . $total_pages . '</a></li>';
    }
    
    // Next button
    if ($pagination['has_next']) {
        $pagination['html'] .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $pagination['next_page'], $url) . '">Next &raquo;</a></li>';
    } else {
        $pagination['html'] .= '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
    }
    
    $pagination['html'] .= '</ul>';
    
    return $pagination;
}

// Get current URL segments
function getUrlSegments() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', trim($uri, '/'));
    
    // Remove base path segments
    $base_path = parse_url(BASE_URL, PHP_URL_PATH);
    $base_segments = explode('/', trim($base_path, '/'));
    
    foreach ($base_segments as $segment) {
        if ($segment === $segments[0]) {
            array_shift($segments);
        }
    }
    
    return $segments;
}

// Get current route
function getCurrentRoute() {
    $segments = getUrlSegments();
    return '/' . implode('/', $segments);
}

// Check if route matches pattern
function routeIs($pattern) {
    $route = getCurrentRoute();
    return fnmatch($pattern, $route);
}

// Get current user IP
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Generate password hash
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Generate a random password
function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
    $password = '';
    $chars_length = strlen($chars) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, $chars_length)];
    }
    
    return $password;
}

// Truncate text
function truncateText($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $append;
}

// Get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Check if file is an image
function isImage($filename) {
    $ext = getFileExtension($filename);
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    return in_array($ext, $image_extensions);
}

// Resize image
function resizeImage($source_path, $destination_path, $max_width = 800, $max_height = 600, $quality = 80) {
    if (!file_exists($source_path) || !isImage($source_path)) {
        return false;
    }
    
    $ext = getFileExtension($source_path);
    
    // Create image resource based on file type
    switch($ext) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($source_path);
            break;
        case 'png':
            $image = imagecreatefrompng($source_path);
            break;
        case 'gif':
            $image = imagecreatefromgif($source_path);
            break;
        case 'webp':
            $image = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    if (!$image) {
        return false;
    }
    
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Calculate new dimensions
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = (int)($width * $ratio);
    $new_height = (int)($height * $ratio);
    
    // Create new image
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Preserve transparency for PNG and GIF
    if ($ext === 'png' || $ext === 'gif') {
        imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
    }
    
    // Resize image
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Save image
    $result = false;
    
    switch($ext) {
        case 'jpg':
        case 'jpeg':
            $result = imagejpeg($new_image, $destination_path, $quality);
            break;
        case 'png':
            $result = imagepng($new_image, $destination_path, 9);
            break;
        case 'gif':
            $result = imagegif($new_image, $destination_path);
            break;
        case 'webp':
            $result = imagewebp($new_image, $destination_path, $quality);
            break;
    }
    
    // Free up memory
    imagedestroy($image);
    imagedestroy($new_image);
    
    return $result;
}

// Generate thumbnail
function generateThumbnail($source_path, $destination_path, $width = 200, $height = 200, $quality = 80) {
    return resizeImage($source_path, $destination_path, $width, $height, $quality);
}

// Get file size in human readable format
function formatFileSize($bytes, $decimals = 2) {
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
}
