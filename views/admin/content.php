<?php $pageTitle = 'Content Management'; require_once 'partials/sidebar.php'; ?>

<p class="text-secondary mb-4">Edit site pages like About, FAQ, and Homepage content.</p>

<div class="glass-card" id="contentList">
    <div class="text-center py-4"><div class="spinner-custom mx-auto"></div></div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="contentModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="contentTitle">Edit Content</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <form id="contentForm">
            <input type="hidden" name="id" id="cId">
            <div class="mb-3">
                <label class="form-label-custom">Page Title</label>
                <input type="text" name="title" class="form-control form-control-custom" id="cTitle">
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Meta Description</label>
                <input type="text" name="meta_description" class="form-control form-control-custom" id="cMeta">
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Content (HTML)</label>
                <textarea name="content" class="form-control form-control-custom" rows="12" id="cContent" style="font-family:monospace;font-size:0.85rem;"></textarea>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary-custom" onclick="saveContent()"><i class="fas fa-save me-1"></i>Save</button>
    </div>
</div></div></div>

<script>
document.addEventListener('DOMContentLoaded', loadContent);

async function loadContent() {
    try {
        const data = await apiRequest('content/index.php');
        document.getElementById('contentList').innerHTML = data.contents.map(c => `
            <div class="d-flex justify-content-between align-items-center p-3 mb-2" style="background:var(--bg-glass);border-radius:var(--radius-md);">
                <div>
                    <strong>${c.title}</strong>
                    <span class="badge-status badge-${c.status === 'active' ? 'active' : 'maintenance'} ms-2">${c.status}</span>
                    <br><small class="text-secondary">Key: ${c.page_key} · Updated: ${formatDate(c.updated_at)}</small>
                </div>
                <button class="btn btn-glass btn-sm" onclick='editContent(${JSON.stringify(c)})'><i class="fas fa-edit me-1"></i>Edit</button>
            </div>
        `).join('');
    } catch(e) { Toast.error('Failed to load content.'); }
}

function editContent(c) {
    document.getElementById('contentTitle').textContent = 'Edit: ' + c.title;
    document.getElementById('cId').value = c.id;
    document.getElementById('cTitle').value = c.title;
    document.getElementById('cMeta').value = c.meta_description || '';
    document.getElementById('cContent').value = c.content;
    new bootstrap.Modal(document.getElementById('contentModal')).show();
}

async function saveContent() {
    const formData = new FormData(document.getElementById('contentForm'));
    try {
        await apiRequest('content/index.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(formData).toString()
        });
        Toast.success('Content updated!');
        bootstrap.Modal.getInstance(document.getElementById('contentModal')).hide();
        loadContent();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
