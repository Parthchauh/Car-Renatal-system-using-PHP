<?php
/**
 * Session Management & Authentication Helpers
 * CSRF protection, role checks, session security
 */

require_once __DIR__ . '/config.php';

// Start secure session
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        session_start();
    }
    
    // Regenerate session ID periodically to prevent fixation
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// Start session automatically
initSession();

// ============ CSRF Protection ============

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Validate CSRF token from request
 */
function validateCSRFToken($token = null) {
    if ($token === null) {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    }
    
    if (empty($token) || !isset($_SESSION[CSRF_TOKEN_NAME])) {
        return false;
    }
    
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// ============ Auth Helpers ============

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data from session
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user',
        'profile_image' => $_SESSION['profile_image'] ?? 'default-avatar.png'
    ];
}

/**
 * Set user session data after login
 */
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['profile_image'] = $user['profile_image'];
    session_regenerate_id(true);
}

/**
 * Destroy session (logout)
 */
function destroySession() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Require authentication - redirect or return JSON error
 */
function requireLogin($json = true) {
    if (!isLoggedIn()) {
        if ($json) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Please login to continue.']);
            exit;
        }
        header('Location: ' . APP_URL . '/views/login.php');
        exit;
    }
}

/**
 * Require admin role
 */
function requireAdmin($json = true) {
    requireLogin($json);
    if (!isAdmin()) {
        if ($json) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
            exit;
        }
        header('Location: ' . APP_URL . '/views/login.php');
        exit;
    }
}
