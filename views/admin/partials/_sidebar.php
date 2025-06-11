<?php
// File: views/admin/partials/_sidebar.php

$baseUrl = $appConfig['BASE_URL'] ?? './';

// --- Logic to Determine Active URL Path (for JavaScript consumption) ---
$requestUriForMenu = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$basePathFromConfigForMenu = trim(parse_url($appConfig['BASE_URL'] ?? '/', PHP_URL_PATH), '/');

$currentRoutePathForMenu = $requestUriForMenu;
if (!empty($basePathFromConfigForMenu) && strpos($currentRoutePathForMenu, $basePathFromConfigForMenu) === 0) {
    $currentRoutePathForMenu = trim(substr($currentRoutePathForMenu, strlen($basePathFromConfigForMenu)), '/');
}
if (strpos($currentRoutePathForMenu, 'index.php') === 0) {
    $currentRoutePathForMenu = trim(substr($currentRoutePathForMenu, strlen('index.php')), '/');
}

// PHP flags are removed, active state handled by JS below.
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav admin-menu">
        <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/kos'); ?>">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Kelola Kos</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/users'); ?>">
                <i class="icon-head menu-icon"></i> 
                <span class="menu-title">Kelola Pengguna</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/bookings'); ?>">
                <i class="icon-paper menu-icon"></i> 
                <span class="menu-title">Kelola Pemesanan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl . 'admin/voucher'); ?>">
                <i class="icon-tag menu-icon"></i>
                <span class="menu-title">Kelola Voucher</span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- FIX: Wrap active state logic in a setTimeout to ensure it runs last ---
    setTimeout(function() {
        console.log('Sidebar JS: Executing active state logic after delay.'); // Debug log

        const baseUrl = '<?php echo htmlspecialchars($baseUrl); ?>';
        const currentPath = '<?php echo htmlspecialchars($currentRoutePathForMenu); ?>';
        console.log('Sidebar JS: currentPath = ' + currentPath);

        const navLinks = document.querySelectorAll('#sidebar .nav-item .nav-link');
        console.log('Sidebar JS: Found ' + navLinks.length + ' navigation links.');

        // Remove 'active' class from all items first
        navLinks.forEach(link => {
            if (link.closest('.nav-item').classList.contains('active')) {
                link.closest('.nav-item').classList.remove('active');
                console.log('Sidebar JS: Removed active from ' + link.getAttribute('href'));
            }
        });
        console.log('Sidebar JS: All active classes removed.');

        // Add 'active' class to the matching item
        navLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (!linkHref) return;

            let normalizedLinkPath = linkHref.replace(baseUrl, '').replace(/(^\/|\/$)/g, '');

            if (normalizedLinkPath === '') {
                normalizedLinkPath = 'admin/dashboard';
            }

            let shouldBeActive = false;
            if (currentPath.startsWith(normalizedLinkPath) && normalizedLinkPath !== '') {
                shouldBeActive = true;
            }

            if (shouldBeActive) {
                link.closest('.nav-item').classList.add('active');
                console.log('Sidebar JS: Added active to ' + linkHref + ' (matched ' + normalizedLinkPath + ')');
            }
        });
        console.log('Sidebar JS: Active class assignment complete.');

        let activeItemsFinalCount = document.querySelectorAll('#sidebar .nav-item.active').length;
        console.log('Sidebar JS: Final active items count: ' + activeItemsFinalCount);

    }, 200); // 200ms delay: Adjust this value if needed. Too low might still conflict, too high might be noticeable.
});
</script>