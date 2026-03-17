<?php $pageTitle = 'Contact Inquiries'; require_once 'partials/sidebar.php'; ?>

<div class="d-flex gap-2 mb-4 flex-wrap">
    <button class="btn btn-glass active" onclick="filterQ(this,'')">All</button>
    <button class="btn btn-glass" onclick="filterQ(this,'new')">New</button>
    <button class="btn btn-glass" onclick="filterQ(this,'read')">Read</button>
    <button class="btn btn-glass" onclick="filterQ(this,'replied')">Replied</button>
    <button class="btn btn-glass" onclick="filterQ(this,'closed')">Closed</button>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="queriesTable"><tr><td colspan="6" class="text-center py-4"><div class="spinner-custom mx-auto"></div></td></tr></tbody>
        </table>
    </div>
    <div id="qPag" class="mt-3"></div>
</div>

<script>
let qPage = 1, qStatus = '';
document.addEventListener('DOMContentLoaded', loadQueries);

function filterQ(btn, s) {
    document.querySelectorAll('.btn-glass').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    qStatus = s; qPage = 1; loadQueries();
}

async function loadQueries() {
    try {
        const data = await apiRequest(`contact/index.php?page=${qPage}${qStatus ? '&status='+qStatus : ''}`);
        document.getElementById('queriesTable').innerHTML = data.queries.map(q => `
            <tr>
                <td><strong>${q.name}</strong><br><small class="text-secondary">${q.email}</small></td>
                <td>${q.subject}</td>
                <td style="max-width:250px;">${q.message.substring(0, 80)}...</td>
                <td>${statusBadge(q.status)}</td>
                <td>${formatDate(q.created_at)}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-glass btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu" style="background:var(--bg-card);border-color:var(--border-color);">
                            ${['new','read','replied','closed'].map(s => 
                                `<li><a class="dropdown-item text-light" href="#" onclick="updateQ(${q.id},'${s}')">Mark ${s}</a></li>`
                            ).join('')}
                            <li><hr class="dropdown-divider" style="border-color:var(--border-color)"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteQ(${q.id})">Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `).join('');
        
        document.getElementById('qPag').innerHTML = renderPagination(data.pagination);
        document.querySelectorAll('#qPag .page-link').forEach(l => l.addEventListener('click', e => {
            e.preventDefault(); qPage = parseInt(e.target.closest('.page-link').dataset.page); loadQueries();
        }));
    } catch(e) { Toast.error('Failed to load queries.'); }
}

async function updateQ(id, status) {
    try {
        await apiRequest('contact/index.php', { method: 'PUT', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `id=${id}&status=${status}` });
        Toast.success('Status updated.'); loadQueries();
    } catch(e) { Toast.error(e.message); }
}

async function deleteQ(id) {
    if (!confirmDelete()) return;
    try {
        await apiRequest(`contact/index.php?id=${id}`, { method: 'DELETE' });
        Toast.success('Query deleted.'); loadQueries();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
