</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
document.getElementById('adminLogout')?.addEventListener('click', async (e) => {
    e.preventDefault();
    await apiRequest('auth/logout.php');
    window.location.href = '<?= APP_URL ?>/views/login.php';
});
</script>
</body>
</html>
