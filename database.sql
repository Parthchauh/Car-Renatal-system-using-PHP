-- ============================================
-- Car Rental System - Database Schema
-- MySQL (XAMPP) Compatible
-- ============================================

CREATE DATABASE IF NOT EXISTS `car_rental` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `car_rental`;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE `users` (
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
) ENGINE=InnoDB;

-- ============================================
-- 2. BRANDS TABLE
-- ============================================
CREATE TABLE `brands` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `logo` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- 3. CARS TABLE
-- ============================================
CREATE TABLE `cars` (
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
) ENGINE=InnoDB;

-- ============================================
-- 4. BOOKINGS TABLE
-- ============================================
CREATE TABLE `bookings` (
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
) ENGINE=InnoDB;

-- ============================================
-- 5. TESTIMONIALS TABLE
-- ============================================
CREATE TABLE `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `booking_id` INT DEFAULT NULL,
  `rating` TINYINT NOT NULL DEFAULT 5 CHECK (`rating` BETWEEN 1 AND 5),
  `review` TEXT NOT NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE SET NULL,
  INDEX `idx_testimonials_user` (`user_id`),
  INDEX `idx_testimonials_status` (`status`)
) ENGINE=InnoDB;

-- ============================================
-- 6. CONTACT QUERIES TABLE
-- ============================================
CREATE TABLE `contact_queries` (
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
) ENGINE=InnoDB;

-- ============================================
-- 7. SUBSCRIBERS TABLE
-- ============================================
CREATE TABLE `subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `status` ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_subscribers_email` (`email`)
) ENGINE=InnoDB;

-- ============================================
-- 8. SITE CONTENT TABLE
-- ============================================
CREATE TABLE `site_content` (
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
) ENGINE=InnoDB;

-- ============================================
-- SEED DATA
-- ============================================

-- Admin user (password: Admin@123)
INSERT INTO `users` (`full_name`, `email`, `phone`, `password`, `role`, `status`) VALUES
('System Admin', 'admin@carrental.com', '+1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'),
('John Doe', 'john@example.com', '+1987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active'),
('Jane Smith', 'jane@example.com', '+1122334455', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active');

-- Brands
INSERT INTO `brands` (`name`, `description`, `status`) VALUES
('Toyota', 'Reliable Japanese automaker known for quality and durability', 'active'),
('BMW', 'German luxury vehicle manufacturer', 'active'),
('Mercedes-Benz', 'Premium German automotive brand', 'active'),
('Honda', 'Japanese manufacturer known for innovation', 'active'),
('Tesla', 'Leading electric vehicle manufacturer', 'active'),
('Audi', 'German luxury automobile manufacturer', 'active'),
('Ford', 'American multinational automobile manufacturer', 'active'),
('Hyundai', 'South Korean multinational automotive manufacturer', 'active');

-- Cars
INSERT INTO `cars` (`brand_id`, `model`, `year`, `color`, `price_per_day`, `fuel_type`, `transmission`, `seats`, `category`, `description`, `features`, `mileage`, `image`, `status`) VALUES
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
(8, 'Tucson', 2024, 'Amazon Gray', 60.00, 'hybrid', 'automatic', 5, 'compact', 'Modern compact SUV with hybrid efficiency.', 'BlueLink, Wireless Charging, Bose Audio, Smart Liftgate', '38 MPG', 'https://images.pexels.com/photos/1707826/pexels-photo-1707826.jpeg', 'available');

-- Sample Bookings
INSERT INTO `bookings` (`user_id`, `car_id`, `pickup_date`, `return_date`, `total_days`, `daily_rate`, `total_amount`, `status`) VALUES
(2, 1, '2026-03-20', '2026-03-25', 5, 65.00, 325.00, 'confirmed'),
(2, 3, '2026-04-01', '2026-04-05', 4, 120.00, 480.00, 'pending'),
(3, 8, '2026-03-18', '2026-03-22', 4, 150.00, 600.00, 'active'),
(3, 5, '2026-02-10', '2026-02-15', 5, 130.00, 650.00, 'completed');

-- Sample Testimonials
INSERT INTO `testimonials` (`user_id`, `booking_id`, `rating`, `review`, `status`) VALUES
(2, 1, 5, 'Excellent service! The Toyota Camry was in perfect condition and the booking process was seamless. Will definitely rent again.', 'approved'),
(3, 4, 4, 'Great experience renting the Mercedes C-Class. Very clean car and friendly staff. Only giving 4 stars because pickup took a bit longer than expected.', 'approved'),
(2, NULL, 5, 'Best car rental service in the city! Affordable prices and a fantastic selection of vehicles. Highly recommended!', 'approved');

-- Sample Contact Queries
INSERT INTO `contact_queries` (`name`, `email`, `phone`, `subject`, `message`, `status`) VALUES
('Mike Wilson', 'mike@example.com', '+1555666777', 'Corporate Rental Inquiry', 'We are interested in setting up a corporate account for our company. Could you provide bulk pricing details?', 'new'),
('Sarah Brown', 'sarah@example.com', NULL, 'Long-term Rental', 'I need a car for 3 months. Do you offer monthly discounts?', 'read');

-- Sample Subscribers
INSERT INTO `subscribers` (`email`) VALUES
('subscriber1@example.com'),
('subscriber2@example.com'),
('newsletter@example.com');

-- Site Content
INSERT INTO `site_content` (`page_key`, `title`, `content`, `meta_description`) VALUES
('about', 'About DriveElite', '<h3>Who We Are</h3><p>DriveElite is a premier car rental service dedicated to providing exceptional vehicles and outstanding customer experiences. Founded in 2020, we have grown to become one of the most trusted names in the car rental industry.</p><h3>Our Mission</h3><p>To make luxury and reliable transportation accessible to everyone through competitive pricing, a diverse fleet, and unmatched customer service.</p><h3>Why Choose Us?</h3><ul><li>Fleet of 500+ well-maintained vehicles</li><li>24/7 roadside assistance</li><li>Flexible booking and cancellation policies</li><li>Competitive pricing with no hidden fees</li><li>GPS navigation included in all vehicles</li></ul>', 'DriveElite - Premier car rental service with 500+ vehicles. Affordable luxury car rentals with 24/7 support.'),
('faq', 'Frequently Asked Questions', '<div class="faq-item"><h5>What documents do I need to rent a car?</h5><p>You need a valid driver''s license, a credit/debit card, and a government-issued photo ID. International renters also need a passport and international driving permit.</p></div><div class="faq-item"><h5>What is the minimum age to rent a car?</h5><p>The minimum age is 21 years. Drivers under 25 may be subject to a young driver surcharge.</p></div><div class="faq-item"><h5>Can I cancel my booking?</h5><p>Yes! Free cancellation is available up to 24 hours before your pickup time. Cancellations within 24 hours may incur a fee.</p></div><div class="faq-item"><h5>Is insurance included?</h5><p>Basic insurance is included with all rentals. Premium coverage options are available at checkout.</p></div><div class="faq-item"><h5>Do you offer airport pickup/drop-off?</h5><p>Yes, we offer complimentary airport transfers for all bookings. Just select your airport terminal during booking.</p></div><div class="faq-item"><h5>What fuel policy do you follow?</h5><p>All cars are provided with a full tank. Please return the car with a full tank to avoid refueling charges.</p></div>', 'Frequently asked questions about DriveElite car rental services including booking, cancellation, insurance, and more.'),
('homepage', 'Welcome to DriveElite', '<p>Experience the freedom of the open road with DriveElite premium car rental services. Choose from our fleet of luxury, economy, and sport vehicles.</p>', 'DriveElite - Premium car rental service. Book luxury and economy cars at the best rates.');
