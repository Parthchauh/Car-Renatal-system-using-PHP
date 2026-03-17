<?php $pageTitle = 'About Us'; require_once 'partials/header.php'; ?>
<div class="page-header">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">About <span class="gradient-text">DriveElite</span></h1>
        <nav aria-label="breadcrumb" class="fade-in-up stagger-1">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">About</li>
            </ol>
        </nav>
    </div>
</div>
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 fade-in-up">
                <div class="glass-card" style="padding:3rem;background:linear-gradient(135deg,rgba(108,92,231,0.08),rgba(0,210,255,0.04));">
                    <i class="fas fa-car-side" style="font-size:8rem;background:linear-gradient(135deg,var(--primary-light),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;text-align:center;"></i>
                </div>
            </div>
            <div class="col-lg-6 fade-in-up stagger-1">
                <div id="aboutContent">
                    <div class="spinner-custom mx-auto"></div>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="row g-4 mt-5">
            <div class="col-lg-3 col-md-6 fade-in-up stagger-1">
                <div class="glass-card text-center">
                    <div style="font-size:2.5rem;font-weight:800;font-family:var(--font-primary);color:var(--primary-light);">500+</div>
                    <p class="text-secondary mb-0">Vehicles in Fleet</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up stagger-2">
                <div class="glass-card text-center">
                    <div style="font-size:2.5rem;font-weight:800;font-family:var(--font-primary);color:var(--accent);">15K+</div>
                    <p class="text-secondary mb-0">Happy Customers</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up stagger-3">
                <div class="glass-card text-center">
                    <div style="font-size:2.5rem;font-weight:800;font-family:var(--font-primary);color:var(--success-light);">50+</div>
                    <p class="text-secondary mb-0">Locations</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up stagger-4">
                <div class="glass-card text-center">
                    <div style="font-size:2.5rem;font-weight:800;font-family:var(--font-primary);color:var(--warning);">4.9</div>
                    <p class="text-secondary mb-0">Average Rating</p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const data = await apiRequest('content/index.php?key=about');
        document.getElementById('aboutContent').innerHTML = data.content.content;
    } catch(e) {
        document.getElementById('aboutContent').innerHTML = '<p class="text-secondary">Content loading failed.</p>';
    }
});
</script>
<?php require_once 'partials/footer.php'; ?>
