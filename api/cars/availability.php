<?php
/**
 * Cars API - Check Availability
 * GET: car_id, pickup_date, return_date
 */

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$carId = intval($_GET['car_id'] ?? 0);
$pickupDate = sanitize($_GET['pickup_date'] ?? '');
$returnDate = sanitize($_GET['return_date'] ?? '');

if ($carId <= 0 || !$pickupDate || !$returnDate) {
    errorResponse('Car ID, pickup date, and return date are required.');
}

if (strtotime($pickupDate) >= strtotime($returnDate)) {
    errorResponse('Return date must be after pickup date.');
}

if (strtotime($pickupDate) < strtotime('today')) {
    errorResponse('Pickup date cannot be in the past.');
}

try {
    $pdo = getDBConnection();
    
    // Check car exists and is not in maintenance
    $stmt = $pdo->prepare("SELECT id, status, price_per_day FROM cars WHERE id = ?");
    $stmt->execute([$carId]);
    $car = $stmt->fetch();
    
    if (!$car) errorResponse('Car not found.', 404);
    if ($car['status'] === 'maintenance') {
        successResponse('Car is currently under maintenance.', ['available' => false]);
    }
    
    // Check for overlapping bookings
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM bookings 
        WHERE car_id = ? 
        AND status IN ('confirmed', 'active')
        AND pickup_date < ? 
        AND return_date > ?
    ");
    $stmt->execute([$carId, $returnDate, $pickupDate]);
    $conflicts = $stmt->fetchColumn();
    
    $days = calculateDays($pickupDate, $returnDate);
    $totalAmount = $days * $car['price_per_day'];
    
    successResponse('Availability checked.', [
        'available' => $conflicts == 0,
        'days' => $days,
        'daily_rate' => $car['price_per_day'],
        'total_amount' => $totalAmount
    ]);
    
} catch (PDOException $e) {
    error_log("Availability Check Error: " . $e->getMessage());
    errorResponse('Failed to check availability.', 500);
}
