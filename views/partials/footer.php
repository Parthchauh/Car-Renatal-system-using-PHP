<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h4 class="footer-title"><i class="fas fa-car-side me-2" style="color:var(--primary)"></i>DriveElite</h4>
                <p class="text-secondary mb-3" style="font-size:0.9rem;">Premium car rental service offering luxury, economy, and sport vehicles at competitive rates. Experience the freedom of the open road.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-secondary" style="font-size:1.2rem"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-secondary" style="font-size:1.2rem"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-secondary" style="font-size:1.2rem"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-secondary" style="font-size:1.2rem"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="<?= APP_URL ?>/views/index.php">Home</a></li>
                    <li><a href="<?= APP_URL ?>/views/cars.php">Browse Cars</a></li>
                    <li><a href="<?= APP_URL ?>/views/about.php">About Us</a></li>
                    <li><a href="<?= APP_URL ?>/views/contact.php">Contact</a></li>
                    <li><a href="<?= APP_URL ?>/views/faq.php">FAQ</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Contact Info</h5>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt me-2" style="color:var(--primary-light)"></i> 123 Auto Avenue, Car City</li>
                    <li><i class="fas fa-phone me-2" style="color:var(--primary-light)"></i> +1 (555) 123-4567</li>
                    <li><i class="fas fa-envelope me-2" style="color:var(--primary-light)"></i> info@driveelite.com</li>
                    <li><i class="fas fa-clock me-2" style="color:var(--primary-light)"></i> Mon - Sun: 8AM - 10PM</li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Newsletter</h5>
                <p class="text-secondary mb-3" style="font-size:0.88rem;">Subscribe for exclusive deals and updates.</p>
                <form id="subscribeForm" class="d-flex gap-2">
                    <input type="email" name="email" class="form-control form-control-custom" placeholder="Your email" required style="font-size:0.88rem;">
                    <button type="submit" class="btn btn-primary-custom btn-sm" style="white-space:nowrap;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> DriveElite. All rights reserved. Crafted with <i class="fas fa-heart" style="color:var(--danger)"></i></p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Chart.js (for admin) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Custom JS -->
<script src="<?= APP_URL ?>/assets/js/main.js"></script>

<script>
// Logout handler
document.getElementById('logoutBtn')?.addEventListener('click', async (e) => {
    e.preventDefault();
    try {
        await apiRequest('auth/logout.php');
        Toast.success('Logged out successfully!');
        setTimeout(() => window.location.href = '<?= APP_URL ?>/views/index.php', 1000);
    } catch (err) {
        window.location.href = '<?= APP_URL ?>/views/index.php';
    }
});

// Newsletter subscribe
document.getElementById('subscribeForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitForm(e.target, 'subscribers/index.php', { resetForm: true });
});
</script>
</body>
</html>
