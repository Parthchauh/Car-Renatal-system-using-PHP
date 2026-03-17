<?php $pageTitle = 'Brand Management'; require_once 'partials/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-secondary mb-0">Manage vehicle brands</p>
    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal" onclick="resetBrandForm()"><i class="fas fa-plus me-2"></i>Add Brand</button>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>Name</th><th>Cars</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody id="brandsTable"><tr><td colspan="5" class="text-center py-4"><div class="spinner-custom mx-auto"></div></td></tr></tbody>
        </table>
    </div>
</div>

<!-- Brand Modal -->
<div class="modal fade" id="brandModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="brandModalTitle">Add Brand</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <form id="brandForm">
            <input type="hidden" name="id" id="brandId">
            <div class="mb-3">
                <label class="form-label-custom">Brand Name *</label>
                <input type="text" name="name" class="form-control form-control-custom" id="brandName" required>
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Description</label>
                <textarea name="description" class="form-control form-control-custom" rows="3" id="brandDesc"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Status</label>
                <select name="status" class="form-select form-select-custom" id="brandStatus">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary-custom" onclick="saveBrand()"><i class="fas fa-save me-1"></i>Save</button>
    </div>
</div></div></div>

<script>
document.addEventListener('DOMContentLoaded', loadBrands);

async function loadBrands() {
    try {
        const data = await apiRequest('brands/index.php');
        document.getElementById('brandsTable').innerHTML = data.brands.map(b => `
            <tr>
                <td><strong>${b.name}</strong></td>
                <td>${b.car_count}</td>
                <td>${statusBadge(b.status)}</td>
                <td>${formatDate(b.created_at)}</td>
                <td>
                    <button class="btn btn-glass btn-sm me-1" onclick="editBrand(${JSON.stringify(b).replace(/"/g,'&quot;')})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-glass btn-sm" onclick="deleteBrand(${b.id})"><i class="fas fa-trash text-danger"></i></button>
                </td>
            </tr>
        `).join('');
    } catch(e) { Toast.error('Failed to load brands.'); }
}

function resetBrandForm() {
    document.getElementById('brandModalTitle').textContent = 'Add Brand';
    document.getElementById('brandId').value = '';
    document.getElementById('brandName').value = '';
    document.getElementById('brandDesc').value = '';
    document.getElementById('brandStatus').value = 'active';
}

function editBrand(b) {
    document.getElementById('brandModalTitle').textContent = 'Edit Brand';
    document.getElementById('brandId').value = b.id;
    document.getElementById('brandName').value = b.name;
    document.getElementById('brandDesc').value = b.description || '';
    document.getElementById('brandStatus').value = b.status;
    new bootstrap.Modal(document.getElementById('brandModal')).show();
}

async function saveBrand() {
    const id = document.getElementById('brandId').value;
    const formData = new FormData(document.getElementById('brandForm'));
    
    try {
        if (id) {
            await apiRequest('brands/index.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(formData).toString()
            });
        } else {
            await apiRequest('brands/index.php', { method: 'POST', body: formData });
        }
        Toast.success(id ? 'Brand updated!' : 'Brand created!');
        bootstrap.Modal.getInstance(document.getElementById('brandModal')).hide();
        loadBrands();
    } catch(e) { Toast.error(e.message); }
}

async function deleteBrand(id) {
    if (!confirmDelete('Delete this brand and all its cars?')) return;
    try {
        await apiRequest(`brands/index.php?id=${id}`, { method: 'DELETE' });
        Toast.success('Brand deleted.');
        loadBrands();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
