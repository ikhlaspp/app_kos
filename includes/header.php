<?php

if (!isset($appConfig) && file_exists(__DIR__ . '/../config/app.php')) { $appConfig = require __DIR__ . '/../config/app.php';}
if (!isset($appConfig) || !is_array($appConfig)) {
    $appConfig = [
        'APP_NAME' => 'Aplikasi Default',
        'BASE_URL' => '/',
        'ASSETS_URL' => '/assets/',
        'SESSION_NAME' => 'PHPSESSID_APP'
    ];
}

$currentAppName = $appConfig['APP_NAME'] ?? 'Nama Aplikasi Default';
$baseUrl = $appConfig['BASE_URL'] ?? '/';
$assetsUrl = $appConfig['ASSETS_URL'] ?? rtrim($baseUrl, '/') . '/assets/';
$currentPageTitle = $pageTitle ?? $currentAppName;

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
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>css/style.css">
    <style>
        html {
            height: 100%;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F9F7F7;
            color: #112D4E;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .page-wrapper {
            flex: 1 0 auto;
        }

        .site-header {
            background-color: #112D4E;
            color: #F9F7F7;
            padding: 12px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .site-header .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 15px;
            padding-right: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between; /* Ini akan memisahkan logo, nav, dan header-icons */
        }

        .logo-area a {
            font-size: 1.9rem;
            font-weight: 700;
            color: #F9F7F7;
            text-decoration: none;
            line-height: 1;
        }

        .main-navigation {
            margin-left: 25px; /* Jarak dari logo */
            /* margin-right: auto; /* Dihapus agar nav tidak mendorong ikon terlalu jauh jika ikon hanya login */
        }
        /* Jika .main-navigation dan .header-icons harus berdampingan dan .header-icons di ujung kanan, */
        /* .main-navigation bisa dibiarkan tanpa margin-right: auto jika .header-icons punya margin-left: auto atau container menggunakan space-between dengan benar */


        .main-navigation ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
        }
        .main-navigation ul li a {
            display: block;
            color: #F9F7F7;
            text-decoration: none;
            padding: 0.6rem 1rem;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
        }
        .main-navigation ul li a:hover,
        .main-navigation ul li.active a {
            background-color: #3F72AF;
            color: #FFFFFF;
        }

        .header-icons {
            display: flex;
            align-items: center;
            margin-left: auto; /* Mendorong grup ikon (termasuk login) ke kanan */
        }
        .header-icons .icon-link { /* Untuk ikon seperti hamburger, search */
            color: #F9F7F7;
            text-decoration: none;
            font-size: 1.25rem;
            margin-left: 18px; /* Jarak antar ikon */
            padding: 5px;
            cursor: pointer;
            transition: color 0.2s ease-in-out;
        }
        .header-icons .icon-link:hover {
            color: #DBE2EF;
        }
        .hamburger-menu-placeholder { display: none; }


        /* CSS BARU untuk tombol Login/Logout di dalam header-icons */
        .header-auth-link {
            color: #F9F7F7;
            text-decoration: none;
            padding: 0.5rem 0.9rem; /* Sedikit disesuaikan agar pas */
            font-weight: 500;
            font-size: 0.80rem; /* Sedikit lebih kecil agar serasi dengan ikon */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            margin-left: 15px; /* Jarak dari elemen sebelumnya di header-icons (mis. ikon lain atau tepi) */
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            line-height: 1.2; /* Penyesuaian untuk alignment vertikal */
            border: 1px solid transparent; /* Opsional: untuk outline jika diperlukan */
        }
        .header-auth-link:hover {
            background-color: #3F72AF;
            color: #FFFFFF;
            border-color: #3F72AF; /* Opsional */
        }
        /* Styling alternatif jika ingin seperti tombol */
        /*
        .header-auth-link.button-style {
            background-color: #3F72AF;
            color: #FFFFFF;
            padding: 8px 15px;
        }
        .header-auth-link.button-style:hover {
            background-color: #DBE2EF;
            color: #112D4E;
        }
        */


        @media (max-width: 992px) {
            .main-navigation {
                display: none;
                position: absolute;
                top: 54px; /* Sesuaikan dengan tinggi header Anda */
                left: 0;
                right: 0;
                background-color: #112D4E;
                box-shadow: 0 8px 16px rgba(0,0,0,0.15);
                z-index: 999;
                padding: 8px 0;
            }
            .main-navigation.open { display: block; }
            .main-navigation.open ul { flex-direction: column; width: 100%; }
            .main-navigation.open ul li { text-align: center; width: 100%; }
            .main-navigation.open ul li a {
                padding: 12px 15px;
                border-top: 1px solid #3F72AF;
                border-radius: 0;
            }
            .main-navigation.open ul li:first-child a { border-top: none; }
            .main-navigation.open ul li a:hover,
            .main-navigation.open ul li.active a {
                background-color: #3F72AF;
                color: #FFFFFF;
            }
            
            .hamburger-menu-placeholder { display: block !important; } /* Pastikan ikon hamburger muncul */

            /* Tombol Login/Logout di mobile mungkin perlu penyesuaian jika terlalu banyak item di header-icons */
            /* Jika ingin menyembunyikan tombol login teks di mobile (misalnya jika login ada di dalam menu hamburger juga) */
            /* .header-auth-link { display: none; } */
        }

        .main-content-area {
            padding-top: 1.5rem;
            padding-bottom: 2rem;
            flex: 1 0 auto;
        }
        .main-content-area .container {
            max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 15px; padding-right: 15px;
        }

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
                    <?php // Navigasi utama (TANPA Login/Logout) ?>
                    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">Dashboard Admin</a></li>
                        <?php // Tambahkan link admin spesifik lainnya di sini jika perlu ?>
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
                <?php // Tombol Login/Logout SEKARANG DI SINI ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    $userNameDisplay = $_SESSION['user_nama'] ?? ($_SESSION['is_admin'] ? 'Admin' : 'User');
                    ?>
                    <a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>" class="header-auth-link">Logout (<?php echo htmlspecialchars($userNameDisplay); ?>)</a>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($baseUrl . 'auth/login'); ?>" class="header-auth-link">Login</a>
                <?php endif; ?>
                
                <?php // Ikon hamburger untuk mobile (placeholder diaktifkan) ?>
                <a href="#" class="icon-link hamburger-menu-placeholder" id="hamburgerIcon" aria-label="Menu">&#9776;</a>
                <?php // Anda bisa menambahkan ikon lain di sini jika perlu, contoh: ?>
                </div>
        </div>
    </header>
     <main class="main-content-area">
        <div class="container">
            <?php
            if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) {
                $flash = $_SESSION['flash_message'];
                echo '<div class="flash-message ' . htmlspecialchars($flash['type'] ?? 'info') . '">';
                echo $flash['message']; 
                echo '</div>';
                unset($_SESSION['flash_message']); 
            }
            ?>

