<?php
/**
 * Homepage - DriveElite Car Rental
 */
$pageTitle = 'Home';
$pageDescription = 'DriveElite - Premium car rental service. Browse luxury, economy, and sport cars at the best rates.';
require_once 'partials/header.php';
requireLogin();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container position-relative" style="z-index:2">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-in-up">
                <span class="badge rounded-pill mb-3" style="background:var(--bg-glass);border:1px solid var(--border-color);color:var(--accent);padding:0.5rem 1rem;font-size:0.85rem;">
                    <i class="fas fa-star me-1"></i> #1 Rated Car Rental Service
                </span>
                <h1 class="hero-title">
                    Drive Your <br><span class="gradient-text">Dream Car</span> Today
                </h1>
                <p class="hero-subtitle">
                    Discover our premium fleet of vehicles. From sleek sports cars to spacious SUVs — find the perfect ride for every occasion at unbeatable prices.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="cars.php" class="btn btn-primary-custom">
                        <i class="fas fa-car me-2"></i> Browse Fleet
                    </a>
                    <a href="about.php" class="btn btn-outline-custom">
                        <i class="fas fa-play-circle me-2"></i> Learn More
                    </a>
                </div>
                
                <div class="hero-stats fade-in-up stagger-2">
                    <div class="hero-stat">
                        <div class="hero-stat-value" id="counterCars">500+</div>
                        <div class="hero-stat-label">Vehicles</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">15k+</div>
                        <div class="hero-stat-label">Happy Clients</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">4.9</div>
                        <div class="hero-stat-label">Star Rating</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">24/7</div>
                        <div class="hero-stat-label">Support</div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 text-center fade-in-up stagger-3">
                <div style="position:relative;">
                    <div style="width:100%;height:400px;border-radius:var(--radius-xl);background:linear-gradient(135deg,rgba(108,92,231,0.12),rgba(0,210,255,0.08));display:flex;align-items:center;justify-content:center;border:1px solid var(--border-color);">
                        <i class="fas fa-car-side" style="font-size:12rem;background:linear-gradient(135deg,var(--primary-light),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Brands Marquee -->
<section class="section" style="padding:3rem 0;background:var(--bg-dark);">
    <div class="container">
        <div class="d-flex justify-content-center gap-5 flex-wrap align-items-center" id="brandLogos" style="opacity:0.5;">
            <!-- Loaded via JS -->
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title fade-in-up">Why Choose <span class="gradient-text">DriveElite</span></h2>
            <p class="section-subtitle mx-auto fade-in-up stagger-1">We provide an unmatched car rental experience with premium vehicles and outstanding service</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 fade-in-up stagger-1">
                <div class="feature-box">
                    <div class="feature-icon" style="background:linear-gradient(135deg,rgba(108,92,231,0.15),rgba(108,92,231,0.05));color:var(--primary-light);">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Fully Insured</h5>
                    <p>Comprehensive insurance coverage included with every rental for your peace of mind.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up stagger-2">
                <div class="feature-box">
                    <div class="feature-icon" style="background:linear-gradient(135deg,rgba(0,210,255,0.15),rgba(0,210,255,0.05));color:var(--accent);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h5>Best Prices</h5>
                    <p>Competitive daily rates with no hidden fees. Get the best value for your money.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up stagger-3">
                <div class="feature-box">
                    <div class="feature-icon" style="background:linear-gradient(135deg,rgba(0,184,148,0.15),rgba(0,184,148,0.05));color:var(--success-light);">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5>24/7 Support</h5>
                    <p>Round-the-clock customer support and roadside assistance wherever you go.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up stagger-4">
                <div class="feature-box">
                    <div class="feature-icon" style="background:linear-gradient(135deg,rgba(253,203,110,0.15),rgba(253,203,110,0.05));color:var(--warning);">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h5>Instant Booking</h5>
                    <p>Quick and easy online booking. Reserve your car in under 2 minutes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Cars -->
