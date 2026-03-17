# 🚗 DriveElite - Premium Car Rental System

DriveElite is a modern, full-stack car rental management system built with **PHP, MySQL, and AJAX**. It features a premium dark-themed UI with glassmorphism aesthetics, supporting distinct modules for Guests, Registered Users, and Administrators.

![DriveElite Hero](https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80)

## ✨ Key Features

### 👤 Guest Module
- **Browse Fleet**: Search and filter cars by brand, category, fuel type, and price.
- **Dynamic Content**: Explore About Us, FAQ, and Contact pages managed via Admin.
- **Micro-Interactions**: Smooth animations and real-time status updates without page reloads.

### 🔐 User Module
- **Secure Auth**: Registration, Login, and Password Reset with hashed security.
- **Smart Booking**: Check real-time availability and calculate total costs instantly.
- **History & Profile**: View booking history, manage personal details, and leave reviews.
- **Dashboard**: Personal overview of active and past rentals.

### 🛡️ Admin Module
- **Analytics Dashboard**: Visual charts for revenue and booking trends using **Chart.js**.
- **Fleet Management**: CRUD operations for Vehicles and Brands with image upload support.
- **Reservation Control**: Manage and update statuses for all customer bookings.
- **Content Management**: Edit site pages directly from the admin panel with an integrated editor.
- **User Moderation**: Manage user accounts, moderate reviews, and respond to inquiries.

---

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3 (Custom Design System), JavaScript (ES6+ AJAX)
- **Styling**: Bootstrap 5 + Glassmorphism + Font Awesome 6
- **Backend**: PHP 8.x (Modular Architecture)
- **Database**: MySQL (PDO with Prepared Statements)
- **Libraries**: Chart.js, Flatpickr, Google Fonts

---

## 🚀 Installation Guide (XAMPP)

1. **Clone the Project**:
   ```bash
   git clone https://github.com/Parthchauh/Car-Renatal-system-using-PHP.git
   ```

2. **Move to htdocs**:
   Place the project folder in `C:\xampp\htdocs\Car Rental system`.

3. **Database Setup**:
   - Start **Apache** and **MySQL** in XAMPP.
   - Run the automated setup script by visiting:
     `http://localhost/Car Rental system/setup.php`
   - This script will automatically create the `car_rental` database, all 8 required tables, and seed them with demo data.

4. **Launch Application**:
   Visit `http://localhost/Car Rental system/` in your browser.

---

## 📋 Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@carrental.com` | `password` |
| **User** | `john@example.com` | `password` |

---

## 📂 Project Structure

```text
├── api/             # Backend AJAX endpoints (auth, cars, bookings, etc.)
├── assets/          # CSS, JS, and image uploads
├── includes/        # Core configuration, DB connect, and shared functions
├── views/           # Frontend pages (Guest, User, Admin protected areas)
├── setup.php        # Automated database initialization script
└── database.sql     # Raw SQL schema and seed data
```

---

## 🤝 Contribution

Feel free to fork this project, report issues, or submit pull requests to improve the system!

**Designed & Developed by Parth Chauhan**
