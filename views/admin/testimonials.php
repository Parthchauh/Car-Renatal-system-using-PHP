<?php $pageTitle = 'Reviews Management'; require_once 'partials/sidebar.php'; ?>

<div class="d-flex gap-2 mb-4 flex-wrap">
    <button class="btn btn-glass active" onclick="filterT(this,'')">All</button>
    <button class="btn btn-glass" onclick="filterT(this,'pending')">Pending</button>
    <button class="btn btn-glass" onclick="filterT(this,'approved')">Approved</button>
    <button class="btn btn-glass" onclick="filterT(this,'rejected')">Rejected</button>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>User</th><th>Rating</th><th>Review</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="reviewsTable"><tr><td colspan="6" class="text-center py-4"><div class="spinner-custom mx-auto"></div></td></tr></tbody>
        </table>
    </div>
    <div id="tPag" class="mt-3"></div>
</div>

<script>
let tPage = 1, tStatus = '';
document.addEventListener('DOMContentLoaded', loadReviews);

function filterT(btn, s) {
    document.querySelectorAll('.btn-glass').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    tStatus = s; tPage = 1; loadReviews();
}

async function loadReviews() {
    try {
        const data = await apiRequest(`testimonials/index.php?all=1&page=${tPage}${tStatus ? '&status='+tStatus : ''}`);
        document.getElementById('reviewsTable').innerHTML = data.testimonials.map(t => `
            <tr>
                <td>${t.user_name}</td>
                <td>${starRating(t.rating)}</td>
                <td style="max-width:300px;">${t.review.substring(0, 100)}${t.review.length > 100 ? '...' : ''}</td>
                <td>${statusBadge(t.status)}</td>
                <td>${formatDate(t.created_at)}</td>
                <td>
                    ${t.status !== 'approved' ? `<button class="btn btn-glass btn-sm me-1" onclick="moderateReview(${t.id},'approved')" title="Approve"><i class="fas fa-check text-success"></i></button>` : ''}
                    ${t.status !== 'rejected' ? `<button class="btn btn-glass btn-sm me-1" onclick="moderateReview(${t.id},'rejected')" title="Reject"><i class="fas fa-ban text-warning"></i></button>` : ''}
                    <button class="btn btn-glass btn-sm" onclick="deleteReview(${t.id})" title="Delete"><i class="fas fa-trash text-danger"></i></button>
                </td>
            </tr>
        `).join('');
        
        document.getElementById('tPag').innerHTML = renderPagination(data.pagination);
        document.querySelectorAll('#tPag .page-link').forEach(l => l.addEventListener('click', e => {
            e.preventDefault(); tPage = parseInt(e.target.closest('.page-link').dataset.page); loadReviews();
        }));
    } catch(e) { Toast.error('Failed to load reviews.'); }
}

async function moderateReview(id, status) {
    try {
        await apiRequest('testimonials/index.php', { method: 'PUT', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `id=${id}&status=${status}` });
        Toast.success(`Review ${status}.`); loadReviews();
    } catch(e) { Toast.error(e.message); }
}

async function deleteReview(id) {
    if (!confirmDelete()) return;
    try {
        await apiRequest(`testimonials/index.php?id=${id}`, { method: 'DELETE' });
        Toast.success('Review deleted.'); loadReviews();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
