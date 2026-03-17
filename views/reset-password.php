<?php $pageTitle = 'Reset Password'; require_once 'partials/header.php'; ?>
<div class="page-header" style="min-height:auto;padding:7rem 0 2rem;">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">Reset <span class="gradient-text">Password</span></h1>
    </div>
</div>
<section class="section" style="padding:2rem 0 5rem;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="glass-card fade-in-up">
                    <!-- Request Reset -->
                    <div id="requestForm">
                        <div class="text-center mb-4">
                            <i class="fas fa-key" style="font-size:2.5rem;color:var(--primary-light);margin-bottom:1rem;"></i>
                            <h4>Forgot Password?</h4>
                            <p class="text-secondary small">Enter your email and we'll send you a reset token.</p>
                        </div>
                        <form id="resetRequestForm">
                            <input type="hidden" name="action" value="request">
                            <div class="mb-3">
                                <label class="form-label-custom">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="you@example.com" required>
                            </div>
                            <button type="submit" class="btn btn-primary-custom w-100 mb-3"><i class="fas fa-paper-plane me-2"></i>Send Reset Token</button>
                            <p class="text-center small"><a href="login.php"><i class="fas fa-arrow-left me-1"></i>Back to Login</a></p>
                        </form>
                    </div>
                    
                    <!-- Reset with Token -->
                    <div id="resetForm" style="display:none;">
                        <div class="text-center mb-4">
                            <i class="fas fa-lock-open" style="font-size:2.5rem;color:var(--success-light);margin-bottom:1rem;"></i>
                            <h4>Set New Password</h4>
                        </div>
                        <form id="resetPasswordForm">
                            <input type="hidden" name="action" value="reset">
                            <div class="mb-3">
                                <label class="form-label-custom">Reset Token</label>
                                <input type="text" name="token" class="form-control form-control-custom" id="tokenInput" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">New Password</label>
                                <input type="password" name="password" class="form-control form-control-custom" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-custom">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control form-control-custom" required>
                            </div>
                            <button type="submit" class="btn btn-primary-custom w-100"><i class="fas fa-check me-2"></i>Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.getElementById('resetRequestForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const resp = await submitForm(e.target, 'auth/reset_password.php', {});
    if (resp?.success && resp.token) {
        document.getElementById('requestForm').style.display = 'none';
        document.getElementById('resetForm').style.display = 'block';
        document.getElementById('tokenInput').value = resp.token;
    }
});
document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitForm(e.target, 'auth/reset_password.php', { redirect: 'login.php' });
});
</script>
<?php require_once 'partials/footer.php'; ?>
