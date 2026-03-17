<?php $pageTitle = 'My Profile'; require_once 'partials/header.php'; requireLogin(false); ?>
<div class="page-header" style="min-height:auto;padding:7rem 0 2rem;">
    <div class="container position-relative" style="z-index:1"><h1 class="fade-in-up">My <span class="gradient-text">Profile</span></h1></div>
</div>
<section class="section" style="padding:2rem 0 5rem;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7 fade-in-up">
                <div class="glass-card">
                    <h5 class="mb-4"><i class="fas fa-user-edit me-2" style="color:var(--primary-light)"></i>Edit Profile</h5>
                    <form id="profileForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Full Name *</label>
                                <input type="text" name="full_name" class="form-control form-control-custom" id="profName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Email (read-only)</label>
                                <input type="email" class="form-control form-control-custom" id="profEmail" readonly style="opacity:0.6">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Phone</label>
                                <input type="tel" name="phone" class="form-control form-control-custom" id="profPhone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control form-control-custom" accept="image/*" onchange="previewImage(this, '#profImgPreview')">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Address</label>
                                <textarea name="address" class="form-control form-control-custom" rows="3" id="profAddress"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom"><i class="fas fa-save me-2"></i>Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-5 fade-in-up stagger-1">
                <!-- Profile Card -->
                <div class="glass-card text-center mb-4">
                    <img id="profImgPreview" src="" alt="Profile" style="width:90px;height:90px;border-radius:50%;object-fit:cover;margin-bottom:1rem;border:3px solid var(--primary);display:none;">
                    <div id="profAvatar" style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2.5rem;color:white;font-weight:700;"></div>
                    <h5 id="profNameDisplay">-</h5>
                    <p class="text-secondary small" id="profRoleDisplay">-</p>
                </div>
                
                <!-- Change Password -->
                <div class="glass-card">
                    <h5 class="mb-4"><i class="fas fa-lock me-2" style="color:var(--warning)"></i>Change Password</h5>
                    <form id="passwordForm">
                        <div class="mb-3">
                            <label class="form-label-custom">Current Password</label>
                            <input type="password" name="current_password" class="form-control form-control-custom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">New Password</label>
                            <input type="password" name="new_password" class="form-control form-control-custom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control form-control-custom" required>
                        </div>
                        <button type="submit" class="btn btn-glass w-100"><i class="fas fa-key me-2"></i>Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const data = await apiRequest('users/index.php?action=profile');
        const u = data.user;
        document.getElementById('profName').value = u.full_name;
        document.getElementById('profEmail').value = u.email;
        document.getElementById('profPhone').value = u.phone || '';
        document.getElementById('profAddress').value = u.address || '';
        document.getElementById('profNameDisplay').textContent = u.full_name;
        document.getElementById('profRoleDisplay').textContent = u.role + ' · Member since ' + formatDate(u.created_at);
        document.getElementById('profAvatar').textContent = u.full_name.charAt(0).toUpperCase();
    } catch(e) { Toast.error('Failed to load profile.'); }
});

document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitForm(e.target, 'users/index.php', {});
});

document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitForm(e.target, 'auth/change_password.php', { resetForm: true });
});
</script>
<?php require_once 'partials/footer.php'; ?>
