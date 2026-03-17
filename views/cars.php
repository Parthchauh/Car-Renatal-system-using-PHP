<?php
/**
 * Cars Browsing Page - Search, Filter, Paginate
 */
$pageTitle = 'Browse Cars';
require_once 'partials/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">Our <span class="gradient-text">Fleet</span></h1>
        <nav aria-label="breadcrumb" class="fade-in-up stagger-1">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Cars</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Search & Filter Bar -->
        <div class="search-bar fade-in-up">
            <form id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label-custom">Search</label>
                        <div class="input-group input-group-custom">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control form-control-custom" placeholder="Search by model, brand..." id="searchInput">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label-custom">Brand</label>
                        <select name="brand" class="form-select form-select-custom" id="brandFilter">
                            <option value="">All Brands</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label-custom">Category</label>
                        <select name="category" class="form-select form-select-custom" id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="economy">Economy</option>
                            <option value="compact">Compact</option>
                            <option value="midsize">Midsize</option>
                            <option value="fullsize">Fullsize</option>
                            <option value="suv">SUV</option>
                            <option value="luxury">Luxury</option>
                            <option value="sports">Sports</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label-custom">Fuel Type</label>
                        <select name="fuel" class="form-select form-select-custom" id="fuelFilter">
                            <option value="">All Types</option>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label-custom">Transmission</label>
                        <select name="transmission" class="form-select form-select-custom" id="transFilter">
                            <option value="">All</option>
                            <option value="automatic">Automatic</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-4">
                        <button type="button" class="btn btn-glass w-100" onclick="resetFilters()">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Price Range & Sort -->
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label-custom">Min Price/day</label>
                        <input type="number" name="min_price" class="form-control form-control-custom" placeholder="$0" id="minPrice" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Max Price/day</label>
                        <input type="number" name="max_price" class="form-control form-control-custom" placeholder="$999" id="maxPrice" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Sort By</label>
                        <select class="form-select form-select-custom" id="sortBy">
                            <option value="created_at">Newest</option>
                            <option value="price_per_day">Price</option>
                            <option value="model">Name</option>
                            <option value="year">Year</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Order</label>
                        <select class="form-select form-select-custom" id="sortDir">
                            <option value="ASC">Low to High</option>
                            <option value="DESC">High to Low</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-secondary mb-0" id="resultCount">Loading...</p>
        </div>
        
        <!-- Cars Grid -->
        <div class="row g-4" id="carsGrid">
            <div class="col-12 text-center py-5">
                <div class="spinner-custom mx-auto"></div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4" id="carsPagination"></div>
    </div>
</section>

<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', () => {
    loadBrands();
    loadCars();
    
    // Live search with debounce
    const debouncedSearch = debounce(() => { currentPage = 1; loadCars(); }, 400);
    document.getElementById('searchInput').addEventListener('input', debouncedSearch);
    
    // Filter change events
    ['brandFilter','categoryFilter','fuelFilter','transFilter','sortBy','sortDir','minPrice','maxPrice'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => { currentPage = 1; loadCars(); });
    });
});

async function loadBrands() {
    try {
        const data = await apiRequest('brands/index.php?status=active');
        const select = document.getElementById('brandFilter');
        data.brands?.forEach(b => {
            select.innerHTML += `<option value="${b.id}">${b.name} (${b.car_count})</option>`;
        });
    } catch(e) {}
}

async function loadCars() {
    const params = new URLSearchParams({
        page: currentPage,
        search: document.getElementById('searchInput').value,
        brand: document.getElementById('brandFilter').value,
        category: document.getElementById('categoryFilter').value,
        fuel: document.getElementById('fuelFilter').value,
        transmission: document.getElementById('transFilter').value,
        min_price: document.getElementById('minPrice').value || 0,
        max_price: document.getElementById('maxPrice').value || 999999,
        sort: document.getElementById('sortBy').value,
        dir: document.getElementById('sortDir').value,
        status: 'available'
    });
    
    try {
        const data = await apiRequest(`cars/index.php?${params}`);
        const container = document.getElementById('carsGrid');
        
        document.getElementById('resultCount').textContent = 
            `Showing ${data.cars.length} of ${data.pagination.total_items} vehicles`;
        
        if (data.cars.length > 0) {
            container.innerHTML = data.cars.map(car => `
                <div class="col-lg-4 col-md-6 fade-in-up">
                    <div class="car-card">
                        <div class="car-image-wrapper">
                            <img src="${getCarImage(car.image)}" alt="${car.model}" loading="lazy">
                            <span class="car-badge ${car.status}">${car.status}</span>
                        </div>
                        <div class="car-body">
                            <h5 class="car-title">${car.model}</h5>
                            <p class="car-brand"><i class="fas fa-tag me-1"></i>${car.brand_name} &middot; ${car.year}</p>
                            <div class="car-features">
                                <span class="car-feature"><i class="fas fa-gas-pump"></i> ${car.fuel_type}</span>
                                <span class="car-feature"><i class="fas fa-cog"></i> ${car.transmission}</span>
                                <span class="car-feature"><i class="fas fa-users"></i> ${car.seats}</span>
                                ${car.mileage ? `<span class="car-feature"><i class="fas fa-tachometer-alt"></i> ${car.mileage}</span>` : ''}
                            </div>
                            ${car.description ? `<p class="text-secondary small mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${car.description}</p>` : ''}
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
            container.innerHTML = `<div class="col-12 text-center py-5">
                <i class="fas fa-car" style="font-size:3rem;color:var(--text-muted);margin-bottom:1rem;"></i>
                <p class="text-secondary">No cars found matching your criteria. Try adjusting your filters.</p>
            </div>`;
        }
        
        // Render pagination
        const pagHtml = renderPagination(data.pagination);
        document.getElementById('carsPagination').innerHTML = pagHtml;
        
        // Bind pagination clicks
        document.querySelectorAll('#carsPagination .page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.target.closest('.page-link').dataset.page);
                if (page > 0 && page <= data.pagination.total_pages) {
                    currentPage = page;
                    loadCars();
                    window.scrollTo({ top: 200, behavior: 'smooth' });
                }
            });
        });
    } catch (e) {
        console.error('loadCars error:', e);
        document.getElementById('carsGrid').innerHTML = `<div class="col-12 text-center text-danger py-5">
            <i class="fas fa-exclamation-triangle mb-2" style="font-size:2rem;"></i>
            <p>Failed to load cars: ${e.message}</p>
        </div>`;
    }
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('brandFilter').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('fuelFilter').value = '';
    document.getElementById('transFilter').value = '';
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    document.getElementById('sortBy').value = 'created_at';
    document.getElementById('sortDir').value = 'DESC';
    currentPage = 1;
    loadCars();
}
</script>

<?php require_once 'partials/footer.php'; ?>
