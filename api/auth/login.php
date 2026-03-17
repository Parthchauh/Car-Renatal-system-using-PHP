<?php
/**
 * Auth API - Login
 * POST: email, password
 */

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed.', 405);
}

[$data, $errors] = getRequiredFields(['email', 'password']);

if (!empty($errors)) {
    errorResponse(implode(' ', $errors));
}

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($data['password'], $user['password'])) {
        errorResponse('Invalid email or password.');
    }
    
    // Set session
    setUserSession($user);
    
    // Generate new CSRF token
    generateCSRFToken();
    
    successResponse('Login successful!', [
        'user' => [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'profile_image' => $user['profile_image']
        ],
        'redirect' => $user['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php'
    ]);
    
} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    errorResponse('Login failed. Please try again.', 500);
}
