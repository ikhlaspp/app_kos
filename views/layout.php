<?php
// views/layout.php
// This file will now receive $pageTitle, $appConfig, $_SESSION etc. from BaseController::loadView

// Example of variables you'd expect from the controller or set here
// Ensure these variables are passed to this layout.php if they're used.
// (e.g., $currentAppName, $baseUrl, $assetsUrl, $currentPageTitle)
// For simplicity, let's assume they are passed or derive them here.

// Fetch app config if not available (should be from index.php -> controller -> layout)
if (!isset($appConfig) && file_exists(dirname(__DIR__) . '/config/app.php')) {
    $appConfig = require dirname(__DIR__) . '/config/app.php';
}
// Fallback if appConfig somehow not available (less likely with proper structure)
if (!isset($appConfig) || !is_array($appConfig)) {
    $appConfig = [
        'APP_NAME' => 'Aplikasi Default', 'BASE_URL' => '/', 'ASSETS_URL' => '/assets/', 'SESSION_NAME' => 'PHPSESSID_APP'
    ];
}

$currentAppName = $appConfig['APP_NAME'] ?? 'Nama Aplikasi Default';
$baseUrl = $appConfig['BASE_URL'] ?? '/';
$assetsUrl = $appConfig['ASSETS_URL'] ?? rtrim($baseUrl, '/') . '/assets/';
$currentPageTitle = $pageTitle ?? $currentAppName; // $pageTitle should be passed by controller

// NOTE: Session start should typically be in index.php or early bootstrap file, not here.
// This block is for robustness, but ideally session is already started.
if (session_status() == PHP_SESSION_NONE) {
    if (isset($appConfig['SESSION_NAME'])) { session_name($appConfig['SESSION_NAME']); }
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentPageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>css/style.css">
    <style>
        /* All the global CSS that applies to multiple pages (e.g., body, site-header, flash messages, hamburger) */
        /* You can move some of this into assets/css/style.css for better organization */
        html { height: 100%; }
        body { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; background-color: #F0F4FF; color: #0D2A57; display: flex; flex-direction: column; min-height: 100vh; }
        .page-wrapper { flex: 1 0 auto; }
        .site-header { background-color: #112D4E; color: #F9F7F7; padding: 12px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 1000; }
        .site-header .container { max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 15px; padding-right: 15px; display: flex; align-items: center; justify-content: space-between; }
        .logo-area a { font-size: 1.9rem; font-weight: 700; color: #F9F7F7; text-decoration: none; line-height: 1; }
        .main-navigation { margin-left: 25px; }
        .main-navigation ul { list-style-type: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; }
        .main-navigation ul li a { display: block; color: #F9F7F7; text-decoration: none; padding: 0.6rem 1rem; font-weight: 500; transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-radius: 4px; }
        .main-navigation ul li a:hover, .main-navigation ul li.active a { background-color: #3F72AF; color: #FFFFFF; }
        .header-icons { display: flex; align-items: center; margin-left: auto; }
        .header-icons .icon-link { color: #F9F7F7; text-decoration: none; font-size: 1.25rem; margin-left: 18px; padding: 5px; cursor: pointer; transition: color 0.2s ease-in-out; }
        .header-icons .icon-link:hover { color: #DBE2EF; }
        .hamburger-menu-placeholder { display: none; } /* HIDDEN BY DEFAULT ON LARGE SCREENS */
        .header-auth-link { color: #F9F7F7; text-decoration: none; padding: 0.5rem 0.9rem; font-weight: 500; font-size: 0.80rem; text-transform: uppercase; letter-spacing: 0.5px; border-radius: 4px; margin-left: 15px; transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out; line-height: 1.2; border: 1px solid transparent; }
        .header-auth-link:hover { background-color: #3F72AF; color: #FFFFFF; border-color: #3F72AF; }

        @media (max-width: 992px) {
            .main-navigation { display: none; position: absolute; top: 54px; left: 0; right: 0; background-color: #112D4E; box-shadow: 0 8px 16px rgba(0,0,0,0.15); z-index: 999; padding: 8px 0; }
            .main-navigation.open { display: block; }
            .main-navigation.open ul { flex-direction: column; width: 100%; }
            .main-navigation.open ul li { text-align: center; width: 100%; }
            .main-navigation.open ul li a { padding: 12px 15px; border-top: 1px solid #3F72AF; border-radius: 0; }
            .main-navigation.open ul li:first-child a { border-top: none; }
            .main-navigation.open ul li a:hover, .main-navigation.open ul li.active a { background-color: #3F72AF; color: #FFFFFF; }
            .hamburger-menu-placeholder { display: block !important; } /* VISIBLE ON SMALL SCREENS */
        }

        .main-content-area { padding-top: 1.5rem; padding-bottom: 2rem; flex: 1 0 auto; }
        .main-content-area .container { max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 15px; padding-right: 15px; }
        .flash-message { padding: 1rem; margin-bottom: 1.5rem; border: 1px solid transparent; border-radius: 0.25rem; font-size: 0.95em; }
        .flash-message.success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .flash-message.error { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
        .flash-message.info { color: #055160; background-color: #cff4fc; border-color: #b6effb; }
        .flash-message.warning { color: #664d03; background-color: #fff3cd; border-color: #ffecb5; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <header class="site-header">
            <div class="container">
                <div class="logo-area">
                    <a href="<?php echo htmlspecialchars($baseUrl); ?>"><?php echo htmlspecialchars($currentAppName); ?></a>
                </div>

                <nav class="main-navigation" id="mainNav">
                    <ul>
                        <?php // Navigation links based on user status (from BaseController data or session) ?>
                        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">Dashboard Admin</a></li>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Home</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'user/dashboard'); ?>">Dashboard Saya</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Home</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                            <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="header-icons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php $userNameDisplay = $_SESSION['user_nama'] ?? ($_SESSION['is_admin'] ? 'Admin' : 'User'); ?>
                        <a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>" class="header-auth-link">Logout (<?php echo htmlspecialchars($userNameDisplay); ?>)</a>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($baseUrl . 'auth/login'); ?>" class="header-auth-link">Login</a>
                    <?php endif; ?>
                    <a href="#" class="icon-link hamburger-menu-placeholder" id="hamburgerIcon" aria-label="Menu">&#9776;</a>
                </div>
            </div>
        </header>

        <main class="main-content-area">
            <div class="container">
                <?php
                // Flash message display logic
                if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) {
                    $flash = $_SESSION['flash_message'];
                    echo '<div class="flash-message ' . htmlspecialchars($flash['type'] ?? 'info') . '">';
                    echo htmlspecialchars($flash['message']);
                    echo '</div>';
                    unset($_SESSION['flash_message']);
                }
                ?>
                <?php echo $viewContent ?? ''; // This variable would hold the buffered output of the specific view ?>
            </div>
        </main>

        <footer class="footer-custom">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($currentAppName); ?>. All rights reserved.</p>
            </div>
        </footer>

    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Simple JavaScript for mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.getElementById('hamburgerIcon');
            const mainNav = document.getElementById('mainNav');

            if (hamburger && mainNav) {
                hamburger.addEventListener('click', function(e) {
                    e.preventDefault();
                    mainNav.classList.toggle('open');
                });
                // Close menu when clicking outside (optional but good UX)
                document.addEventListener('click', function(e) {
                    if (!mainNav.contains(e.target) && !hamburger.contains(e.target)) {
                        mainNav.classList.remove('open');
                    }
                });
            }
        });
    </script>
</body>
</html>