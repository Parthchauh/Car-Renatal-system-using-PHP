<?php
/**
 * User Dashboard
 */
$pageTitle = 'Dashboard';
require_once 'partials/header.php';
requireLogin(false);
?>

<div class="page-header" style="min-height:auto;padding:7rem 0 2rem;">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">My <span class="gradient-text">Dashboard</span></h1>
    </div>
</div>

<section class="section" style="padding:2rem 0 5rem;">
    <div class="container">
        <!-- Welcome -->
        <div class="dashboard-welcome fade-in-up">
            <div class="d-flex align-items-center gap-3">
                <div style="width:55px;height:55px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:white;font-weight:700;">
                    <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                </div>
                <div>
                    <h2>Welcome back, <?= htmlspecialchars($currentUser['name']) ?>!</h2>
                    <p>Here's an overview of your activity.</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6 fade-in-up stagger-1">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,rgba(108,92,231,0.15),rgba(108,92,231,0.05));color:var(--primary-light);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value" id="totalBookings">-</div>
                    <div class="stat-label">Total Bookings</div>
                </div>
            </div>
            <div class="col-md-3 col-6 fade-in-up stagger-2">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,rgba(0,184,148,0.15),rgba(0,184,148,0.05));color:var(--success-light);">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="stat-value" id="activeBookings">-</div>
                    <div class="stat-label">Active Rentals</div>
                </div>
            </div>
            <div class="col-md-3 col-6 fade-in-up stagger-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,rgba(253,203,110,0.15),rgba(253,203,110,0.05));color:var(--warning);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value" id="pendingBookings">-</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="col-md-3 col-6 fade-in-up stagger-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,rgba(0,210,255,0.15),rgba(0,210,255,0.05));color:var(--accent);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-value" id="totalSpent">-</div>
                    <div class="stat-label">Total Spent</div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <a href="cars.php" class="btn btn-glass w-100"><i class="fas fa-car me-2"></i>Browse Cars</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="my-bookings.php" class="btn btn-glass w-100"><i class="fas fa-list me-2"></i>My Bookings</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="profile.php" class="btn btn-glass w-100"><i class="fas fa-user-edit me-2"></i>Edit Profile</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="testimonials.php" class="btn btn-glass w-100"><i class="fas fa-star me-2"></i>Write Review</a>
            </div>
        </div>
        
        <!-- Recent Bookings -->
        <div class="glass-card fade-in-up">
            <h5 class="mb-3"><i class="fas fa-history me-2" style="color:var(--primary-light)"></i>Recent Bookings</h5>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Car</th>
                            <th>Dates</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="recentBookingsTable">
                        <tr><td colspan="5" class="text-center py-4"><div class="spinner-custom mx-auto"></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', loadDashboard);

async function loadDashboard() {
    try {
        const data = await apiRequest('bookings/index.php');
        const bookings = data.bookings || [];
        
        // Stats
        document.getElementById('totalBookings').textContent = data.pagination.total_items;
        document.getElementById('activeBookings').textContent = bookings.filter(b => b.status === 'active' || b.status === 'confirmed').length;
        document.getElementById('pendingBookings').textContent = bookings.filter(b => b.status === 'pending').length;
        
        const spent = bookings.filter(b => ['confirmed','active','completed'].includes(b.status))
            .reduce((sum, b) => sum + parseFloat(b.total_amount), 0);
        document.getElementById('totalSpent').textContent = formatCurrency(spent);
        
        // Recent bookings table
        const tbody = document.getElementById('recentBookingsTable');
        if (bookings.length > 0) {
            tbody.innerHTML = bookings.slice(0, 5).map(b => `
                <tr>
                    <td><strong>${b.brand_name} ${b.model}</strong></td>
                    <td>${formatDate(b.pickup_date)} - ${formatDate(b.return_date)}</td>
                    <td>${formatCurrency(b.total_amount)}</td>
                    <td>${statusBadge(b.status)}</td>
                    <td>
                        ${['pending','confirmed'].includes(b.status) ? 
                            `<button class="btn btn-glass btn-sm" onclick="cancelBooking(${b.id})"><i class="fas fa-times"></i></button>` : '-'}
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-secondary py-4">No bookings yet. <a href="cars.php">Browse cars</a> to get started!</td></tr>';
        }
    } catch(e) {
        Toast.error('Failed to load dashboard data.');
    }
}

async function cancelBooking(id) {
    if (!confirmDelete('Cancel this booking?')) return;
    try {
        await apiRequest(`bookings/index.php?id=${id}`, { method: 'DELETE' });
        Toast.success('Booking cancelled.');
        loadDashboard();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
