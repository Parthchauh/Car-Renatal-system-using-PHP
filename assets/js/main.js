/**
 * DriveElite - Main JavaScript
 * AJAX utilities, toast notifications, and shared functionality
 */

// ============ Configuration ============
const APP = {
    baseUrl: window.location.origin + '/Car Rental system',
    apiUrl: window.location.origin + '/Car Rental system/api'
};

// ============ Toast Notification System ============
class Toast {
    static container = null;

    static init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    }

    static show(message, type = 'info', duration = 4000) {
        this.init();
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        const toast = document.createElement('div');
        toast.className = `toast-custom toast-${type}`;
        toast.innerHTML = `<i class="${icons[type] || icons.info}"></i><span>${message}</span>`;
        
        toast.addEventListener('click', () => this.remove(toast));
        this.container.appendChild(toast);

        setTimeout(() => this.remove(toast), duration);
    }

    static remove(toast) {
        if (toast && toast.parentNode) {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 300);
        }
    }

    static success(msg) { this.show(msg, 'success'); }
    static error(msg) { this.show(msg, 'error', 5000); }
    static warning(msg) { this.show(msg, 'warning'); }
    static info(msg) { this.show(msg, 'info'); }
}

// ============ AJAX Helper ============
async function apiRequest(endpoint, options = {}) {
    const url = `${APP.apiUrl}/${endpoint}`;
    const config = {
        headers: {},
        ...options
    };

    // Add CSRF token for non-GET requests
    if (config.method && config.method !== 'GET') {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken;
        }
    }

    try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        if (!response.ok && !data.success) {
            throw new Error(data.message || 'Request failed');
        }
        
        return data;
    } catch (error) {
        if (error.message === 'Failed to fetch') {
            Toast.error('Network error. Please check your connection.');
        }
        throw error;
    }
}

/**
 * Submit form data via AJAX
 */
async function submitForm(formElement, endpoint, options = {}) {
    const form = typeof formElement === 'string' ? document.querySelector(formElement) : formElement;
    if (!form) return;

    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn?.innerHTML;

    try {
        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        }

        const formData = new FormData(form);
        
        const response = await apiRequest(endpoint, {
            method: 'POST',
            body: formData
        });

        if (response.success) {
            Toast.success(response.message);
            if (options.onSuccess) options.onSuccess(response);
            if (options.resetForm) form.reset();
            if (options.redirect) {
                setTimeout(() => {
                    window.location.href = `${APP.baseUrl}/views/${response.redirect || options.redirect}`;
                }, 1000);
            }
            if (options.closeModal) {
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                if (modal) modal.hide();
            }
        }

        return response;
    } catch (error) {
        Toast.error(error.message);
        if (options.onError) options.onError(error);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
}

// ============ Navbar Scroll Effect ============
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    }
});

// ============ Format Helpers ============
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric'
    });
}

function calculateDays(start, end) {
    const startDate = new Date(start);
    const endDate = new Date(end);
    const diffTime = Math.abs(endDate - startDate);
    return Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
}

// ============ Status Badge Generator ============
function statusBadge(status) {
    return `<span class="badge-status badge-${status}">${status}</span>`;
}

/**
 * Handles car image paths (local vs external)
 */
function getCarImage(path) {
    if (!path || path.includes('default')) {
        return 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=400&h=250&fit=crop';
    }
    if (path.startsWith('http')) {
        return path;
    }
    return `${APP.baseUrl}/${path}`;
}


// ============ Star Rating Display ============
function starRating(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="fa${i <= rating ? 's' : 'r'} fa-star"></i>`;
    }
    return stars;
}

// ============ Pagination Generator ============
function renderPagination(pagination, onPageClick) {
    if (!pagination || pagination.total_pages <= 1) return '';

    let html = '<nav><ul class="pagination pagination-custom justify-content-center">';
    
    // Previous
    html += `<li class="page-item ${!pagination.has_prev ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${pagination.current_page - 1}"><i class="fas fa-chevron-left"></i></a></li>`;
    
    // Pages
    const start = Math.max(1, pagination.current_page - 2);
    const end = Math.min(pagination.total_pages, pagination.current_page + 2);
    
    for (let i = start; i <= end; i++) {
        html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }
    
    // Next
    html += `<li class="page-item ${!pagination.has_next ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${pagination.current_page + 1}"><i class="fas fa-chevron-right"></i></a></li>`;
    
    html += '</ul></nav>';
    return html;
}

// ============ Confirm Delete ============
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

// ============ Image Preview ============
function previewImage(input, previewElement) {
    const preview = typeof previewElement === 'string' ? document.querySelector(previewElement) : previewElement;
    if (input.files && input.files[0] && preview) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ============ Debounce ============
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// ============ Mobile Sidebar Toggle ============
function toggleSidebar() {
    document.querySelector('.admin-sidebar')?.classList.toggle('show');
}

// ============ DOMContentLoaded ============
document.addEventListener('DOMContentLoaded', () => {
    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });
});
