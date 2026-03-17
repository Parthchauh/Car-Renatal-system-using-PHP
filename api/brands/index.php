<?php
/**
 * Brands API - CRUD
 * GET: List all / single brand
 * POST: Create (admin)
 * PUT: Update (admin)
 * DELETE: Delete (admin)
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
            $id = intval($_GET['id'] ?? 0);
            if ($id > 0) {
                $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
                $stmt->execute([$id]);
                $brand = $stmt->fetch();
                if (!$brand) errorResponse('Brand not found.', 404);
                successResponse('Brand found.', ['brand' => $brand]);
            }
            
            $status = sanitize($_GET['status'] ?? '');
            $where = $status ? "WHERE status = ?" : "";
            $params = $status ? [$status] : [];
            
            $stmt = $pdo->prepare("SELECT b.*, (SELECT COUNT(*) FROM cars WHERE brand_id = b.id) AS car_count FROM brands b {$where} ORDER BY b.name");
            $stmt->execute($params);
            $brands = $stmt->fetchAll();
            
            successResponse('Brands retrieved.', ['brands' => $brands]);
            break;
            
        case 'POST':
            requireAdmin();
            [$data, $errors] = getRequiredFields(['name']);
            if (!empty($errors)) errorResponse(implode(' ', $errors));
            
            $logoPath = null;
            if (!empty($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $upload = uploadImage($_FILES['logo'], 'brands');
                if ($upload['success']) $logoPath = $upload['path'];
            }
            
            $stmt = $pdo->prepare("INSERT INTO brands (name, logo, description, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $logoPath,
                sanitize($_POST['description'] ?? ''),
                sanitize($_POST['status'] ?? 'active')
            ]);
            
            successResponse('Brand created successfully.', ['id' => $pdo->lastInsertId()], 201);
            break;
            
        case 'PUT':
            requireAdmin();
            parse_str(file_get_contents('php://input'), $putData);
            
            $id = intval($putData['id'] ?? 0);
            if ($id <= 0) errorResponse('Brand ID is required.');
            
            $fields = [];
            $params = [];
            
            foreach (['name', 'description', 'status'] as $field) {
                if (isset($putData[$field])) {
                    $fields[] = "{$field} = ?";
                    $params[] = sanitize($putData[$field]);
                }
            }
            
            if (empty($fields)) errorResponse('No fields to update.');
            
            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE brands SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($params);
            
            successResponse('Brand updated successfully.');
            break;
            
        case 'DELETE':
            requireAdmin();
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) errorResponse('Brand ID is required.');
            
            $stmt = $pdo->prepare("DELETE FROM brands WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) errorResponse('Brand not found.', 404);
            successResponse('Brand deleted successfully.');
            break;
            
        default:
            errorResponse('Method not allowed.', 405);
    }
} catch (PDOException $e) {
    error_log("Brands API Error: " . $e->getMessage());
    errorResponse('An error occurred.', 500);
}
