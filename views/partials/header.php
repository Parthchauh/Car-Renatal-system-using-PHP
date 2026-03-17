<?php
/**
 * Shared Header Component
 * Include at top of every public page
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

$csrfToken = generateCSRFToken();
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $csrfToken ?>">
    <meta name="description" content="<?= $pageDescription ?? 'DriveElite - Premium Car Rental Service. Book luxury, economy, and sport cars at the best rates.' ?>">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' . APP_NAME : APP_NAME . ' - Premium Car Rentals' ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= APP_URL ?>/views/index.php">
            <i class="fas fa-car-side brand-icon"></i> DriveElite
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>" href="<?= APP_URL ?>/views/index.php">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'cars' ? 'active' : '' ?>" href="<?= APP_URL ?>/views/cars.php">
                        <i class="fas fa-car me-1"></i> Cars
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>" href="<?= APP_URL ?>/views/about.php">
                        <i class="fas fa-info-circle me-1"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'faq' ? 'active' : '' ?>" href="<?= APP_URL ?>/views/faq.php">
                        <i class="fas fa-question-circle me-1"></i> FAQ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>" href="<?= APP_URL ?>/views/contact.php">
                        <i class="fas fa-envelope me-1"></i> Contact
                    </a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-2">
                <?php if ($currentUser): ?>
                    <div class="dropdown">
                        <button class="btn btn-glass dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($currentUser['name']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="background:var(--bg-card);border:1px solid var(--border-color);">
                            <?php if ($currentUser['role'] === 'admin'): ?>
                                <li><a class="dropdown-item text-light" href="<?= APP_URL ?>/views/admin/index.php"><i class="fas fa-tachometer-alt me-2"></i>Admin Panel</a></li>
                                <li><hr class="dropdown-divider" style="border-color:var(--border-color)"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item text-light" href="<?= APP_URL ?>/views/dashboard.php"><i class="fas fa-columns me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item text-light" href="<?= APP_URL ?>/views/profile.php"><i class="fas fa-user-edit me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item text-light" href="<?= APP_URL ?>/views/my-bookings.php"><i class="fas fa-calendar-check me-2"></i>My Bookings</a></li>
                            <li><hr class="dropdown-divider" style="border-color:var(--border-color)"></li>
                            <li><a class="dropdown-item text-danger" href="#" id="logoutBtn"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/views/login.php" class="btn btn-glass btn-sm">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="<?= APP_URL ?>/views/register.php" class="nav-btn nav-link btn-sm">
                        <i class="fas fa-user-plus me-1"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
