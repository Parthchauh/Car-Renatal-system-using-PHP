<?php $pageTitle = 'Subscribers'; require_once 'partials/sidebar.php'; ?>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>Email</th><th>Subscribed On</th><th>Action</th></tr></thead>
            <tbody id="subsTable"><tr><td colspan="3" class="text-center py-4"><div class="spinner-custom mx-auto"></div></td></tr></tbody>
        </table>
    </div>
    <div id="sPag" class="mt-3"></div>
</div>

<script>
let sPage = 1;
document.addEventListener('DOMContentLoaded', loadSubs);

async function loadSubs() {
    try {
        const data = await apiRequest(`subscribers/index.php?page=${sPage}`);
        document.getElementById('subsTable').innerHTML = data.subscribers.map(s => `
            <tr>
                <td><i class="fas fa-envelope me-2" style="color:var(--primary-light)"></i>${s.email}</td>
                <td>${formatDate(s.created_at)}</td>
                <td><button class="btn btn-glass btn-sm" onclick="removeSub(${s.id})"><i class="fas fa-trash text-danger"></i> Remove</button></td>
            </tr>
        `).join('');
        
        document.getElementById('sPag').innerHTML = renderPagination(data.pagination);
        document.querySelectorAll('#sPag .page-link').forEach(l => l.addEventListener('click', e => {
            e.preventDefault(); sPage = parseInt(e.target.closest('.page-link').dataset.page); loadSubs();
        }));
    } catch(e) { Toast.error('Failed to load subscribers.'); }
}

async function removeSub(id) {
    if (!confirmDelete('Remove this subscriber?')) return;
    try {
        await apiRequest(`subscribers/index.php?id=${id}`, { method: 'DELETE' });
        Toast.success('Subscriber removed.'); loadSubs();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
