<?php

if (!isset($appConfig) && file_exists(__DIR__ . '/../config/app.php')) { $appConfig = require __DIR__ . '/../config/app.php'; }
if (!isset($appConfig) || !is_array($appConfig)) { /* Handle error: $appConfig tidak ada */ return; }

$currentAppName = isset($appConfig['APP_NAME']) ? htmlspecialchars($appConfig['APP_NAME']) : 'Nama Aplikasi Default';
$baseUrl = isset($appConfig['BASE_URL']) ? htmlspecialchars($appConfig['BASE_URL']) : '/';
$assetsUrl = isset($appConfig['ASSETS_URL']) ? htmlspecialchars($appConfig['ASSETS_URL']) : rtrim($baseUrl, '/') . '/assets/';
$currentPageTitle = isset($pageTitle) ? htmlspecialchars($pageTitle) : $currentAppName; 

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
    <title><?php echo $currentPageTitle; ?></title>
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>css/style.css">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f8f9; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
        .page-wrapper { flex: 1 0 auto; }
        .site-header { background-color: #007bff; color: white; padding: 1rem 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .site-header .container, .main-navigation .container, .main-content-area .container, .site-footer .container { max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 15px; padding-right: 15px; }
        .site-header h1 { margin: 0; font-size: 1.8rem; font-weight: 600; } .site-header h1 a { color: white; text-decoration: none; }
        .main-navigation { background-color: #0069d9; border-bottom: 1px solid #005cbf; }
        .main-navigation ul { list-style-type: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; }
        .main-navigation ul li a { display: block; color: #f8f9fa; text-decoration: none; padding: 0.8rem 1.2rem; font-weight: 500; transition: background-color 0.2s ease-in-out; }
        .main-navigation ul li a:hover, .main-navigation ul li.active a { background-color: #005cbf; color: #fff; }
        .main-content-area { padding-top: 1.5rem; padding-bottom: 2rem; }
        .flash-message { padding: 1rem; margin-bottom: 1.5rem; border: 1px solid transparent; border-radius: 0.25rem; font-size: 0.95em; }
        .flash-message.success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .flash-message.error { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
        .flash-message.info { color: #055160; background-color: #cff4fc; border-color: #b6effb; }
        .flash-message.warning { color: #664d03; background-color: #fff3cd; border-color: #ffecb5; }
        .site-footer { background-color: #343a40; color: #adb5bd; padding: 2rem 0; margin-top: auto; text-align: center; font-size: 0.875em; flex-shrink: 0; }
        .site-footer p { margin: 0.5rem 0; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <header class="site-header">
        <div class="container"><h1><a href="<?php echo htmlspecialchars($baseUrl); ?>"><?php echo htmlspecialchars($currentAppName); ?></a></h1></div>
    </header>

    <nav class="main-navigation">
        <div class="container">
            <ul>
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <?php // Navigasi untuk ADMIN yang sedang login ?>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">Dashboard Admin</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>">Logout (<?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?>)</a></li>

                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <?php // Navigasi untuk PENGGUNA BIASA yang sedang login (bukan admin) ?>
                    <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Home</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'user/dashboard'); ?>">Dashboard Saya</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>">Logout (<?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'User'); ?>)</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang</a></li>

                <?php else: ?>
                    <?php // Navigasi untuk PENGUNJUNG (belum login) ?>
                    <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Home</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/login'); ?>">Login</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/register'); ?>">Registrasi</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="main-content-area">
        <div class="container">
            <?php
            // ... (kode untuk menampilkan flash message tetap sama) ...
            if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) {
                $flash = $_SESSION['flash_message'];
                echo '<div class="flash-message ' . htmlspecialchars($flash['type'] ?? 'info') . '">';
                echo $flash['message']; 
                echo '</div>';
                unset($_SESSION['flash_message']); 
            }
            ?>
           