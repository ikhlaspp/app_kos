<?php
// File: views/admin/partials/_footer.php
$appName = $appConfig['APP_NAME'] ?? 'App Kos';
$currentYear = date('Y');
?>
<footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © <?php echo $currentYear; ?>. <?php echo htmlspecialchars($appName); ?>. All rights reserved.</span>
    </div>
</footer>