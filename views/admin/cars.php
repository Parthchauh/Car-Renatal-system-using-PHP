<?php $pageTitle = 'Vehicle Management'; require_once 'partials/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-secondary mb-0">Manage your fleet</p>
    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#carModal" onclick="resetCarForm()"><i class="fas fa-plus me-2"></i>Add Vehicle</button>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>Img</th><th>Vehicle</th><th>Brand</th><th>Category</th><th>Price/Day</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="carsTable"><tr><td colspan="7" class="text-center py-4"><div class="spinner-custom mx-auto"></div></td></tr></tbody>
        </table>
    </div>
    <div id="carsPag" class="mt-3"></div>
</div>

<!-- Car Modal -->
<div class="modal fade" id="carModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="carModalTitle">Add Vehicle</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <form id="carForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="carId">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label-custom">Brand *</label><select name="brand_id" class="form-select form-select-custom" id="carBrand" required></select></div>
                <div class="col-md-6"><label class="form-label-custom">Model *</label><input type="text" name="model" class="form-control form-control-custom" id="carModel" required></div>
                <div class="col-md-4"><label class="form-label-custom">Year *</label><input type="number" name="year" class="form-control form-control-custom" id="carYear" min="2000" max="2030" required></div>
                <div class="col-md-4"><label class="form-label-custom">Color</label><input type="text" name="color" class="form-control form-control-custom" id="carColor"></div>
                <div class="col-md-4"><label class="form-label-custom">Price/Day ($) *</label><input type="number" name="price_per_day" class="form-control form-control-custom" id="carPrice" step="0.01" required></div>
                <div class="col-md-4"><label class="form-label-custom">Fuel Type</label><select name="fuel_type" class="form-select form-select-custom" id="carFuel"><option value="petrol">Petrol</option><option value="diesel">Diesel</option><option value="electric">Electric</option><option value="hybrid">Hybrid</option></select></div>
                <div class="col-md-4"><label class="form-label-custom">Transmission</label><select name="transmission" class="form-select form-select-custom" id="carTrans"><option value="automatic">Automatic</option><option value="manual">Manual</option></select></div>
                <div class="col-md-4"><label class="form-label-custom">Seats</label><input type="number" name="seats" class="form-control form-control-custom" id="carSeats" value="5" min="2" max="12"></div>
                <div class="col-md-4"><label class="form-label-custom">Category</label><select name="category" class="form-select form-select-custom" id="carCat"><option value="economy">Economy</option><option value="compact">Compact</option><option value="midsize">Midsize</option><option value="fullsize">Fullsize</option><option value="suv">SUV</option><option value="luxury">Luxury</option><option value="sports">Sports</option></select></div>
                <div class="col-md-4"><label class="form-label-custom">Mileage</label><input type="text" name="mileage" class="form-control form-control-custom" id="carMileage" placeholder="e.g. 30 MPG"></div>
                <div class="col-md-4"><label class="form-label-custom">Status</label><select name="status" class="form-select form-select-custom" id="carStatus"><option value="available">Available</option><option value="rented">Rented</option><option value="maintenance">Maintenance</option></select></div>
                <div class="col-12"><label class="form-label-custom">Image</label><input type="file" name="image" class="form-control form-control-custom" accept="image/*"></div>
                <div class="col-12"><label class="form-label-custom">Description</label><textarea name="description" class="form-control form-control-custom" rows="2" id="carDescr"></textarea></div>
                <div class="col-12"><label class="form-label-custom">Features</label><input type="text" name="features" class="form-control form-control-custom" id="carFeatures" placeholder="Comma separated"></div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary-custom" onclick="saveCar()"><i class="fas fa-save me-1"></i>Save</button>
    </div>
</div></div></div>

<script>
let cPage = 1;
document.addEventListener('DOMContentLoaded', () => { loadBrandOptions(); loadCars(); });

async function loadBrandOptions() {
    const data = await apiRequest('brands/index.php?status=active');
    document.getElementById('carBrand').innerHTML = data.brands.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
}

async function loadCars() {
    try {
        const data = await apiRequest(`cars/index.php?status=all&page=${cPage}`);
        document.getElementById('carsTable').innerHTML = data.cars.map(c => `
            <tr>
                <td><img src="${getCarImage(c.image)}" class="rounded" style="width:50px;height:35px;object-fit:cover;border:1px solid var(--border-color);"></td>
                <td><strong>${c.model}</strong> <small class="text-secondary">(${c.year})</small></td>
                <td>${c.brand_name}</td>
                <td><span class="badge-status badge-${c.category === 'luxury' ? 'confirmed' : 'active'}">${c.category}</span></td>
                <td>${formatCurrency(c.price_per_day)}</td>
                <td>${statusBadge(c.status)}</td>
                <td>
                    <button class="btn btn-glass btn-sm me-1" onclick='editCar(${JSON.stringify(c)})'><i class="fas fa-edit"></i></button>
                    <button class="btn btn-glass btn-sm" onclick="deleteCar(${c.id})"><i class="fas fa-trash text-danger"></i></button>
                </td>
            </tr>
        `).join('');
        
        document.getElementById('carsPag').innerHTML = renderPagination(data.pagination);
        document.querySelectorAll('#carsPag .page-link').forEach(l => l.addEventListener('click', e => {
            e.preventDefault(); cPage = parseInt(e.target.closest('.page-link').dataset.page); loadCars();
        }));
    } catch(e) { 
        console.error('Admin loadCars error:', e);
        Toast.error('Failed to load cars: ' + e.message); 
    }
}

function resetCarForm() {
    document.getElementById('carModalTitle').textContent = 'Add Vehicle';
    document.getElementById('carForm').reset();
    document.getElementById('carId').value = '';
}

function editCar(c) {
    document.getElementById('carModalTitle').textContent = 'Edit Vehicle';
    document.getElementById('carId').value = c.id;
    document.getElementById('carBrand').value = c.brand_id;
    document.getElementById('carModel').value = c.model;
    document.getElementById('carYear').value = c.year;
    document.getElementById('carColor').value = c.color || '';
    document.getElementById('carPrice').value = c.price_per_day;
    document.getElementById('carFuel').value = c.fuel_type;
    document.getElementById('carTrans').value = c.transmission;
    document.getElementById('carSeats').value = c.seats;
    document.getElementById('carCat').value = c.category;
    document.getElementById('carMileage').value = c.mileage || '';
    document.getElementById('carStatus').value = c.status;
    document.getElementById('carDescr').value = c.description || '';
    document.getElementById('carFeatures').value = c.features || '';
    new bootstrap.Modal(document.getElementById('carModal')).show();
}

async function saveCar() {
    const id = document.getElementById('carId').value;
    const formData = new FormData(document.getElementById('carForm'));
    try {
        if (id) {
            await apiRequest('cars/index.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(formData).toString()
            });
        } else {
            await apiRequest('cars/index.php', { method: 'POST', body: formData });
        }
        Toast.success(id ? 'Vehicle updated!' : 'Vehicle added!');
        bootstrap.Modal.getInstance(document.getElementById('carModal')).hide();
        loadCars();
    } catch(e) { Toast.error(e.message); }
}

async function deleteCar(id) {
    if (!confirmDelete('Delete this vehicle?')) return;
    try {
        await apiRequest(`cars/index.php?id=${id}`, { method: 'DELETE' });
        Toast.success('Vehicle deleted.');
        loadCars();
    } catch(e) { Toast.error(e.message); }
}
</script>

<?php require_once 'partials/footer.php'; ?>
