<?php
/**
 * Site Content API
 * GET: Retrieve page content (public by key)
 * PUT: Update content (admin)
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
            $key = sanitize($_GET['key'] ?? '');
            
            if ($key) {
                $stmt = $pdo->prepare("SELECT * FROM site_content WHERE page_key = ?");
                $stmt->execute([$key]);
                $content = $stmt->fetch();
                if (!$content) errorResponse('Content not found.', 404);
                successResponse('Content retrieved.', ['content' => $content]);
            }
            
            // List all content (admin)
            $stmt = $pdo->prepare("SELECT * FROM site_content ORDER BY page_key");
            $stmt->execute();
            $contents = $stmt->fetchAll();
            successResponse('Contents retrieved.', ['contents' => $contents]);
            break;
            
        case 'PUT':
            requireAdmin();
            parse_str(file_get_contents('php://input'), $putData);
            
            $id = intval($putData['id'] ?? 0);
            if ($id <= 0) errorResponse('Content ID is required.');
            
            $fields = [];
            $params = [];
            
            foreach (['title', 'content', 'meta_description', 'status'] as $field) {
                if (isset($putData[$field])) {
                    $fields[] = "{$field} = ?";
                    $params[] = $field === 'content' ? $putData[$field] : sanitize($putData[$field]);
                }
            }
            
            $fields[] = "updated_by = ?";
            $params[] = getCurrentUserId();
            
            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE site_content SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($params);
            
            successResponse('Content updated successfully.');
            break;
            
        default:
            errorResponse('Method not allowed.', 405);
    }
} catch (PDOException $e) {
    error_log("Content API Error: " . $e->getMessage());
    errorResponse('An error occurred.', 500);
}
