<?php
/**
 * Admin Sidebar Partial
 */
require_once __DIR__ . '/../../../includes/session.php';
require_once __DIR__ . '/../../../includes/functions.php';
requireAdmin(false);

$csrfToken = generateCSRFToken();
$currentUser = getCurrentUser();
$adminPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $csrfToken ?>">
    <title><?= isset($pageTitle) ? $pageTitle . ' | Admin' : 'Admin Panel' ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>

<!-- Mobile Toggle -->
<button class="btn btn-glass d-lg-none position-fixed" style="top:15px;left:15px;z-index:1060;" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-car-side"></i>
        <span>DriveElite</span>
    </div>
    
    <div class="sidebar-section-title">Main</div>
    <ul class="nav flex-column sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'index' ? 'active' : '' ?>" href="index.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
    </ul>
    
    <div class="sidebar-section-title">Fleet</div>
    <ul class="nav flex-column sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'brands' ? 'active' : '' ?>" href="brands.php">
                <i class="fas fa-tags"></i> Brands
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'cars' ? 'active' : '' ?>" href="cars.php">
                <i class="fas fa-car"></i> Vehicles
            </a>
        </li>
    </ul>
    
    <div class="sidebar-section-title">Business</div>
    <ul class="nav flex-column sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'bookings' ? 'active' : '' ?>" href="bookings.php">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'users' ? 'active' : '' ?>" href="users.php">
                <i class="fas fa-users"></i> Users
            </a>
        </li>
    </ul>
    
    <div class="sidebar-section-title">Engagement</div>
    <ul class="nav flex-column sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'testimonials' ? 'active' : '' ?>" href="testimonials.php">
                <i class="fas fa-star"></i> Reviews
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'contacts' ? 'active' : '' ?>" href="contacts.php">
                <i class="fas fa-envelope"></i> Inquiries
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'subscribers' ? 'active' : '' ?>" href="subscribers.php">
                <i class="fas fa-bell"></i> Subscribers
            </a>
        </li>
    </ul>
    
    <div class="sidebar-section-title">Settings</div>
    <ul class="nav flex-column sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === 'content' ? 'active' : '' ?>" href="content.php">
                <i class="fas fa-file-alt"></i> Content
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= APP_URL ?>/views/index.php">
                <i class="fas fa-globe"></i> View Site
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="#" id="adminLogout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>

<main class="admin-main">
    <!-- Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0" style="font-family:var(--font-primary);"><?= $pageTitle ?? 'Admin' ?></h4>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary small d-none d-md-inline"><i class="fas fa-user-shield me-1"></i> <?= htmlspecialchars($currentUser['name']) ?></span>
        </div>
    </div>
