<?php $pageTitle = 'My Bookings'; require_once 'partials/header.php'; requireLogin(false); ?>
<div class="page-header" style="min-height:auto;padding:7rem 0 2rem;">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">My <span class="gradient-text">Bookings</span></h1>
    </div>
</div>
<section class="section" style="padding:2rem 0 5rem;">
    <div class="container">
        <!-- Status Filter -->
        <div class="d-flex gap-2 mb-4 flex-wrap fade-in-up">
            <button class="btn btn-glass active" data-status="" onclick="filterBookings(this,'')">All</button>
            <button class="btn btn-glass" data-status="pending" onclick="filterBookings(this,'pending')">Pending</button>
            <button class="btn btn-glass" data-status="confirmed" onclick="filterBookings(this,'confirmed')">Confirmed</button>
            <button class="btn btn-glass" data-status="active" onclick="filterBookings(this,'active')">Active</button>
            <button class="btn btn-glass" data-status="completed" onclick="filterBookings(this,'completed')">Completed</button>
            <button class="btn btn-glass" data-status="cancelled" onclick="filterBookings(this,'cancelled')">Cancelled</button>
        </div>
        
        <div id="bookingsList">
            <div class="text-center py-5"><div class="spinner-custom mx-auto"></div></div>
        </div>
        <div id="bookingsPagination" class="mt-4"></div>
    </div>
</section>
<script>
let bPage = 1, bStatus = '';

document.addEventListener('DOMContentLoaded', () => loadBookings());

function filterBookings(btn, status) {
    document.querySelectorAll('[data-status]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    bStatus = status;
    bPage = 1;
    loadBookings();
}

async function loadBookings() {
    try {
        const params = `page=${bPage}${bStatus ? '&status=' + bStatus : ''}`;
        const data = await apiRequest(`bookings/index.php?${params}`);
        const container = document.getElementById('bookingsList');
        
        if (data.bookings.length > 0) {
            container.innerHTML = data.bookings.map(b => `
                <div class="glass-card mb-3 fade-in-up">
                    <div class="row align-items-center g-3">
                        <div class="col-md-2">
                            <img src="${b.image && !b.image.includes('default') ? APP.baseUrl + '/' + b.image : 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=200&h=130&fit=crop'}" alt="${b.model}" style="width:100%;height:100px;object-fit:cover;border-radius:var(--radius-md);">
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1">${b.brand_name} ${b.model}</h6>
                            <p class="text-secondary small mb-1"><i class="fas fa-calendar me-1"></i>${formatDate(b.pickup_date)} → ${formatDate(b.return_date)}</p>
                            <p class="text-secondary small mb-0"><i class="fas fa-clock me-1"></i>${b.total_days} days</p>
                        </div>
                        <div class="col-md-2 text-center">
                            ${statusBadge(b.status)}
                        </div>
                        <div class="col-md-2 text-center">
                            <div style="font-family:var(--font-primary);font-size:1.2rem;font-weight:700;color:var(--accent);">${formatCurrency(b.total_amount)}</div>
                        </div>
                        <div class="col-md-2 text-end">
                            ${['pending','confirmed'].includes(b.status) ? 
                                `<button class="btn btn-glass btn-sm" onclick="cancelBooking(${b.id})" title="Cancel"><i class="fas fa-times text-danger"></i> Cancel</button>` : ''}
                            ${b.status === 'completed' ? 
                                `<a href="testimonials.php?booking_id=${b.id}" class="btn btn-glass btn-sm"><i class="fas fa-star text-warning"></i> Review</a>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = `<div class="glass-card text-center py-5">
                <i class="fas fa-calendar-times" style="font-size:3rem;color:var(--text-muted);margin-bottom:1rem;"></i>
                <p class="text-secondary">No bookings found. <a href="cars.php">Browse cars</a> to make your first booking!</p>
            </div>`;
        }
        
        document.getElementById('bookingsPagination').innerHTML = renderPagination(data.pagination);
        document.querySelectorAll('#bookingsPagination .page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                bPage = parseInt(e.target.closest('.page-link').dataset.page);
                loadBookings();
            });
        });
    } catch(e) { Toast.error('Failed to load bookings.'); }
}

async function cancelBooking(id) {
    if (!confirmDelete('Cancel this booking?')) return;
    try {
        await apiRequest(`bookings/index.php?id=${id}`, { method: 'DELETE' });
        Toast.success('Booking cancelled.');
        loadBookings();
    } catch(e) { Toast.error(e.message); }
}
</script>
<?php require_once 'partials/footer.php'; ?>