<section class="section" style="background:var(--bg-dark);">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title fade-in-up">Featured <span class="gradient-text">Vehicles</span></h2>
            <p class="section-subtitle mx-auto fade-in-up stagger-1">Explore our handpicked selection of premium cars</p>
        </div>
        
        <div class="row g-4" id="featuredCars">
            <!-- Cars loaded via AJAX -->
            <div class="col-12 text-center py-5">
                <div class="spinner-custom mx-auto"></div>
                <p class="text-secondary mt-3">Loading fleet...</p>
            </div>
        </div>
        
        <div class="text-center mt-4 fade-in-up">
            <a href="cars.php" class="btn btn-outline-custom">
                View All Cars <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title fade-in-up">How It <span class="gradient-text">Works</span></h2>
            <p class="section-subtitle mx-auto fade-in-up stagger-1">Rent your dream car in just 3 easy steps</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 fade-in-up stagger-1">
                <div class="glass-card text-center h-100">
                    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;font-size:1.5rem;font-weight:800;color:white;">1</div>
                    <h5>Choose Your Car</h5>
                    <p class="text-secondary small">Browse our extensive fleet, filter by category, brand, or price, and find your perfect ride.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up stagger-2">
                <div class="glass-card text-center h-100">
                    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-dark));display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;font-size:1.5rem;font-weight:800;color:#0A0A1A;">2</div>
                    <h5>Book & Confirm</h5>
                    <p class="text-secondary small">Select your dates, see the price instantly, and book in seconds. No hidden fees.</p>
                </div>
            </div>
            <div class="col-md-4 fade-in-up stagger-3">
                <div class="glass-card text-center h-100">
                    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--success),#00796B);display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;font-size:1.5rem;font-weight:800;color:white;">3</div>
                    <h5>Hit The Road</h5>
                    <p class="text-secondary small">Pick up your car and enjoy the ride. Return it when done — it's that simple!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="section" style="background:var(--bg-dark);">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title fade-in-up">What Our <span class="gradient-text">Clients Say</span></h2>
            <p class="section-subtitle mx-auto fade-in-up stagger-1">Real reviews from real customers</p>
        </div>
        
        <div class="row g-4" id="homeTestimonials">
            <div class="col-12 text-center py-4">
                <div class="spinner-custom mx-auto"></div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="glass-card text-center" style="padding:4rem 2rem;background:linear-gradient(135deg,rgba(108,92,231,0.1),rgba(0,210,255,0.05));border-color:rgba(108,92,231,0.2);">
            <h2 class="section-title mb-3">Ready to Hit The Road?</h2>
            <p class="section-subtitle mx-auto mb-4">Sign up today and get <span style="color:var(--accent);font-weight:700;">10% OFF</span> your first rental!</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="register.php" class="btn btn-primary-custom">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </a>
                <a href="cars.php" class="btn btn-accent-custom">
                    <i class="fas fa-car me-2"></i> Browse Cars
                </a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Load featured cars
    loadFeaturedCars();
    // Load testimonials
    loadTestimonials();
    // Load brands
    loadBrands();
});

async function loadFeaturedCars() {
    try {
        const data = await apiRequest('cars/index.php?status=available&page=1');
        const container = document.getElementById('featuredCars');
        
        if (data.cars && data.cars.length > 0) {
            container.innerHTML = data.cars.slice(0, 6).map(car => `
                <div class="col-lg-4 col-md-6 fade-in-up">
                    <div class="car-card">
                        <div class="car-image-wrapper">
                            <img src="${getCarImage(car.image)}" alt="${car.model}">
                            <span class="car-badge ${car.status}">${car.status}</span>
                        </div>
                        <div class="car-body">
                            <h5 class="car-title">${car.model}</h5>
                            <p class="car-brand"><i class="fas fa-tag me-1"></i>${car.brand_name}</p>
                            <div class="car-features">
                                <span class="car-feature"><i class="fas fa-gas-pump"></i> ${car.fuel_type}</span>
                                <span class="car-feature"><i class="fas fa-cog"></i> ${car.transmission}</span>
                                <span class="car-feature"><i class="fas fa-users"></i> ${car.seats} seats</span>
                                <span class="car-feature"><i class="fas fa-calendar"></i> ${car.year}</span>
                            </div>
                            <div class="car-footer">
                                <div class="car-price">${formatCurrency(car.price_per_day)} <span>/day</span></div>
                                <a href="booking.php?car_id=${car.id}" class="btn btn-primary-custom btn-sm">
                                    Book Now <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-secondary">No cars available right now. Check back soon!</p></div>';
        }
    } catch (e) {
        document.getElementById('featuredCars').innerHTML = '<div class="col-12 text-center"><p class="text-secondary">Failed to load cars.</p></div>';
    }
}

async function loadTestimonials() {
    try {
        const data = await apiRequest('testimonials/index.php');
        const container = document.getElementById('homeTestimonials');
        
        if (data.testimonials && data.testimonials.length > 0) {
            container.innerHTML = data.testimonials.slice(0, 3).map(t => `
                <div class="col-md-4 fade-in-up">
                    <div class="testimonial-card h-100">
                        <div class="quote-icon">"</div>
                        <div class="stars mb-2">${starRating(t.rating)}</div>
                        <p class="testimonial-text">${t.review}</p>
                        <div class="testimonial-author">
                            <div class="author-avatar">${t.user_name.charAt(0)}</div>
                            <div>
                                <div style="font-weight:600;font-size:0.95rem;">${t.user_name}</div>
                                <div style="font-size:0.8rem;color:var(--text-muted);">${formatDate(t.created_at)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-secondary">No reviews yet.</p></div>';
        }
    } catch (e) {
        document.getElementById('homeTestimonials').innerHTML = '<div class="col-12 text-center"><p class="text-secondary">Failed to load reviews.</p></div>';
    }
}

async function loadBrands() {
    try {
        const data = await apiRequest('brands/index.php?status=active');
        const container = document.getElementById('brandLogos');
        if (data.brands) {
            container.innerHTML = data.brands.map(b => `
                <span style="font-family:var(--font-primary);font-size:1.2rem;font-weight:700;color:var(--text-secondary);letter-spacing:1px;">${b.name}</span>
            `).join('');
        }
    } catch(e) {}
}
</script>

<?php require_once 'partials/footer.php'; ?>
