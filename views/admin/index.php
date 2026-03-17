<?php
/**
 * Admin Dashboard - Analytics & Overview
 */
$pageTitle = 'Dashboard';
require_once 'partials/sidebar.php';
?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6 fade-in-up">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,rgba(108,92,231,0.15),rgba(108,92,231,0.05));color:var(--primary-light);"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-value" id="sRevenue">-</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fade-in-up stagger-1">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,rgba(0,210,255,0.15),rgba(0,210,255,0.05));color:var(--accent);"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-value" id="sBookings">-</div>
            <div class="stat-label">Total Bookings</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fade-in-up stagger-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,rgba(0,184,148,0.15),rgba(0,184,148,0.05));color:var(--success-light);"><i class="fas fa-car"></i></div>
            <div class="stat-value" id="sCars">-</div>
            <div class="stat-label">Available Cars</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fade-in-up stagger-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,rgba(253,203,110,0.15),rgba(253,203,110,0.05));color:var(--warning);"><i class="fas fa-users"></i></div>
            <div class="stat-value" id="sUsers">-</div>
            <div class="stat-label">Registered Users</div>
        </div>
    </div>
</div>

<!-- Quick Info Badges -->
<div class="d-flex gap-3 flex-wrap mb-4 fade-in-up">
    <div class="glass-card d-flex align-items-center gap-2" style="padding:0.6rem 1rem;">
        <span class="badge-status badge-pending" id="sPending">0 Pending</span>
    </div>
    <div class="glass-card d-flex align-items-center gap-2" style="padding:0.6rem 1rem;">
        <span class="badge-status badge-active" id="sActive">0 Active</span>
    </div>
    <div class="glass-card d-flex align-items-center gap-2" style="padding:0.6rem 1rem;">
        <span class="badge-status badge-new" id="sQueries">0 New Queries</span>
    </div>
    <div class="glass-card d-flex align-items-center gap-2" style="padding:0.6rem 1rem;">
        <span class="badge-status badge-pending" id="sReviews">0 Pending Reviews</span>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8 fade-in-up">
        <div class="glass-card">
            <h6 class="mb-3"><i class="fas fa-chart-line me-2" style="color:var(--primary-light)"></i>Revenue Overview (Last 6 Months)</h6>
            <canvas id="revenueChart" height="280"></canvas>
        </div>
    </div>
    <div class="col-lg-4 fade-in-up stagger-1">
        <div class="glass-card">
            <h6 class="mb-3"><i class="fas fa-chart-doughnut me-2" style="color:var(--accent)"></i>Bookings by Status</h6>
            <canvas id="statusChart" height="280"></canvas>
        </div>
    </div>
</div>

<!-- Top Cars & Recent Bookings -->
<div class="row g-4">
    <div class="col-lg-5 fade-in-up">
        <div class="glass-card">
            <h6 class="mb-3"><i class="fas fa-trophy me-2" style="color:var(--warning)"></i>Top Rented Cars</h6>
            <div id="topCarsTable"></div>
        </div>
    </div>
    <div class="col-lg-7 fade-in-up stagger-1">
        <div class="glass-card">
            <h6 class="mb-3"><i class="fas fa-history me-2" style="color:var(--success-light)"></i>Recent Bookings</h6>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead><tr><th>User</th><th>Car</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody id="recentTable"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadDashboard);

async function loadDashboard() {
    try {
        const data = await apiRequest('dashboard/index.php');
        const s = data.stats;
        
        // Stat cards
        document.getElementById('sRevenue').textContent = formatCurrency(s.total_revenue);
        document.getElementById('sBookings').textContent = s.total_bookings;
        document.getElementById('sCars').textContent = s.available_cars + '/' + s.total_cars;
        document.getElementById('sUsers').textContent = s.total_users;
        document.getElementById('sPending').textContent = s.pending_bookings + ' Pending';
        document.getElementById('sActive').textContent = s.active_bookings + ' Active';
        document.getElementById('sQueries').textContent = s.new_queries + ' New Queries';
        document.getElementById('sReviews').textContent = s.pending_reviews + ' Pending Reviews';
        
        // Revenue Chart
        const revCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: s.monthly_revenue.map(m => m.month),
                datasets: [{
                    label: 'Revenue ($)',
                    data: s.monthly_revenue.map(m => m.revenue),
                    borderColor: '#6C5CE7',
                    backgroundColor: 'rgba(108,92,231,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6C5CE7',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }, {
                    label: 'Bookings',
                    data: s.monthly_revenue.map(m => m.booking_count),
                    borderColor: '#00D2FF',
                    backgroundColor: 'rgba(0,210,255,0.05)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#00D2FF',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: '#aaa' } } },
                scales: {
                    x: { ticks: { color: '#666' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    y: { ticks: { color: '#666' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    y1: { position: 'right', ticks: { color: '#666' }, grid: { display: false } }
                }
            }
        });
        
        // Status Doughnut
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const colors = { pending: '#FDCB6E', confirmed: '#6C5CE7', active: '#00B894', completed: '#00D2FF', cancelled: '#FF6B6B' };
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: s.bookings_by_status.map(b => b.status),
                datasets: [{
                    data: s.bookings_by_status.map(b => b.count),
                    backgroundColor: s.bookings_by_status.map(b => colors[b.status] || '#666'),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: { legend: { position: 'bottom', labels: { color: '#aaa', padding: 15 } } }
            }
        });
        
        // Top Cars
        document.getElementById('topCarsTable').innerHTML = s.top_cars.map((c, i) => `
            <div class="d-flex align-items-center gap-3 mb-3 p-2" style="background:var(--bg-glass);border-radius:var(--radius-md);">
                <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;color:white;">${i+1}</div>
                <div class="flex-grow-1">
                    <div style="font-weight:600;font-size:0.9rem;">${c.brand_name} ${c.model}</div>
                    <div class="text-secondary" style="font-size:0.8rem;">${c.rental_count} rentals · ${formatCurrency(c.revenue)} revenue</div>
                </div>
            </div>
        `).join('');
        
        // Recent Bookings
        document.getElementById('recentTable').innerHTML = s.recent_bookings.map(b => `
            <tr>
                <td>${b.user_name}</td>
                <td>${b.brand_name} ${b.model}</td>
                <td>${formatCurrency(b.total_amount)}</td>
                <td>${statusBadge(b.status)}</td>
            </tr>
        `).join('');
        
    } catch(e) { Toast.error('Failed to load dashboard.'); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
