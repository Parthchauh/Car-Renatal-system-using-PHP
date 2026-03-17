<?php
/**
 * Database Setup Script
 * Run this ONCE to create all tables and seed data
 * Access via: http://localhost/Car Rental system/setup.php
 */

// Prevent re-running accidentally
$lockFile = __DIR__ . '/.setup_done';

echo "<!DOCTYPE html><html><head><title>DriveElite - Database Setup</title>";
echo "<style>
    body { font-family: 'Segoe UI', sans-serif; background: #0A0A1A; color: #E0E0E0; padding: 40px; max-width: 800px; margin: 0 auto; }
    .success { color: #00B894; }
    .error { color: #FF6B6B; }
    .info { color: #00D2FF; }
    .warning { color: #FDCB6E; }
    h1 { color: #6C5CE7; }
    .step { background: rgba(255,255,255,0.05); padding: 10px 15px; margin: 8px 0; border-radius: 8px; border-left: 3px solid #6C5CE7; }
    .step.ok { border-left-color: #00B894; }
    .step.fail { border-left-color: #FF6B6B; }
    a { color: #00D2FF; }
    .btn { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #6C5CE7, #00D2FF); color: white; text-decoration: none; border-radius: 8px; margin-top: 20px; font-weight: 600; }
</style></head><body>";

echo "<h1>🚗 DriveElite - Database Setup</h1>";

// Check if already set up
if (file_exists($lockFile)) {
    echo "<div class='step ok'><span class='warning'>⚠️ Setup has already been completed.</span></div>";
    echo "<p>If you want to re-run setup, delete the file: <code>.setup_done</code> from the project root.</p>";
    echo "<a href='views/index.php' class='btn'>Go to Homepage →</a>";
    echo "</body></html>";
    exit;
}

$errors = 0;
$steps = 0;

// Step 1: Connect to MySQL
echo "<h2>Step 1: Database Connection</h2>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;port=3306;charset=utf8mb4",
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<div class='step ok'>✅ Connected to MySQL on <span class='info'>localhost:3306</span></div>";
    $steps++;
} catch (PDOException $e) {
    echo "<div class='step fail'>❌ <span class='error'>Connection failed: " . htmlspecialchars($e->getMessage()) . "</span></div>";
    echo "<p class='warning'>⚠️ Make sure MySQL/XAMPP is running on port 3306.</p>";
    echo "</body></html>";
    exit;
}

// Step 2: Use the car_rental database
echo "<h2>Step 2: Selecting Database</h2>";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `car_rental` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `car_rental`");
    echo "<div class='step ok'>✅ Using database: <span class='info'>car_rental</span></div>";
    $steps++;
} catch (PDOException $e) {
    echo "<div class='step fail'>❌ <span class='error'>" . htmlspecialchars($e->getMessage()) . "</span></div>";
    $errors++;
}

// Step 3: Create all tables
echo "<h2>Step 3: Creating Tables</h2>";

$tables = [
    'users' => "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `full_name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `phone` VARCHAR(20) DEFAULT NULL,
        `password` VARCHAR(255) NOT NULL,
        `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
        `profile_image` VARCHAR(255) DEFAULT 'default-avatar.png',
        `address` TEXT DEFAULT NULL,
        `reset_token` VARCHAR(255) DEFAULT NULL,
        `reset_token_expiry` DATETIME DEFAULT NULL,
        `status` ENUM('active','inactive','banned') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_users_email` (`email`),
        INDEX `idx_users_role` (`role`),
        INDEX `idx_users_status` (`status`)
    ) ENGINE=InnoDB",

    'brands' => "CREATE TABLE IF NOT EXISTS `brands` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL UNIQUE,
        `logo` VARCHAR(255) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",

    'cars' => "CREATE TABLE IF NOT EXISTS `cars` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `brand_id` INT NOT NULL,
        `model` VARCHAR(100) NOT NULL,
        `year` YEAR NOT NULL,
        `color` VARCHAR(50) DEFAULT NULL,
        `price_per_day` DECIMAL(10,2) NOT NULL,
        `fuel_type` ENUM('petrol','diesel','electric','hybrid') NOT NULL DEFAULT 'petrol',
        `transmission` ENUM('automatic','manual') NOT NULL DEFAULT 'automatic',
        `seats` INT NOT NULL DEFAULT 5,
        `category` ENUM('economy','compact','midsize','fullsize','suv','luxury','sports') NOT NULL DEFAULT 'economy',
        `image` VARCHAR(255) DEFAULT 'default-car.png',
        `gallery` JSON DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `features` TEXT DEFAULT NULL,
        `mileage` VARCHAR(50) DEFAULT NULL,
        `status` ENUM('available','rented','maintenance') NOT NULL DEFAULT 'available',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE,
        INDEX `idx_cars_brand` (`brand_id`),
        INDEX `idx_cars_status` (`status`),
        INDEX `idx_cars_category` (`category`),
        INDEX `idx_cars_price` (`price_per_day`)
    ) ENGINE=InnoDB",

    'bookings' => "CREATE TABLE IF NOT EXISTS `bookings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `car_id` INT NOT NULL,
        `pickup_date` DATE NOT NULL,
        `return_date` DATE NOT NULL,
        `pickup_location` VARCHAR(255) DEFAULT 'Main Office',
        `return_location` VARCHAR(255) DEFAULT 'Main Office',
        `total_days` INT NOT NULL,
        `daily_rate` DECIMAL(10,2) NOT NULL,
        `total_amount` DECIMAL(10,2) NOT NULL,
        `status` ENUM('pending','confirmed','active','completed','cancelled') NOT NULL DEFAULT 'pending',
        `notes` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE,
        INDEX `idx_bookings_user` (`user_id`),
        INDEX `idx_bookings_car` (`car_id`),
        INDEX `idx_bookings_status` (`status`),
        INDEX `idx_bookings_dates` (`pickup_date`, `return_date`)
    ) ENGINE=InnoDB",

    'testimonials' => "CREATE TABLE IF NOT EXISTS `testimonials` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `booking_id` INT DEFAULT NULL,
        `rating` TINYINT NOT NULL DEFAULT 5,
        `review` TEXT NOT NULL,
        `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE SET NULL,
        INDEX `idx_testimonials_user` (`user_id`),
        INDEX `idx_testimonials_status` (`status`)
    ) ENGINE=InnoDB",

    'contact_queries' => "CREATE TABLE IF NOT EXISTS `contact_queries` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `subject` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `status` ENUM('new','read','replied','closed') NOT NULL DEFAULT 'new',
        `admin_notes` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_contact_status` (`status`)
    ) ENGINE=InnoDB",

    'subscribers' => "CREATE TABLE IF NOT EXISTS `subscribers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `status` ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_subscribers_email` (`email`)
    ) ENGINE=InnoDB",

    'site_content' => "CREATE TABLE IF NOT EXISTS `site_content` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `page_key` VARCHAR(100) NOT NULL UNIQUE,
        `title` VARCHAR(255) NOT NULL,
        `content` LONGTEXT NOT NULL,
        `meta_description` VARCHAR(500) DEFAULT NULL,
        `status` ENUM('active','draft') NOT NULL DEFAULT 'active',
        `updated_by` INT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        INDEX `idx_content_key` (`page_key`)
    ) ENGINE=InnoDB"
];

foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "<div class='step ok'>✅ Table <span class='info'>`$name`</span> created</div>";
        $steps++;
    } catch (PDOException $e) {
        echo "<div class='step fail'>❌ Table `$name`: <span class='error'>" . htmlspecialchars($e->getMessage()) . "</span></div>";
        $errors++;
    }
}

// Step 4: Seed Data
echo "<h2>Step 4: Seeding Data</h2>";

// Check if data already exists
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

if ($userCount > 0) {
    echo "<div class='step ok'><span class='warning'>⚠️ Data already exists ($userCount users found). Skipping seed to avoid duplicates.</span></div>";
} else {
    $seeds = [
        'Users (3 accounts)' => "INSERT INTO `users` (`full_name`, `email`, `phone`, `password`, `role`, `status`) VALUES
            ('System Admin', 'admin@carrental.com', '+1234567890', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'),
            ('John Doe', 'john@example.com', '+1987654321', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active'),
            ('Jane Smith', 'jane@example.com', '+1122334455', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active')",

        'Brands (8 brands)' => "INSERT INTO `brands` (`name`, `description`, `status`) VALUES
            ('Toyota', 'Reliable Japanese automaker known for quality and durability', 'active'),
            ('BMW', 'German luxury vehicle manufacturer', 'active'),
            ('Mercedes-Benz', 'Premium German automotive brand', 'active'),
            ('Honda', 'Japanese manufacturer known for innovation', 'active'),
            ('Tesla', 'Leading electric vehicle manufacturer', 'active'),
            ('Audi', 'German luxury automobile manufacturer', 'active'),
            ('Ford', 'American multinational automobile manufacturer', 'active'),
            ('Hyundai', 'South Korean multinational automotive manufacturer', 'active')",

        'Cars (12 vehicles)' => "INSERT INTO `cars` (`brand_id`, `model`, `year`, `color`, `price_per_day`, `fuel_type`, `transmission`, `seats`, `category`, `description`, `features`, `mileage`, `image`, `status`) VALUES
            (1, 'Camry', 2024, 'Pearl White', 65.00, 'petrol', 'automatic', 5, 'midsize', 'The Toyota Camry offers exceptional comfort and reliability.', 'Bluetooth, Backup Camera, Lane Assist, Apple CarPlay', '32 MPG', 'https://images.pexels.com/photos/170811/pexels-photo-170811.jpeg', 'available'),
            (1, 'RAV4', 2024, 'Midnight Black', 80.00, 'hybrid', 'automatic', 5, 'suv', 'Adventure-ready SUV with hybrid efficiency.', 'AWD, Sunroof, Heated Seats, Adaptive Cruise Control', '38 MPG', 'https://images.unsplash.com/photo-1459356979461-dae1b8dcb07b', 'available'),
            (2, '3 Series', 2024, 'Alpine White', 120.00, 'petrol', 'automatic', 5, 'luxury', 'The BMW 3 Series delivers the ultimate driving experience.', 'Sport Package, Navigation, Leather Seats, Harman Kardon Sound', '30 MPG', 'https://images.unsplash.com/photo-1461632830798-3adb3034e4c8', 'available'),
            (2, 'X5', 2024, 'Space Gray', 180.00, 'diesel', 'automatic', 7, 'suv', 'Luxury SUV perfect for family trips.', 'Panoramic Roof, Third Row, Ambient Lighting, Gesture Control', '25 MPG', 'https://images.pexels.com/photos/358070/pexels-photo-358070.jpeg', 'available'),
            (3, 'C-Class', 2024, 'Obsidian Black', 130.00, 'petrol', 'automatic', 5, 'luxury', 'Elegant and powerful Mercedes-Benz sedan.', 'MBUX System, Burmester Sound, Digital Cockpit, Wireless Charging', '29 MPG', 'https://images.unsplash.com/photo-1517841905240-472988babdf9', 'available'),
            (3, 'GLE', 2024, 'Selenite Gray', 200.00, 'diesel', 'automatic', 7, 'suv', 'Premium SUV with commanding presence.', 'Air Suspension, 360 Camera, MBUX, Massage Seats', '24 MPG', 'https://images.pexels.com/photos/358489/pexels-photo-358489.jpeg', 'available'),
            (4, 'Civic', 2024, 'Rallye Red', 55.00, 'petrol', 'manual', 5, 'compact', 'Sporty and efficient compact sedan.', 'Honda Sensing, Bose Audio, Wireless CarPlay, LED Headlights', '36 MPG', 'https://images.unsplash.com/photo-1502877338535-766e1452684a', 'available'),
            (5, 'Model 3', 2024, 'Deep Blue', 150.00, 'electric', 'automatic', 5, 'luxury', 'All-electric performance sedan with autopilot.', 'Autopilot, Glass Roof, 15-inch Touchscreen, OTA Updates', '358 mi Range', 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d', 'available'),
            (5, 'Model S', 2024, 'Red Multi-Coat', 170.00, 'electric', 'automatic', 7, 'suv', 'Electric SUV with impressive range and space.', 'Full Self-Driving Capable, Camp Mode, Premium Audio', '330 mi Range', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70', 'available'),
            (6, 'A4', 2024, 'Navarra Blue', 110.00, 'petrol', 'automatic', 5, 'luxury', 'Refined luxury sedan with quattro AWD.', 'Virtual Cockpit, B&O Sound, Matrix LED, quattro AWD', '28 MPG', 'https://images.unsplash.com/photo-1511390835673-02e273e6b0e7', 'available'),
            (7, 'Mustang', 2024, 'Race Red', 140.00, 'petrol', 'manual', 4, 'sports', 'Iconic American muscle car experience.', 'V8 Engine, Track Mode, Recaro Seats, MagneRide Suspension', '22 MPG', 'https://images.unsplash.com/photo-1549921296-a0108b3a0664', 'available'),
            (8, 'Tucson', 2024, 'Amazon Gray', 60.00, 'hybrid', 'automatic', 5, 'compact', 'Modern compact SUV with hybrid efficiency.', 'BlueLink, Wireless Charging, Bose Audio, Smart Liftgate', '38 MPG', 'https://images.pexels.com/photos/1707826/pexels-photo-1707826.jpeg', 'available')",

        'Bookings (4 samples)' => "INSERT INTO `bookings` (`user_id`, `car_id`, `pickup_date`, `return_date`, `total_days`, `daily_rate`, `total_amount`, `status`) VALUES
            (2, 1, '2026-03-20', '2026-03-25', 5, 65.00, 325.00, 'confirmed'),
            (2, 3, '2026-04-01', '2026-04-05', 4, 120.00, 480.00, 'pending'),
            (3, 8, '2026-03-18', '2026-03-22', 4, 150.00, 600.00, 'active'),
            (3, 5, '2026-02-10', '2026-02-15', 5, 130.00, 650.00, 'completed')",

        'Testimonials (3 reviews)' => "INSERT INTO `testimonials` (`user_id`, `booking_id`, `rating`, `review`, `status`) VALUES
            (2, 1, 5, 'Excellent service! The Toyota Camry was in perfect condition and the booking process was seamless.', 'approved'),
            (3, 4, 4, 'Great experience renting the Mercedes C-Class. Very clean car and friendly staff.', 'approved'),
            (2, NULL, 5, 'Best car rental service in the city! Affordable prices and fantastic selection.', 'approved')",

        'Contact Queries (2 samples)' => "INSERT INTO `contact_queries` (`name`, `email`, `phone`, `subject`, `message`, `status`) VALUES
            ('Mike Wilson', 'mike@example.com', '+1555666777', 'Corporate Rental Inquiry', 'We are interested in setting up a corporate account. Could you provide bulk pricing details?', 'new'),
            ('Sarah Brown', 'sarah@example.com', NULL, 'Long-term Rental', 'I need a car for 3 months. Do you offer monthly discounts?', 'read')",

        'Subscribers (3 emails)' => "INSERT INTO `subscribers` (`email`) VALUES
            ('subscriber1@example.com'),
            ('subscriber2@example.com'),
            ('newsletter@example.com')",

        'Site Content (3 pages)' => "INSERT INTO `site_content` (`page_key`, `title`, `content`, `meta_description`) VALUES
            ('about', 'About DriveElite', '<h3>Who We Are</h3><p>DriveElite is a premier car rental service dedicated to providing exceptional vehicles and outstanding customer experiences. Founded in 2020, we have grown to become one of the most trusted names in the car rental industry.</p><h3>Our Mission</h3><p>To make luxury and reliable transportation accessible to everyone through competitive pricing, a diverse fleet, and unmatched customer service.</p><h3>Why Choose Us?</h3><ul><li>Fleet of 500+ well-maintained vehicles</li><li>24/7 roadside assistance</li><li>Flexible booking and cancellation policies</li><li>Competitive pricing with no hidden fees</li><li>GPS navigation included in all vehicles</li></ul>', 'DriveElite - Premier car rental service with 500+ vehicles.'),
            ('faq', 'Frequently Asked Questions', '<div class=\"faq-item\"><h5>What documents do I need to rent a car?</h5><p>You need a valid drivers license, a credit/debit card, and a government-issued photo ID.</p></div><div class=\"faq-item\"><h5>What is the minimum age to rent?</h5><p>The minimum age is 21 years. Drivers under 25 may incur a surcharge.</p></div><div class=\"faq-item\"><h5>Can I cancel my booking?</h5><p>Yes! Free cancellation up to 24 hours before pickup.</p></div><div class=\"faq-item\"><h5>Is insurance included?</h5><p>Basic insurance is included with all rentals.</p></div><div class=\"faq-item\"><h5>Do you offer airport pickup?</h5><p>Yes, complimentary airport transfers for all bookings.</p></div><div class=\"faq-item\"><h5>What fuel policy do you follow?</h5><p>All cars come with a full tank. Please return with a full tank.</p></div>', 'FAQ about DriveElite car rental services.'),
            ('homepage', 'Welcome to DriveElite', '<p>Experience the freedom of the open road with DriveElite premium car rental services.</p>', 'DriveElite - Premium car rental service.')"
    ];

    foreach ($seeds as $label => $sql) {
        try {
            $pdo->exec($sql);
            echo "<div class='step ok'>✅ Seeded: <span class='info'>$label</span></div>";
            $steps++;
        } catch (PDOException $e) {
            echo "<div class='step fail'>❌ Seed $label: <span class='error'>" . htmlspecialchars($e->getMessage()) . "</span></div>";
            $errors++;
        }
    }
}

// Final Summary
echo "<h2>Setup Complete!</h2>";

if ($errors === 0) {
    // Create lock file
    file_put_contents($lockFile, date('Y-m-d H:i:s'));
    
    echo "<div class='step ok' style='font-size:1.1rem;'>";
    echo "🎉 <strong class='success'>All $steps steps completed successfully!</strong>";
    echo "</div>";
    
    echo "<div style='background:rgba(108,92,231,0.1);padding:20px;border-radius:12px;margin:20px 0;border:1px solid rgba(108,92,231,0.3);'>";
    echo "<h3 style='color:#6C5CE7;margin-top:0;'>📋 Demo Login Credentials</h3>";
    echo "<table style='width:100%;border-collapse:collapse;'>";
    echo "<tr style='border-bottom:1px solid rgba(255,255,255,0.1);'><td style='padding:8px;'><strong>Admin</strong></td><td style='padding:8px;'><code>admin@carrental.com</code></td><td style='padding:8px;'><code>password</code></td></tr>";
    echo "<tr style='border-bottom:1px solid rgba(255,255,255,0.1);'><td style='padding:8px;'><strong>User</strong></td><td style='padding:8px;'><code>john@example.com</code></td><td style='padding:8px;'><code>password</code></td></tr>";
    echo "<tr><td style='padding:8px;'><strong>User</strong></td><td style='padding:8px;'><code>jane@example.com</code></td><td style='padding:8px;'><code>password</code></td></tr>";
    echo "</table>";
    echo "</div>";
    
    echo "<a href='views/index.php' class='btn'>🚗 Launch DriveElite →</a>";
} else {
    echo "<div class='step fail'><span class='error'>⚠️ $errors error(s) occurred. Please fix and try again.</span></div>";
}

echo "</body></html>";
