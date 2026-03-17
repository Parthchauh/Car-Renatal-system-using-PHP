<?php $pageTitle = 'User Management'; require_once 'partials/sidebar.php'; ?>

<div class="d-flex justify-content-between mb-4">
    <input type="text" class="form-control form-control-custom" style="max-width:300px;" placeholder="Search users..." id="userSearch" oninput="debounce(loadUsers, 400)()">
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Bookings</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody id="usersTable"><tr><td colspan="7" class="text-center py-4"><div class="spinner-custom mx-auto"></div></td></tr></tbody>
        </table>
    </div>
    <div id="uPag" class="mt-3"></div>
</div>

<script>
let uPage = 1;
document.addEventListener('DOMContentLoaded', loadUsers);

async function loadUsers() {
    const search = document.getElementById('userSearch')?.value || '';
    try {
        const data = await apiRequest(`users/index.php?page=${uPage}&search=${search}`);
        document.getElementById('usersTable').innerHTML = data.users.map(u => `
            <tr>
                <td><strong>${u.full_name}</strong></td>
                <td>${u.email}</td>
                <td><span class="badge-status badge-${u.role === 'admin' ? 'confirmed' : 'active'}">${u.role}</span></td>
                <td>${u.booking_count}</td>
                <td>${statusBadge(u.status)}</td>
                <td>${formatDate(u.created_at)}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-glass btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu" style="background:var(--bg-card);border-color:var(--border-color);">
                            <li><a class="dropdown-item text-light" href="#" onclick="changeRole(${u.id},'${u.role === 'admin' ? 'user' : 'admin'}')">Make ${u.role === 'admin' ? 'User' : 'Admin'}</a></li>
                            <li><a class="dropdown-item text-light" href="#" onclick="changeStatus(${u.id},'${u.status === 'active' ? 'banned' : 'active'}')">${u.status === 'active' ? 'Ban' : 'Activate'}</a></li>
                            <li><hr class="dropdown-divider" style="border-color:var(--border-color)"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteUser(${u.id})">Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `).join('');
        
        document.getElementById('uPag').innerHTML = renderPagination(data.pagination);
        document.querySelectorAll('#uPag .page-link').forEach(l => l.addEventListener('click', e => {
            e.preventDefault(); uPage = parseInt(e.target.closest('.page-link').dataset.page); loadUsers();
        }));
    } catch(e) { Toast.error('Failed to load users.'); }
}

async function changeRole(id, role) {
    try {
        await apiRequest('users/index.php', { method: 'PUT', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `id=${id}&role=${role}` });
        Toast.success('Role updated.'); loadUsers();
    } catch(e) { Toast.error(e.message); }
}

async function changeStatus(id, status) {
    try {
        await apiRequest('users/index.php', { method: 'PUT', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `id=${id}&status=${status}` });
        Toast.success('Status updated.'); loadUsers();
    } catch(e) { Toast.error(e.message); }
}

async function deleteUser(id) {
    if (!confirmDelete('Delete this user and all their data?')) return;
    try {
        await apiRequest(`users/index.php?id=${id}`, { method: 'DELETE' });
        Toast.success('User deleted.'); loadUsers();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
