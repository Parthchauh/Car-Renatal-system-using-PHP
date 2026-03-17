<?php $pageTitle = 'FAQ'; require_once 'partials/header.php'; ?>
<div class="page-header">
    <div class="container position-relative" style="z-index:1">
        <h1 class="fade-in-up">Frequently Asked <span class="gradient-text">Questions</span></h1>
        <nav aria-label="breadcrumb" class="fade-in-up stagger-1">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">FAQ</li>
            </ol>
        </nav>
    </div>
</div>
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div id="faqContent" class="faq-accordion">
                    <div class="text-center py-4"><div class="spinner-custom mx-auto"></div></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const data = await apiRequest('content/index.php?key=faq');
        const container = document.getElementById('faqContent');
        // Parse HTML FAQ items
        const temp = document.createElement('div');
        temp.innerHTML = data.content.content;
        const items = temp.querySelectorAll('.faq-item');
        
        if (items.length > 0) {
            let html = '<div class="accordion" id="faqAccordion">';
            items.forEach((item, i) => {
                const q = item.querySelector('h5')?.textContent || 'Question';
                const a = item.querySelector('p')?.textContent || 'Answer';
                html += `
                <div class="accordion-item fade-in-up" style="animation-delay:${i*0.1}s">
                    <h2 class="accordion-header">
                        <button class="accordion-button ${i > 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#faq${i}">
                            <i class="fas fa-question-circle me-2" style="color:var(--primary-light)"></i> ${q}
                        </button>
                    </h2>
                    <div id="faq${i}" class="accordion-collapse collapse ${i === 0 ? 'show' : ''}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">${a}</div>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        } else {
            container.innerHTML = data.content.content;
        }
    } catch(e) {
        document.getElementById('faqContent').innerHTML = '<p class="text-secondary text-center">Failed to load FAQ.</p>';
    }
});
</script>
<?php require_once 'partials/footer.php'; ?>
