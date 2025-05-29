<?php
// File: views/admin/partials/_sidebar.php
$baseUrl = $appConfig['BASE_URL'] ?? './';
$currentUri = $_SERVER['REQUEST_URI']; // Untuk menandai menu aktif

$currentRoutePathForMenu = '';
// Coba ambil dari parameter 'route' (jika .htaccess digunakan)
if (isset($_GET['route'])) {
    $currentRoutePathForMenu = trim($_GET['route'], '/');
} else { 
    // Fallback jika tidak ada parameter 'route', coba parse dari REQUEST_URI
    // Ini mungkin perlu penyesuaian lebih lanjut tergantung konfigurasi server/URL Anda
    $requestUriPathForMenu = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $basePathFromConfigForMenu = trim(parse_url($baseUrl, PHP_URL_PATH), '/'); // Gunakan $baseUrl yang sudah ada

    if (!empty($basePathFromConfigForMenu) && strpos($requestUriPathForMenu, $basePathFromConfigForMenu) === 0) {
        $currentRoutePathForMenu = trim(substr($requestUriPathForMenu, strlen($basePathFromConfigForMenu)), '/');
    } elseif (empty($basePathFromConfigForMenu)) {
        $currentRoutePathForMenu = $requestUriPathForMenu;
    }
     // Hapus 'index.php' dari path jika ada (misal, /index.php/admin/dashboard)
    if (strpos($currentRoutePathForMenu, 'index.php') === 0) {
        $currentRoutePathForMenu = trim(substr($currentRoutePathForMenu, strlen('index.php')), '/');
    }
}

$segmentsForMenu = !empty($currentRoutePathForMenu) ? explode('/', $currentRoutePathForMenu) : [];
// Jika path adalah 'admin/controller/action', maka:
// $segmentsForMenu[0] adalah 'admin'
// $segmentsForMenu[1] adalah nama controller/menu utama (misal 'dashboard', 'kos', 'users')
// $segmentsForMenu[2] adalah action spesifik (misal 'create', 'edit', atau ID)
$mainAdminSegment = $segmentsForMenu[1] ?? 'dashboard'; // Default ke dashboard jika tidak ada segmen kedua setelah 'admin'
if (($segmentsForMenu[0] ?? '') !== 'admin') { // Jika bukan halaman admin, default ke 'none' agar tidak ada yang aktif
    $mainAdminSegment = 'none'; 
}
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item <?php echo (strpos($currentUri, '/admin/dashboard') !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item <?php echo (strpos($currentUri, '/admin/kos') !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/kos'); ?>">
                <i class="icon-layout menu-icon"></i> <span class="menu-title">Kelola Kos</span>
            </a>
        </li>
        <li class="nav-item <?php echo (strpos($currentUri, '/admin/users') !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/users'); ?>">
                <i class="icon-head menu-icon"></i> <span class="menu-title">Kelola Pengguna</span>
            </a>
        </li>
        <li class="nav-item <?php echo (strpos($currentUri, '/admin/bookings') !== false) ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/bookings'); ?>">
                <i class="icon-paper menu-icon"></i> <span class="menu-title">Kelola Pemesanan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>">
                <i class="ti-power-off menu-icon"></i>
                <span class="menu-title">Logout</span>
            </a>
        </li>
    </ul>
</nav>