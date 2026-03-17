<?php $pageTitle = 'Contact Us'; require_once 'partials/header.php'; ?>
<div class="page-header">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">Contact <span class="gradient-text">Us</span></h1>
        <nav aria-label="breadcrumb" class="fade-in-up stagger-1">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
    </div>
</div>
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5 fade-in-up">
                <h3 class="mb-4">Get In Touch</h3>
                <p class="text-secondary mb-4">Have a question or need help? Fill out the form and our team will respond within 24 hours.</p>
                
                <div class="d-flex gap-3 mb-3">
                    <div style="width:45px;height:45px;border-radius:var(--radius-md);background:linear-gradient(135deg,rgba(108,92,231,0.15),rgba(108,92,231,0.05));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-map-marker-alt" style="color:var(--primary-light)"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Address</h6>
                        <small class="text-secondary">123 Auto Avenue, Car City, CC 10001</small>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div style="width:45px;height:45px;border-radius:var(--radius-md);background:linear-gradient(135deg,rgba(0,210,255,0.15),rgba(0,210,255,0.05));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-phone" style="color:var(--accent)"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Phone</h6>
                        <small class="text-secondary">+1 (555) 123-4567</small>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div style="width:45px;height:45px;border-radius:var(--radius-md);background:linear-gradient(135deg,rgba(0,184,148,0.15),rgba(0,184,148,0.05));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-envelope" style="color:var(--success-light)"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Email</h6>
                        <small class="text-secondary">info@driveelite.com</small>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div style="width:45px;height:45px;border-radius:var(--radius-md);background:linear-gradient(135deg,rgba(253,203,110,0.15),rgba(253,203,110,0.05));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-clock" style="color:var(--warning)"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Hours</h6>
                        <small class="text-secondary">Mon - Sun: 8:00 AM - 10:00 PM</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7 fade-in-up stagger-1">
                <div class="glass-card">
                    <h4 class="mb-4"><i class="fas fa-paper-plane me-2" style="color:var(--primary-light)"></i>Send Us a Message</h4>
                    <form id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Full Name *</label>
                                <input type="text" name="name" class="form-control form-control-custom" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Email Address *</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="john@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Phone</label>
                                <input type="tel" name="phone" class="form-control form-control-custom" placeholder="+1 (555) 000-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Subject *</label>
                                <input type="text" name="subject" class="form-control form-control-custom" placeholder="How can we help?" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Message *</label>
                                <textarea name="message" class="form-control form-control-custom" rows="5" placeholder="Tell us more about your inquiry..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom w-100">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.getElementById('contactForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitForm(e.target, 'contact/index.php', { resetForm: true });
});
</script>
<?php require_once 'partials/footer.php'; ?>
