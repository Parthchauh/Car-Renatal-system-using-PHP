<?php
/**
 * Auth API - Reset Password
 * POST action=request: email (sends reset token)
 * POST action=reset: token, password, confirm_password
 */

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed.', 405);
}

$action = sanitize($_POST['action'] ?? 'request');

try {
    $pdo = getDBConnection();
    
    if ($action === 'request') {
        // Request password reset
        $email = sanitize($_POST['email'] ?? '');
        if (!isValidEmail($email)) {
            errorResponse('Please enter a valid email address.');
        }
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $token = generateToken();
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            $stmt->execute([$token, $expiry, $user['id']]);
        }
        
        // Always show success to prevent email enumeration
        successResponse('If an account exists with this email, a reset link has been sent.', ['token' => $token ?? '']);
        
    } elseif ($action === 'reset') {
        // Reset password with token
        [$data, $errors] = getRequiredFields(['token', 'password', 'confirm_password']);
        
        if (!empty($errors)) {
            errorResponse(implode(' ', $errors));
        }
        
        if (!isStrongPassword($data['password'])) {
            errorResponse('Password must be at least 8 characters with uppercase, lowercase, and number.');
        }
        
        if ($data['password'] !== $data['confirm_password']) {
            errorResponse('Passwords do not match.');
        }
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() AND status = 'active'");
        $stmt->execute([$data['token']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            errorResponse('Invalid or expired reset token.');
        }
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->execute([$hashedPassword, $user['id']]);
        
        successResponse('Password has been reset successfully. You can now login.');
    }
    
} catch (PDOException $e) {
    error_log("Password Reset Error: " . $e->getMessage());
    errorResponse('Password reset failed. Please try again.', 500);
}
