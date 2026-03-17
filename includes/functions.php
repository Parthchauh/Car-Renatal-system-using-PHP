<?php
/**
 * Utility Functions
 * Input sanitization, response helpers, pagination, file uploads
 */

require_once __DIR__ . '/config.php';

// ============ Response Helpers ============

/**
 * Send JSON response and exit
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Send success JSON response
 */
function successResponse($message, $data = [], $statusCode = 200) {
    jsonResponse(array_merge(['success' => true, 'message' => $message], $data), $statusCode);
}

/**
 * Send error JSON response
 */
function errorResponse($message, $statusCode = 400) {
    jsonResponse(['success' => false, 'message' => $message], $statusCode);
}

// ============ Input Sanitization ============

/**
 * Sanitize string input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function isStrongPassword($password) {
    // Minimum 8 chars, at least 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[a-z]/', $password)
        && preg_match('/[0-9]/', $password);
}

/**
 * Get required POST fields, returns sanitized values or errors
 */
function getRequiredFields($fields) {
    $data = [];
    $errors = [];
    
    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        } else {
            $data[$field] = sanitize($_POST[$field]);
        }
    }
    
    return [$data, $errors];
}

// ============ Pagination ============

/**
 * Get pagination parameters
 */
function getPagination($totalItems, $currentPage = 1, $perPage = null) {
    $perPage = $perPage ?? ITEMS_PER_PAGE;
    $currentPage = max(1, intval($currentPage));
    $totalPages = max(1, ceil($totalItems / $perPage));
    $currentPage = min($currentPage, $totalPages);
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

// ============ File Upload ============

/**
 * Handle image upload
 */
function uploadImage($file, $subDir = '') {
    // Validate file
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload failed.'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB limit.'];
    }
    
    // Check file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, WebP, GIF.'];
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . strtolower($ext);
    
    // Create upload directory if needed
    $uploadPath = UPLOAD_DIR . ($subDir ? $subDir . '/' : '');
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    // Move file
    $destination = $uploadPath . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $relativePath = 'assets/uploads/' . ($subDir ? $subDir . '/' : '') . $filename;
        return ['success' => true, 'filename' => $filename, 'path' => $relativePath];
    }
    
    return ['success' => false, 'message' => 'Failed to save uploaded file.'];
}

// ============ Date Helpers ============

/**
 * Calculate number of days between two dates
 */
function calculateDays($startDate, $endDate) {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $diff = $start->diff($end);
    return max(1, $diff->days);
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// ============ Misc ============

/**
 * Generate random token
 */
function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Get base URL for the project
 */
function baseUrl($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}
