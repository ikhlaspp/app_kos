<?php
// File: nama_proyek_kos/views/admin/partials/_sidebar.php
$baseUrl = $appConfig['BASE_URL'] ?? './';

// --- Logika untuk Menentukan Segmen URL Aktif ---
$currentRoutePathForMenu = '';
if (isset($_GET['route'])) {
    $currentRoutePathForMenu = trim($_GET['route'], '/');
} else { 
    $requestUriPathForMenu = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $basePathFromConfigForMenu = trim(parse_url($baseUrl, PHP_URL_PATH), '/');
    if (!empty($basePathFromConfigForMenu) && strpos($requestUriPathForMenu, $basePathFromConfigForMenu) === 0) {
        $currentRoutePathForMenu = trim(substr($requestUriPathForMenu, strlen($basePathFromConfigForMenu)), '/');
    } elseif (empty($basePathFromConfigForMenu)) {
        $currentRoutePathForMenu = $requestUriPathForMenu;
    }
    if (strpos($currentRoutePathForMenu, 'index.php') === 0) {
        $currentRoutePathForMenu = trim(substr($currentRoutePathForMenu, strlen('index.php')), '/');
    }
}

$segmentsForMenu = !empty($currentRoutePathForMenu) ? explode('/', $currentRoutePathForMenu) : [];
$currentControllerSlug = $segmentsForMenu[0] ?? ''; 
$mainActionSlug = $segmentsForMenu[1] ?? 'dashboard'; 

if ($currentControllerSlug !== 'admin') {
    $mainActionSlug = 'not_on_admin_page'; 
}

// --- MULAI DEBUG OUTPUT LANGSUNG ---
// echo "<div style='background: #fff; color: #000; padding: 10px; border: 2px solid red; position: fixed; top: 0; left: 260px; z-index: 99999; font-size: 12px;'>";
// echo "<strong>DEBUG SIDEBAR:</strong><br>";
// echo "REQUEST_URI: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "<br>";
// echo "currentRoutePathForMenu: '" . htmlspecialchars($currentRoutePathForMenu) . "'<br>";
// echo "segmentsForMenu[0] (Controller Slug): '" . htmlspecialchars($currentControllerSlug) . "'<br>";
// echo "mainActionSlug (Menu Group): '" . htmlspecialchars($mainActionSlug) . "'<br>";
// echo "<hr>";
// --- AKHIR DEBUG OUTPUT LANGSUNG ---


// Definisikan grup untuk setiap menu utama untuk perbandingan yang lebih bersih
$isDashboardActive = ($mainActionSlug === 'dashboard');
$isKosMenuActive = ($mainActionSlug === 'kos'|| $mainActionSlug === 'kosCreate' || $mainActionSlug === 'kosEdit' || $mainActionSlug === 'kosDeletegambar' || $mainActionSlug === 'kosDelete'); 
$isUsersMenuActive = ($mainActionSlug === 'users' || $mainActionSlug === 'useredit');
$isBookingsMenuActive = ($mainActionSlug === 'bookings' || $mainActionSlug === 'bookingDetail' || $mainActionSlug === 'bookingconfirm' || $mainActionSlug === 'bookingreject');

// // --- DEBUG KONDISI AKTIF ---
// echo "isDashboardActive: " . ($isDashboardActive ? 'TRUE' : 'FALSE') . "<br>";
// echo "isKosMenuActive: " . ($isKosMenuActive ? 'TRUE' : 'FALSE') . "<br>";
// echo "isUsersMenuActive: " . ($isUsersMenuActive ? 'TRUE' : 'FALSE') . "<br>";
// echo "isBookingsMenuActive: " . ($isBookingsMenuActive ? 'TRUE' : 'FALSE') . "<br>";
// echo "</div>";
// // --- AKHIR DEBUG KONDISI AKTIF ---

?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav admin-menu">
        <li class="nav-item <?php echo $isDashboardActive ? 'active': ''; ?>">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item <?php echo $isKosMenuActive ? 'active': ''; ?>">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/kos'); ?>">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Kelola Kos</span>
            </a>
        </li>
        <li class="nav-item <?php echo $isUsersMenuActive ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/users'); ?>">
                <i class="icon-head menu-icon"></i> 
                <span class="menu-title">Kelola Pengguna</span>
            </a>
        </li>
        <li class="nav-item <?php echo $isBookingsMenuActive ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/bookings'); ?>">
                <i class="icon-paper menu-icon"></i> 
                <span class="menu-title">Kelola Pemesanan</span>
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