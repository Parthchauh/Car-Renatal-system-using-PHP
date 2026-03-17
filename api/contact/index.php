<?php
/**
 * Contact Queries API
 * POST: Submit inquiry (public)
 * GET: List queries (admin)
 * PUT: Update status/notes (admin)
 * DELETE: Remove query (admin)
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
            $status = sanitize($_GET['status'] ?? '');
            
            $where = $status ? "WHERE status = ?" : "";
            $params = $status ? [$status] : [];
            
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM contact_queries {$where}");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            $pagination = getPagination($total, $page);
            
            $stmt = $pdo->prepare("SELECT * FROM contact_queries {$where} ORDER BY created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
            $stmt->execute($params);
            $queries = $stmt->fetchAll();
            
            successResponse('Queries retrieved.', ['queries' => $queries, 'pagination' => $pagination]);
            break;
            
        case 'POST':
            // Public endpoint
            [$data, $errors] = getRequiredFields(['name', 'email', 'subject', 'message']);
            if (!empty($errors)) errorResponse(implode(' ', $errors));
            
            if (!isValidEmail($data['email'])) errorResponse('Please enter a valid email.');
            
            $stmt = $pdo->prepare("INSERT INTO contact_queries (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['email'],
                sanitize($_POST['phone'] ?? ''),
                $data['subject'],
                $data['message']
            ]);
            
            successResponse('Your inquiry has been submitted successfully! We will get back to you soon.', [], 201);
            break;
            
        case 'PUT':
            requireAdmin();
            parse_str(file_get_contents('php://input'), $putData);
            
            $id = intval($putData['id'] ?? 0);
            if ($id <= 0) errorResponse('Query ID is required.');
            
            $fields = [];
            $params = [];
            
            if (isset($putData['status'])) {
                $fields[] = "status = ?";
                $params[] = sanitize($putData['status']);
            }
            if (isset($putData['admin_notes'])) {
                $fields[] = "admin_notes = ?";
                $params[] = sanitize($putData['admin_notes']);
            }
            
            if (empty($fields)) errorResponse('No fields to update.');
            
            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE contact_queries SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($params);
            
            successResponse('Query updated.');
            break;
            
        case 'DELETE':
            requireAdmin();
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) errorResponse('Query ID is required.');
            
            $stmt = $pdo->prepare("DELETE FROM contact_queries WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) errorResponse('Query not found.', 404);
            successResponse('Query deleted.');
            break;
            
        default:
            errorResponse('Method not allowed.', 405);
    }
} catch (PDOException $e) {
    error_log("Contact API Error: " . $e->getMessage());
    errorResponse('An error occurred.', 500);
}
