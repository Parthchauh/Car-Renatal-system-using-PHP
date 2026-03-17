<?php $pageTitle = 'Register'; require_once 'partials/header.php'; if(isLoggedIn()) { header('Location: dashboard.php'); exit; } ?>
<div class="page-header" style="min-height:auto;padding:7rem 0 2rem;">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">Create <span class="gradient-text">Account</span></h1>
    </div>
</div>
<section class="section" style="padding:2rem 0 5rem;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="glass-card fade-in-up">
                    <div class="text-center mb-4">
                        <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--primary));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;color:white;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h4>Join DriveElite</h4>
                        <p class="text-secondary small">Create your account and start your journey</p>
                    </div>
                    
                    <form id="registerForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Full Name *</label>
                                <input type="text" name="full_name" class="form-control form-control-custom" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Phone</label>
                                <input type="tel" name="phone" class="form-control form-control-custom" placeholder="+1 (555) 000-0000">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Email Address *</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="you@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Password *</label>
                                <input type="password" name="password" class="form-control form-control-custom" placeholder="Min 8 chars, A-Z, a-z, 0-9" required id="regPassword">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Confirm Password *</label>
                                <input type="password" name="confirm_password" class="form-control form-control-custom" placeholder="Repeat password" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label text-secondary small" for="terms">
                                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom w-100">
                                    <i class="fas fa-user-plus me-2"></i> Create Account
                                </button>
                            </div>
                        </div>
                    </form>
                    <p class="text-center text-secondary small mt-3 mb-0">
                        Already have an account? <a href="login.php">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitForm(e.target, 'auth/register.php', { redirect: 'dashboard.php' });
});
</script>
<?php require_once 'partials/footer.php'; ?>
