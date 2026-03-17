<?php
/**
 * Dashboard Analytics API
 * GET: Summary stats for admin dashboard
 */

require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed.', 405);
}

requireAdmin();

try {
    $pdo = getDBConnection();
    
    // Total counts
    $stats = [];
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $stats['total_users'] = intval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM cars");
    $stats['total_cars'] = intval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'available'");
    $stats['available_cars'] = intval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings");
    $stats['total_bookings'] = intval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
    $stats['pending_bookings'] = intval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'active'");
    $stats['active_bookings'] = intval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status IN ('confirmed','active','completed')");
    $stats['total_revenue'] = floatval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_queries WHERE status = 'new'");
    $stats['new_queries'] = intval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM testimonials WHERE status = 'pending'");
    $stats['pending_reviews'] = intval($stmt->fetchColumn());
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM subscribers WHERE status = 'active'");
    $stats['total_subscribers'] = intval($stmt->fetchColumn());
    
    // Monthly revenue (last 6 months)
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') AS month,
            SUM(total_amount) AS revenue,
            COUNT(*) AS booking_count
        FROM bookings 
        WHERE status IN ('confirmed','active','completed')
        AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $stats['monthly_revenue'] = $stmt->fetchAll();
    
    // Bookings by status
    $stmt = $pdo->query("
        SELECT status, COUNT(*) AS count 
        FROM bookings 
        GROUP BY status
    ");
    $stats['bookings_by_status'] = $stmt->fetchAll();
    
    // Top rented cars
    $stmt = $pdo->query("
        SELECT c.model, b.name AS brand_name, COUNT(bk.id) AS rental_count, SUM(bk.total_amount) AS revenue
        FROM bookings bk
        JOIN cars c ON bk.car_id = c.id
        JOIN brands b ON c.brand_id = b.id
        GROUP BY c.id
        ORDER BY rental_count DESC
        LIMIT 5
    ");
    $stats['top_cars'] = $stmt->fetchAll();
    
    // Cars by category
    $stmt = $pdo->query("
        SELECT category, COUNT(*) AS count 
        FROM cars 
        GROUP BY category 
        ORDER BY count DESC
    ");
    $stats['cars_by_category'] = $stmt->fetchAll();
    
    // Recent bookings
    $stmt = $pdo->query("
        SELECT bk.*, c.model, b.name AS brand_name, u.full_name AS user_name
        FROM bookings bk
        JOIN cars c ON bk.car_id = c.id
        JOIN brands b ON c.brand_id = b.id
        JOIN users u ON bk.user_id = u.id
        ORDER BY bk.created_at DESC
        LIMIT 5
    ");
    $stats['recent_bookings'] = $stmt->fetchAll();
    
    successResponse('Dashboard data retrieved.', ['stats' => $stats]);
    
} catch (PDOException $e) {
    error_log("Dashboard API Error: " . $e->getMessage());
    errorResponse('Failed to load dashboard data.', 500);
}
