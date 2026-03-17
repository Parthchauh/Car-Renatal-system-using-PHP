<?php
/**
 * Application Configuration
 * Keep credentials out of source control in production
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'car_rental');
define('DB_USER', 'root');
define('DB_PASS', '');          // Default XAMPP password is empty
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'DriveElite');
define('APP_URL', 'http://localhost/Car Rental system');
define('APP_VERSION', '1.0.0');

// File Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('REMEMBER_ME_LIFETIME', 30 * 24 * 3600); // 30 days

// Pagination
define('ITEMS_PER_PAGE', 10);

// CSRF Token Name
define('CSRF_TOKEN_NAME', 'csrf_token');
