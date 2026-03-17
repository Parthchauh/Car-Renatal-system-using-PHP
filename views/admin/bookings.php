<?php $pageTitle = 'Booking Management'; require_once 'partials/sidebar.php'; ?>

<div class="d-flex gap-2 mb-4 flex-wrap">
    <button class="btn btn-glass active" onclick="filterB(this,'')">All</button>
    <button class="btn btn-glass" onclick="filterB(this,'pending')">Pending</button>
    <button class="btn btn-glass" onclick="filterB(this,'confirmed')">Confirmed</button>
    <button class="btn btn-glass" onclick="filterB(this,'active')">Active</button>
    <button class="btn btn-glass" onclick="filterB(this,'completed')">Completed</button>
    <button class="btn btn-glass" onclick="filterB(this,'cancelled')">Cancelled</button>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>ID</th><th>User</th><th>Car</th><th>Dates</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="bookingsTable"><tr><td colspan="7" class="text-center py-4"><div class="spinner-custom mx-auto"></div></td></tr></tbody>
        </table>
    </div>
    <div id="bPag" class="mt-3"></div>
</div>

<script>
let bkPage = 1, bkStatus = '';
document.addEventListener('DOMContentLoaded', loadBookings);

function filterB(btn, s) {
    document.querySelectorAll('.btn-glass').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    bkStatus = s; bkPage = 1; loadBookings();
}

async function loadBookings() {
    try {
        const data = await apiRequest(`bookings/index.php?page=${bkPage}${bkStatus ? '&status='+bkStatus : ''}`);
        document.getElementById('bookingsTable').innerHTML = data.bookings.map(b => `
            <tr>
                <td>#${b.id}</td>
                <td>${b.user_name}</td>
                <td>${b.brand_name} ${b.model}</td>
                <td>${formatDate(b.pickup_date)} → ${formatDate(b.return_date)}<br><small class="text-secondary">${b.total_days} days</small></td>
                <td>${formatCurrency(b.total_amount)}</td>
                <td>${statusBadge(b.status)}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-glass btn-sm dropdown-toggle" data-bs-toggle="dropdown">Update</button>
                        <ul class="dropdown-menu" style="background:var(--bg-card);border-color:var(--border-color);">
                            ${['pending','confirmed','active','completed','cancelled'].map(s => 
                                `<li><a class="dropdown-item text-light" href="#" onclick="updateStatus(${b.id},'${s}')">${s.charAt(0).toUpperCase()+s.slice(1)}</a></li>`
                            ).join('')}
                        </ul>
                    </div>
                </td>
            </tr>
        `).join('');
        
        document.getElementById('bPag').innerHTML = renderPagination(data.pagination);
        document.querySelectorAll('#bPag .page-link').forEach(l => l.addEventListener('click', e => {
            e.preventDefault(); bkPage = parseInt(e.target.closest('.page-link').dataset.page); loadBookings();
        }));
    } catch(e) { Toast.error('Failed to load bookings.'); }
}

async function updateStatus(id, status) {
    try {
        await apiRequest('bookings/index.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&status=${status}`
        });
        Toast.success(`Booking #${id} → ${status}`);
        loadBookings();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
