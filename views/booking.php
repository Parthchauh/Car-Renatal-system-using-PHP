<?php
/**
 * Car Booking Page
 * Select dates, see real-time price, confirm booking
 */
$pageTitle = 'Book a Car';
require_once 'partials/header.php';
$carId = intval($_GET['car_id'] ?? 0);
?>

<div class="page-header" style="min-height:auto;padding:7rem 0 2rem;">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">Book Your <span class="gradient-text">Ride</span></h1>
        <nav aria-label="breadcrumb" class="fade-in-up stagger-1">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="cars.php">Cars</a></li>
                <li class="breadcrumb-item active">Booking</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section" style="padding:2rem 0 5rem;">
    <div class="container">
        <div class="row g-4">
            <!-- Car Details -->
            <div class="col-lg-5 fade-in-up">
                <div class="glass-card" id="carDetails">
                    <div class="text-center py-5">
                        <div class="spinner-custom mx-auto"></div>
                        <p class="text-secondary mt-2">Loading car details...</p>
                    </div>
                </div>
            </div>
            
            <!-- Booking Form -->
            <div class="col-lg-7 fade-in-up stagger-1">
                <div class="glass-card">
                    <h4 class="mb-4"><i class="fas fa-calendar-plus me-2" style="color:var(--primary-light)"></i>Booking Details</h4>
                    
                    <?php if (!isLoggedIn()): ?>
                        <div class="text-center py-4" style="background:var(--bg-glass);border-radius:var(--radius-md);padding:2rem;">
                            <i class="fas fa-lock" style="font-size:2.5rem;color:var(--text-muted);margin-bottom:1rem;"></i>
                            <h5>Login Required</h5>
                            <p class="text-secondary">Please login to book a car.</p>
                            <a href="login.php" class="btn btn-primary-custom"><i class="fas fa-sign-in-alt me-2"></i>Login Now</a>
                        </div>
                    <?php else: ?>
                        <form id="bookingForm">
                            <input type="hidden" name="car_id" value="<?= $carId ?>" id="carIdInput">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Pickup Date *</label>
                                    <input type="text" name="pickup_date" class="form-control form-control-custom" id="pickupDate" placeholder="Select date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Return Date *</label>
                                    <input type="text" name="return_date" class="form-control form-control-custom" id="returnDate" placeholder="Select date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Pickup Location</label>
                                    <select name="pickup_location" class="form-select form-select-custom">
                                        <option>Main Office</option>
                                        <option>Airport Terminal</option>
                                        <option>Downtown Branch</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Return Location</label>
                                    <select name="return_location" class="form-select form-select-custom">
                                        <option>Main Office</option>
                                        <option>Airport Terminal</option>
                                        <option>Downtown Branch</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-custom">Special Requests</label>
                                    <textarea name="notes" class="form-control form-control-custom" rows="3" placeholder="Any special requirements..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Price Summary -->
                            <div class="mt-4 p-3" style="background:var(--bg-glass);border-radius:var(--radius-md);border:1px solid var(--border-color);" id="priceSummary">
                                <h6 class="mb-3"><i class="fas fa-receipt me-2"></i>Price Summary</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Daily Rate:</span>
                                    <span id="dailyRate">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Duration:</span>
                                    <span id="totalDays">0 days</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2" id="availabilityStatus" style="display:none !important;">
                                    <span class="text-secondary">Availability:</span>
                                    <span id="availText">-</span>
                                </div>
                                <hr style="border-color:var(--border-color)">
                                <div class="d-flex justify-content-between">
                                    <strong>Total Amount:</strong>
                                    <strong style="font-size:1.3rem;color:var(--accent);" id="totalAmount">$0.00</strong>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary-custom w-100 mt-4" id="bookBtn" disabled>
                                <i class="fas fa-check-circle me-2"></i> Confirm Booking
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let carData = null;

