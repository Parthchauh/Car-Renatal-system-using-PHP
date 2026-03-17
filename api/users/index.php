<?php
/**
 * Users API - Admin Management + Profile
 * GET: List/view users (admin) or profile (self)
 * POST: Update profile (self)
 * PUT: Update user (admin)
 * DELETE: Delete user (admin)
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
            requireLogin();
            
            $action = sanitize($_GET['action'] ?? '');
            
            // Get own profile
            if ($action === 'profile') {
                $stmt = $pdo->prepare("SELECT id, full_name, email, phone, profile_image, address, role, created_at FROM users WHERE id = ?");
                $stmt->execute([getCurrentUserId()]);
                $user = $stmt->fetch();
                if (!$user) errorResponse('User not found.', 404);
                successResponse('Profile retrieved.', ['user' => $user]);
            }
            
            // Admin: list all users
            requireAdmin();
            $page = intval($_GET['page'] ?? 1);
            $search = sanitize($_GET['search'] ?? '');
            $role = sanitize($_GET['role'] ?? '');
            
            $where = [];
            $params = [];
            
            if ($search) {
                $where[] = "(full_name LIKE ? OR email LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
            }
            if ($role) {
                $where[] = "role = ?";
                $params[] = $role;
            }
            
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM users {$whereClause}");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            $pagination = getPagination($total, $page);
            
            $stmt = $pdo->prepare("
                SELECT id, full_name, email, phone, role, status, profile_image, created_at,
                    (SELECT COUNT(*) FROM bookings WHERE user_id = users.id) AS booking_count
                FROM users {$whereClause}
                ORDER BY created_at DESC
                LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
            ");
            $stmt->execute($params);
            $users = $stmt->fetchAll();
            
            successResponse('Users retrieved.', ['users' => $users, 'pagination' => $pagination]);
            break;
            
        case 'POST':
            // Update own profile
            requireLogin();
            
            $fullName = sanitize($_POST['full_name'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');
            $address = sanitize($_POST['address'] ?? '');
            
            if (empty($fullName)) errorResponse('Full name is required.');
            
            // Handle profile image upload
            $imageSql = '';
            $imageParam = [];
            if (!empty($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $upload = uploadImage($_FILES['profile_image'], 'profiles');
                if ($upload['success']) {
                    $imageSql = ', profile_image = ?';
                    $imageParam = [$upload['path']];
                    $_SESSION['profile_image'] = $upload['path'];
                }
            }
            
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? {$imageSql} WHERE id = ?");
            $stmt->execute(array_merge([$fullName, $phone, $address], $imageParam, [getCurrentUserId()]));
            
            $_SESSION['user_name'] = $fullName;
            
            successResponse('Profile updated successfully.');
            break;
            
        case 'PUT':
            requireAdmin();
            parse_str(file_get_contents('php://input'), $putData);
            
            $id = intval($putData['id'] ?? 0);
            if ($id <= 0) errorResponse('User ID is required.');
            
            $fields = [];
            $params = [];
            
            foreach (['full_name', 'email', 'phone', 'role', 'status'] as $field) {
                if (isset($putData[$field])) {
                    $fields[] = "{$field} = ?";
                    $params[] = sanitize($putData[$field]);
                }
            }
            
            if (empty($fields)) errorResponse('No fields to update.');
            
            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($params);
            
            successResponse('User updated successfully.');
            break;
            
        case 'DELETE':
            requireAdmin();
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) errorResponse('User ID is required.');
            
            // Prevent self-deletion
            if ($id == getCurrentUserId()) errorResponse('You cannot delete your own account.');
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) errorResponse('User not found.', 404);
            successResponse('User deleted successfully.');
            break;
            
        default:
            errorResponse('Method not allowed.', 405);
    }
} catch (PDOException $e) {
    error_log("Users API Error: " . $e->getMessage());
    errorResponse('An error occurred.', 500);
}
