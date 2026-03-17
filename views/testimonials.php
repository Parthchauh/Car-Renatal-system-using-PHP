<?php $pageTitle = 'Testimonials'; require_once 'partials/header.php'; requireLogin(false); ?>
<div class="page-header" style="min-height:auto;padding:7rem 0 2rem;">
    <div class="container position-relative" style="z-index:1"><h1 class="fade-in-up">Write a <span class="gradient-text">Review</span></h1></div>
</div>
<section class="section" style="padding:2rem 0 5rem;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5 fade-in-up">
                <div class="glass-card">
                    <h5 class="mb-4"><i class="fas fa-star me-2" style="color:var(--warning)"></i>Submit Your Review</h5>
                    <form id="testimonialForm">
                        <input type="hidden" name="booking_id" value="<?= intval($_GET['booking_id'] ?? 0) ?>">
                        <div class="mb-3">
                            <label class="form-label-custom">Rating *</label>
                            <div class="star-rating">
                                <?php for($i=5;$i>=1;$i--): ?>
                                    <input type="radio" name="rating" value="<?=$i?>" id="star<?=$i?>" <?=$i==5?'checked':''?>>
                                    <label for="star<?=$i?>"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Your Review *</label>
                            <textarea name="review" class="form-control form-control-custom" rows="5" placeholder="Share your experience..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100"><i class="fas fa-paper-plane me-2"></i>Submit Review</button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-7 fade-in-up stagger-1">
                <h5 class="mb-3">All Reviews</h5>
                <div id="testimonialsList">
                    <div class="text-center py-5"><div class="spinner-custom mx-auto"></div></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', loadReviews);

document.getElementById('testimonialForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitForm(e.target, 'testimonials/index.php', { resetForm: true, onSuccess: loadReviews });
});

async function loadReviews() {
    try {
        const data = await apiRequest('testimonials/index.php');
        const container = document.getElementById('testimonialsList');
        if (data.testimonials.length > 0) {
            container.innerHTML = data.testimonials.map(t => `
                <div class="testimonial-card mb-3">
                    <div class="stars mb-2">${starRating(t.rating)}</div>
                    <p class="testimonial-text">${t.review}</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">${t.user_name.charAt(0)}</div>
                        <div>
                            <div style="font-weight:600;">${t.user_name}</div>
                            <div class="text-secondary" style="font-size:0.8rem;">${formatDate(t.created_at)}</div>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="text-secondary text-center">No reviews yet. Be the first!</p>';
        }
    } catch(e) { Toast.error('Failed to load reviews.'); }
}
</script>
<?php require_once 'partials/footer.php'; ?>