document.addEventListener('DOMContentLoaded', () => {
    const carId = <?= $carId ?: 0 ?>;
    if (carId > 0) {
        loadCarDetails(carId);
    } else {
        document.getElementById('carDetails').innerHTML = '<p class="text-center text-secondary py-4">No car selected. <a href="cars.php">Browse cars</a></p>';
    }
    
    // Init date pickers
    const today = new Date().toISOString().split('T')[0];
    flatpickr('#pickupDate', {
        minDate: 'today',
        dateFormat: 'Y-m-d',
        theme: 'dark',
        onChange: () => checkAvailability()
    });
    flatpickr('#returnDate', {
        minDate: 'today',
        dateFormat: 'Y-m-d',
        theme: 'dark',
        onChange: () => checkAvailability()
    });
});

async function loadCarDetails(id) {
    try {
        const data = await apiRequest(`cars/index.php?id=${id}`);
        carData = data.car;
        
        document.getElementById('carDetails').innerHTML = `
            <div style="height:220px;border-radius:var(--radius-md);overflow:hidden;margin:-1.8rem -1.8rem 1.5rem;background:var(--bg-glass);">
                <img src="${carData.image && !carData.image.includes('default') ? APP.baseUrl + '/' + carData.image : 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=500&h=280&fit=crop'}" alt="${carData.model}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <h4>${carData.brand_name} ${carData.model}</h4>
            <p class="text-secondary small">${carData.year} · ${carData.color || ''}</p>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge-status badge-available">${carData.fuel_type}</span>
                <span class="badge-status badge-confirmed">${carData.transmission}</span>
                <span class="badge-status badge-pending">${carData.seats} seats</span>
                <span class="badge-status badge-completed">${carData.category}</span>
            </div>
            ${carData.description ? `<p class="text-secondary small mb-3">${carData.description}</p>` : ''}
            ${carData.features ? `<p class="small"><strong>Features:</strong> <span class="text-secondary">${carData.features}</span></p>` : ''}
            <div class="mt-3 pt-3" style="border-top:1px solid var(--border-color)">
                <div class="car-price">${formatCurrency(carData.price_per_day)} <span>/day</span></div>
            </div>
        `;
        
        document.getElementById('dailyRate').textContent = formatCurrency(carData.price_per_day);
    } catch(e) {
        document.getElementById('carDetails').innerHTML = '<p class="text-center text-danger py-4">Car not found.</p>';
    }
}

async function checkAvailability() {
    const pickup = document.getElementById('pickupDate').value;
    const returnD = document.getElementById('returnDate').value;
    const carId = document.getElementById('carIdInput')?.value;
    
    if (!pickup || !returnD || !carId) return;
    
    try {
        const data = await apiRequest(`cars/availability.php?car_id=${carId}&pickup_date=${pickup}&return_date=${returnD}`);
        
        document.getElementById('totalDays').textContent = data.days + ' days';
        document.getElementById('totalAmount').textContent = formatCurrency(data.total_amount);
        document.getElementById('dailyRate').textContent = formatCurrency(data.daily_rate);
        
        const availEl = document.getElementById('availabilityStatus');
        const availText = document.getElementById('availText');
        availEl.style.display = 'flex !important';
        availEl.classList.remove('d-none');
        availEl.style.cssText = '';
        
        if (data.available) {
            availText.innerHTML = '<span style="color:var(--success-light)"><i class="fas fa-check-circle me-1"></i>Available</span>';
            document.getElementById('bookBtn').disabled = false;
        } else {
            availText.innerHTML = '<span style="color:var(--danger)"><i class="fas fa-times-circle me-1"></i>Not Available</span>';
            document.getElementById('bookBtn').disabled = true;
        }
    } catch(e) {
        Toast.error(e.message);
        document.getElementById('bookBtn').disabled = true;
    }
}

document.getElementById('bookingForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const resp = await submitForm(e.target, 'bookings/index.php', {});
    if (resp?.success) {
        setTimeout(() => window.location.href = 'my-bookings.php', 1500);
    }
});
</script>

<?php require_once 'partials/footer.php'; ?>
