<?php
/**
 * Cars API - CRUD, Search, Filter, Availability
 * GET: List/search cars with pagination
 * POST: Create car (admin)
 * PUT: Update car (admin)
 * DELETE: Delete car (admin)
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
            // ---- List / Search / Filter Cars ----
            $page = intval($_GET['page'] ?? 1);
            $search = sanitize($_GET['search'] ?? '');
            $brand = intval($_GET['brand'] ?? 0);
            $category = sanitize($_GET['category'] ?? '');
            $fuel = sanitize($_GET['fuel'] ?? '');
            $transmission = sanitize($_GET['transmission'] ?? '');
            $minPrice = floatval($_GET['min_price'] ?? 0);
            $maxPrice = floatval($_GET['max_price'] ?? 999999);
            $status = sanitize($_GET['status'] ?? 'available');
            $sortBy = sanitize($_GET['sort'] ?? 'created_at');
            $sortDir = strtoupper(sanitize($_GET['dir'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';
            $id = intval($_GET['id'] ?? 0);
            
            // Single car detail
            if ($id > 0) {
                $stmt = $pdo->prepare("
                    SELECT c.*, b.name AS brand_name, b.logo AS brand_logo
                    FROM cars c 
                    JOIN brands b ON c.brand_id = b.id 
                    WHERE c.id = ?
                ");
                $stmt->execute([$id]);
                $car = $stmt->fetch();
                if (!$car) errorResponse('Car not found.', 404);
                successResponse('Car found.', ['car' => $car]);
            }
            
            // Build query with filters
            $where = [];
            $params = [];
            
            if ($status && $status !== 'all') {
                $where[] = "c.status = ?";
                $params[] = $status;
            }
            if ($search) {
                $where[] = "(c.model LIKE ? OR b.name LIKE ? OR c.description LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
            }
            if ($brand > 0) {
                $where[] = "c.brand_id = ?";
                $params[] = $brand;
            }
            if ($category) {
                $where[] = "c.category = ?";
                $params[] = $category;
            }
            if ($fuel) {
                $where[] = "c.fuel_type = ?";
                $params[] = $fuel;
            }
            if ($transmission) {
                $where[] = "c.transmission = ?";
                $params[] = $transmission;
            }
            if ($minPrice > 0) {
                $where[] = "c.price_per_day >= ?";
                $params[] = $minPrice;
            }
            if ($maxPrice < 999999) {
                $where[] = "c.price_per_day <= ?";
                $params[] = $maxPrice;
            }
            
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // Whitelist sort columns
            $allowedSort = ['price_per_day', 'model', 'year', 'created_at'];
            $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
            
            // Count total
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM cars c JOIN brands b ON c.brand_id = b.id {$whereClause}");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            
            $pagination = getPagination($total, $page, 12);
            
            // Fetch cars
            $sql = "SELECT c.*, b.name AS brand_name 
                    FROM cars c 
                    JOIN brands b ON c.brand_id = b.id 
                    {$whereClause} 
                    ORDER BY c.{$sortBy} {$sortDir} 
                    LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $cars = $stmt->fetchAll();
            
            successResponse('Cars retrieved.', ['cars' => $cars, 'pagination' => $pagination]);
            break;
            
        case 'POST':
            // ---- Create Car (Admin Only) ----
            requireAdmin();
            
            [$data, $errors] = getRequiredFields(['brand_id', 'model', 'year', 'price_per_day']);
            if (!empty($errors)) errorResponse(implode(' ', $errors));
            
            // Handle image upload
            $imagePath = 'default-car.png';
            if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload = uploadImage($_FILES['image'], 'cars');
                if ($upload['success']) $imagePath = $upload['path'];
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO cars (brand_id, model, year, color, price_per_day, fuel_type, transmission, seats, category, image, description, features, mileage, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['brand_id'],
                $data['model'],
                $data['year'],
                sanitize($_POST['color'] ?? ''),
                $data['price_per_day'],
                sanitize($_POST['fuel_type'] ?? 'petrol'),
                sanitize($_POST['transmission'] ?? 'automatic'),
                intval($_POST['seats'] ?? 5),
                sanitize($_POST['category'] ?? 'economy'),
                $imagePath,
                sanitize($_POST['description'] ?? ''),
                sanitize($_POST['features'] ?? ''),
                sanitize($_POST['mileage'] ?? ''),
                sanitize($_POST['status'] ?? 'available')
            ]);
            
            successResponse('Car added successfully.', ['id' => $pdo->lastInsertId()], 201);
            break;
            
        case 'PUT':
            // ---- Update Car (Admin Only) ----
            requireAdmin();
            parse_str(file_get_contents('php://input'), $putData);
            
            $id = intval($putData['id'] ?? 0);
            if ($id <= 0) errorResponse('Car ID is required.');
            
            $fields = [];
            $params = [];
            $allowed = ['brand_id', 'model', 'year', 'color', 'price_per_day', 'fuel_type', 'transmission', 'seats', 'category', 'description', 'features', 'mileage', 'status'];
            
            foreach ($allowed as $field) {
                if (isset($putData[$field])) {
                    $fields[] = "{$field} = ?";
                    $params[] = sanitize($putData[$field]);
                }
            }
            
            if (empty($fields)) errorResponse('No fields to update.');
            
            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE cars SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($params);
            
            successResponse('Car updated successfully.');
            break;
            
        case 'DELETE':
            // ---- Delete Car (Admin Only) ----
            requireAdmin();
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) errorResponse('Car ID is required.');
            
            $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) errorResponse('Car not found.', 404);
            
            successResponse('Car deleted successfully.');
            break;
            
        default:
            errorResponse('Method not allowed.', 405);
    }
    
} catch (PDOException $e) {
    error_log("Cars API Error: " . $e->getMessage());
    errorResponse('An error occurred. Please try again.', 500);
}
