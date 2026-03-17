<?php
/**
 * Auth API - Register
 * POST: full_name, email, phone, password, confirm_password
 */

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed.', 405);
}

// Get and validate input
[$data, $errors] = getRequiredFields(['full_name', 'email', 'password', 'confirm_password']);

if (!empty($errors)) {
    errorResponse(implode(' ', $errors));
}

$phone = sanitize($_POST['phone'] ?? '');

// Validate email
if (!isValidEmail($data['email'])) {
    errorResponse('Please enter a valid email address.');
}

// Validate password strength
if (!isStrongPassword($data['password'])) {
    errorResponse('Password must be at least 8 characters with uppercase, lowercase, and number.');
}

// Check password match
if ($data['password'] !== $data['confirm_password']) {
    errorResponse('Passwords do not match.');
}

try {
    $pdo = getDBConnection();
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        errorResponse('An account with this email already exists.');
    }
    
    // Hash password and insert user
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['full_name'], $data['email'], $phone, $hashedPassword]);
    
    $userId = $pdo->lastInsertId();
    
    // Auto-login after registration
    $user = [
        'id' => $userId,
        'full_name' => $data['full_name'],
        'email' => $data['email'],
        'role' => 'user',
        'profile_image' => 'default-avatar.png'
    ];
    setUserSession($user);
    
    successResponse('Registration successful! Welcome aboard.', ['user' => $user]);
    
} catch (PDOException $e) {
    error_log("Register Error: " . $e->getMessage());
    errorResponse('Registration failed. Please try again.', 500);
}
