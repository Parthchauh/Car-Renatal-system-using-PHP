<?php $pageTitle = 'Login'; require_once 'partials/header.php'; if(isLoggedIn()) { header('Location: dashboard.php'); exit; } ?>
<div class="page-header" style="min-height:auto;padding:7rem 0 2rem;">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">Welcome <span class="gradient-text">Back</span></h1>
    </div>
</div>
<section class="section" style="padding:2rem 0 5rem;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="glass-card fade-in-up">
                    <div class="text-center mb-4">
                        <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;color:white;">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h4>Login to Your Account</h4>
                        <p class="text-secondary small">Enter your credentials to access your dashboard</p>
                    </div>
                    
                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label-custom">Email Address</label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="you@example.com" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Password</label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control form-control-custom" placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label text-secondary small" for="remember">Remember me</label>
                            </div>
                            <a href="reset-password.php" class="small">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                        <p class="text-center text-secondary small mb-0">
                            Don't have an account? <a href="register.php">Create one</a>
                        </p>
                    </form>
                    
                    <!-- Demo Credentials -->
                    <div class="mt-4 p-3" style="background:var(--bg-glass);border-radius:var(--radius-md);border:1px solid var(--border-color);">
                        <p class="small text-secondary mb-2"><i class="fas fa-info-circle me-1"></i> Demo Credentials:</p>
                        <p class="small mb-1"><strong>Admin:</strong> admin@carrental.com / password</p>
                        <p class="small mb-0"><strong>User:</strong> john@example.com / password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const response = await submitForm(e.target, 'auth/login.php', {
        redirect: 'dashboard.php'
    });
    if (response?.redirect) {
        setTimeout(() => window.location.href = response.redirect, 1000);
    }
});
</script>
<?php require_once 'partials/footer.php'; ?>
