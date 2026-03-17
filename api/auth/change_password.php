<?php
/**
 * Auth API - Change Password
 * POST: current_password, new_password, confirm_password
 */

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed.', 405);
}

requireLogin();

[$data, $errors] = getRequiredFields(['current_password', 'new_password', 'confirm_password']);

if (!empty($errors)) {
    errorResponse(implode(' ', $errors));
}

if (!isStrongPassword($data['new_password'])) {
    errorResponse('Password must be at least 8 characters with uppercase, lowercase, and number.');
}

if ($data['new_password'] !== $data['confirm_password']) {
    errorResponse('New passwords do not match.');
}

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);
    $user = $stmt->fetch();
    
    if (!password_verify($data['current_password'], $user['password'])) {
        errorResponse('Current password is incorrect.');
    }
    
    $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, getCurrentUserId()]);
    
    successResponse('Password changed successfully.');
    
} catch (PDOException $e) {
    error_log("Change Password Error: " . $e->getMessage());
    errorResponse('Failed to change password.', 500);
}
