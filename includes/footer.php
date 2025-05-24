<?php

if (!isset($appConfig) && file_exists(__DIR__ . '/../config/app.php')) { $appConfig = require __DIR__ . '/../config/app.php';}
if (!isset($appConfig) || !is_array($appConfig)) { /* Handle error */ return; }

$currentYear = date('Y');
$appName = $appConfig['APP_NAME'] ?? 'Aplikasi Booking Kos';
$assetsUrl = $appConfig['ASSETS_URL'] ?? ($appConfig['BASE_URL'] ?? '/') . 'assets/';
?>
            </div> </main> </div> <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo $currentYear; ?> <?php echo htmlspecialchars($appName); ?>. All Rights Reserved.</p>
        </div>
    </footer>

<?php
// Memuat UI Chat jika pengguna sudah login DAN $appConfig tersedia
if (isset($_SESSION['user_id']) && isset($appConfig) && is_array($appConfig)) { 
    if (file_exists(__DIR__ . '/chat_ui.php')) {
        require_once __DIR__ . '/chat_ui.php'; 
    } else {
        error_log("Peringatan Kritis: File includes/chat_ui.php tidak ditemukan.");
    }
}
?>

</body>
</html>