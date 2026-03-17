<?php
/**
 * Bookings API - CRUD + Status Management
 * GET: List bookings (user's own or all for admin)
 * POST: Create booking
 * PUT: Update booking status
 * DELETE: Cancel booking
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
            $page = intval($_GET['page'] ?? 1);
            $status = sanitize($_GET['status'] ?? '');
            $id = intval($_GET['id'] ?? 0);
            
            // Single booking detail
            if ($id > 0) {
                $sql = "SELECT bk.*, c.model, c.image, c.color, br.name AS brand_name, u.full_name AS user_name, u.email AS user_email
                        FROM bookings bk
                        JOIN cars c ON bk.car_id = c.id
                        JOIN brands br ON c.brand_id = br.id
                        JOIN users u ON bk.user_id = u.id
                        WHERE bk.id = ?";
                $params = [$id];
                
                // Non-admin can only view own bookings
                if (!isAdmin()) {
                    $sql .= " AND bk.user_id = ?";
                    $params[] = getCurrentUserId();
                }
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $booking = $stmt->fetch();
                
                if (!$booking) errorResponse('Booking not found.', 404);
                successResponse('Booking found.', ['booking' => $booking]);
            }
            
            // List bookings
            $where = [];
            $params = [];
            
            if (!isAdmin()) {
                $where[] = "bk.user_id = ?";
                $params[] = getCurrentUserId();
            } else {
                // Admin can filter by user
                $userId = intval($_GET['user_id'] ?? 0);
                if ($userId > 0) {
                    $where[] = "bk.user_id = ?";
                    $params[] = $userId;
                }
            }
            
            if ($status) {
                $where[] = "bk.status = ?";
                $params[] = $status;
            }
            
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // Count
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings bk {$whereClause}");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            $pagination = getPagination($total, $page);
            
            $stmt = $pdo->prepare("
                SELECT bk.*, c.model, c.image, c.color, br.name AS brand_name, u.full_name AS user_name
                FROM bookings bk
                JOIN cars c ON bk.car_id = c.id
                JOIN brands br ON c.brand_id = br.id
                JOIN users u ON bk.user_id = u.id
                {$whereClause}
                ORDER BY bk.created_at DESC
                LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
            ");
            $stmt->execute($params);
            $bookings = $stmt->fetchAll();
            
            successResponse('Bookings retrieved.', ['bookings' => $bookings, 'pagination' => $pagination]);
            break;
            
        case 'POST':
            // ---- Create Booking ----
            requireLogin();
            
            [$data, $errors] = getRequiredFields(['car_id', 'pickup_date', 'return_date']);
            if (!empty($errors)) errorResponse(implode(' ', $errors));
            
            $carId = intval($data['car_id']);
            $pickupDate = $data['pickup_date'];
            $returnDate = $data['return_date'];
            
            // Validate dates
            if (strtotime($pickupDate) >= strtotime($returnDate)) {
                errorResponse('Return date must be after pickup date.');
            }
            if (strtotime($pickupDate) < strtotime('today')) {
                errorResponse('Pickup date cannot be in the past.');
            }
            
            // Get car and check availability
            $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND status = 'available'");
            $stmt->execute([$carId]);
            $car = $stmt->fetch();
            if (!$car) errorResponse('Car not available.');
            
            // Check conflicts
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM bookings 
                WHERE car_id = ? AND status IN ('confirmed','active') 
                AND pickup_date < ? AND return_date > ?
            ");
            $stmt->execute([$carId, $returnDate, $pickupDate]);
            if ($stmt->fetchColumn() > 0) {
                errorResponse('Car is not available for these dates.');
            }
            
            // Calculate pricing
            $days = calculateDays($pickupDate, $returnDate);
            $totalAmount = $days * $car['price_per_day'];
            
            $stmt = $pdo->prepare("
                INSERT INTO bookings (user_id, car_id, pickup_date, return_date, pickup_location, return_location, total_days, daily_rate, total_amount, notes, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                getCurrentUserId(),
                $carId,
                $pickupDate,
                $returnDate,
                sanitize($_POST['pickup_location'] ?? 'Main Office'),
                sanitize($_POST['return_location'] ?? 'Main Office'),
                $days,
                $car['price_per_day'],
                $totalAmount,
                sanitize($_POST['notes'] ?? '')
            ]);
            
            successResponse('Booking created successfully! Awaiting confirmation.', [
                'booking_id' => $pdo->lastInsertId(),
                'total_days' => $days,
                'total_amount' => $totalAmount
            ], 201);
            break;
            
        case 'PUT':
            // ---- Update Booking Status ----
            requireLogin();
            parse_str(file_get_contents('php://input'), $putData);
            
            $id = intval($putData['id'] ?? 0);
            $newStatus = sanitize($putData['status'] ?? '');
            
            if ($id <= 0 || !$newStatus) errorResponse('Booking ID and status are required.');
            
            $validStatuses = ['pending', 'confirmed', 'active', 'completed', 'cancelled'];
            if (!in_array($newStatus, $validStatuses)) errorResponse('Invalid status.');
            
            // Check permission
            if (!isAdmin()) {
                // Users can only cancel their own bookings
                if ($newStatus !== 'cancelled') {
                    errorResponse('You can only cancel bookings.');
                }
                $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ? AND user_id = ? AND status IN ('pending','confirmed')");
                $stmt->execute([$newStatus, $id, getCurrentUserId()]);
            } else {
                $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
                $stmt->execute([$newStatus, $id]);
            }
            
            if ($stmt->rowCount() === 0) errorResponse('Booking not found or cannot be updated.');
            successResponse('Booking status updated to ' . $newStatus . '.');
            break;
            
        case 'DELETE':
            // ---- Cancel Booking ----
            requireLogin();
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) errorResponse('Booking ID is required.');
            
            if (!isAdmin()) {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status IN ('pending','confirmed')");
                $stmt->execute([$id, getCurrentUserId()]);
            } else {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
                $stmt->execute([$id]);
            }
            
            if ($stmt->rowCount() === 0) errorResponse('Booking not found or already processed.');
            successResponse('Booking cancelled successfully.');
            break;
            
        default:
            errorResponse('Method not allowed.', 405);
    }
    
} catch (PDOException $e) {
    error_log("Bookings API Error: " . $e->getMessage());
    errorResponse('An error occurred.', 500);
}
