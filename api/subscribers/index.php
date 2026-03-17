<?php
/**
 * Subscribers API
 * POST: Subscribe (public)
 * GET: List subscribers (admin)
 * DELETE: Remove subscriber (admin)
 */

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            requireAdmin();
            $page = intval($_GET['page'] ?? 1);
            
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM subscribers WHERE status = 'active'");
            $countStmt->execute();
            $total = $countStmt->fetchColumn();
            $pagination = getPagination($total, $page);
            
            $stmt = $pdo->prepare("SELECT * FROM subscribers WHERE status = 'active' ORDER BY created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
            $stmt->execute();
            $subscribers = $stmt->fetchAll();
            
            successResponse('Subscribers retrieved.', ['subscribers' => $subscribers, 'pagination' => $pagination]);
            break;
            
        case 'POST':
            $email = sanitize($_POST['email'] ?? '');
            if (!isValidEmail($email)) errorResponse('Please enter a valid email address.');
            
            // Check duplicate
            $stmt = $pdo->prepare("SELECT id, status FROM subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                if ($existing['status'] === 'active') {
                    errorResponse('This email is already subscribed.');
                }
                // Reactivate
                $stmt = $pdo->prepare("UPDATE subscribers SET status = 'active' WHERE id = ?");
                $stmt->execute([$existing['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
                $stmt->execute([$email]);
            }
            
            successResponse('Successfully subscribed to our newsletter!', [], 201);
            break;
            
        case 'DELETE':
            requireAdmin();
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) errorResponse('Subscriber ID is required.');
            
            $stmt = $pdo->prepare("UPDATE subscribers SET status = 'unsubscribed' WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) errorResponse('Subscriber not found.', 404);
            successResponse('Subscriber removed.');
            break;
            
        default:
            errorResponse('Method not allowed.', 405);
    }
} catch (PDOException $e) {
    error_log("Subscribers API Error: " . $e->getMessage());
    errorResponse('An error occurred.', 500);
}
