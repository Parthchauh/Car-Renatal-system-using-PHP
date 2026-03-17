<?php
/**
 * Testimonials API - CRUD + Moderation
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
            $page = intval($_GET['page'] ?? 1);
            $status = sanitize($_GET['status'] ?? '');
            $showAll = isset($_GET['all']) && isAdmin();
            
            $where = [];
            $params = [];
            
            if (!$showAll) {
                // Public: show only approved
                $where[] = "t.status = 'approved'";
            } elseif ($status) {
                $where[] = "t.status = ?";
                $params[] = $status;
            }
            
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM testimonials t {$whereClause}");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            $pagination = getPagination($total, $page);
            
            $stmt = $pdo->prepare("
                SELECT t.*, u.full_name AS user_name, u.profile_image
                FROM testimonials t
                JOIN users u ON t.user_id = u.id
                {$whereClause}
                ORDER BY t.created_at DESC
                LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
            ");
            $stmt->execute($params);
            $testimonials = $stmt->fetchAll();
            
            successResponse('Testimonials retrieved.', ['testimonials' => $testimonials, 'pagination' => $pagination]);
            break;
            
        case 'POST':
            requireLogin();
            
            [$data, $errors] = getRequiredFields(['review', 'rating']);
            if (!empty($errors)) errorResponse(implode(' ', $errors));
            
            $rating = intval($data['rating']);
            if ($rating < 1 || $rating > 5) errorResponse('Rating must be between 1 and 5.');
            
            $bookingId = intval($_POST['booking_id'] ?? 0) ?: null;
            
            $stmt = $pdo->prepare("INSERT INTO testimonials (user_id, booking_id, rating, review, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([getCurrentUserId(), $bookingId, $rating, $data['review']]);
            
            successResponse('Thank you! Your review has been submitted for approval.', ['id' => $pdo->lastInsertId()], 201);
            break;
            
        case 'PUT':
            requireAdmin();
            parse_str(file_get_contents('php://input'), $putData);
            
            $id = intval($putData['id'] ?? 0);
            $status = sanitize($putData['status'] ?? '');
            
            if ($id <= 0 || !in_array($status, ['approved', 'rejected', 'pending'])) {
                errorResponse('Valid testimonial ID and status are required.');
            }
            
            $stmt = $pdo->prepare("UPDATE testimonials SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            
            successResponse('Testimonial status updated.');
            break;
            
        case 'DELETE':
            requireAdmin();
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) errorResponse('Testimonial ID is required.');
            
            $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) errorResponse('Testimonial not found.', 404);
            successResponse('Testimonial deleted.');
            break;
            
        default:
            errorResponse('Method not allowed.', 405);
    }
} catch (PDOException $e) {
    error_log("Testimonials API Error: " . $e->getMessage());
    errorResponse('An error occurred.', 500);
}
