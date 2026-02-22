<?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    </div> <!-- container-fluid -->
  </div> <!-- content -->
</div> <!-- wrapper -->
<?php else: ?>
</div> <!-- container -->
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sidebar toggle for admin layout
document.addEventListener('DOMContentLoaded', function() {
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
});
</script>
</body>
</html>
