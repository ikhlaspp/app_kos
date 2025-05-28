<?php

if (!isset($appConfig) && file_exists(__DIR__ . '/../config/app.php')) { $appConfig = require __DIR__ . '/../config/app.php'; }
if (!isset($appConfig) || !is_array($appConfig)) { /* Handle error: $appConfig tidak ada */ return; }

$currentAppName = isset($appConfig['APP_NAME']) ? htmlspecialchars($appConfig['APP_NAME']) : 'Nama Aplikasi Anda';
$baseUrl = isset($appConfig['BASE_URL']) ? htmlspecialchars($appConfig['BASE_URL']) : '/';
$assetsUrl = isset($appConfig['ASSETS_URL']) ? htmlspecialchars($appConfig['ASSETS_URL']) : rtrim($baseUrl, '/') . '/assets/';
$currentPageTitle = isset($pageTitle) ? htmlspecialchars($pageTitle) : $currentAppName; // Judul halaman tetap relevan

if (session_status() == PHP_SESSION_NONE) {
    if (isset($appConfig['SESSION_NAME'])) { session_name($appConfig['SESSION_NAME']); }
    session_start();
}

// Logika untuk $bodyClasses yang sebelumnya mengatur padding .main-content-area kini tidak relevan
// Jika Anda punya class body lain yang spesifik untuk header, bisa ditambahkan di sini
// Untuk saat ini, kita biarkan body tanpa class dinamis dari logika ini
$bodyClasses = []; // Kosongkan atau isi dengan class lain jika perlu untuk header

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $currentPageTitle; ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F9F7F7; /* Warna latar belakang default halaman */
            color: #112D4E;
            /* display: flex; flex-direction: column; min-height: 100vh; */
            /* Properti di atas relevan jika ada konten & footer, bisa disesuaikan jika hanya header */
        }
        /* .page-wrapper {
            flex: 1 0 auto;
        } */
        /* .page-wrapper mungkin tidak terlalu dibutuhkan jika hanya header */

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
            justify-content: space-between;
        }

        .logo-area a {
            font-size: 1.9rem;
            font-weight: 700;
            color: #F9F7F7;
            text-decoration: none;
            line-height: 1;
        }

        .main-navigation {
            margin-left: 25px;
            margin-right: auto;
        }
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
        }
        .header-icons .icon-link {
            color: #F9F7F7;
            text-decoration: none;
            font-size: 1.25rem;
            margin-left: 18px;
            padding: 5px;
            cursor: pointer;
            transition: color 0.2s ease-in-out;
        }
        .header-icons .icon-link:hover {
            color: #DBE2EF;
        }
        .hamburger-menu-placeholder { display: none; }

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
            .main-navigation.open ul li a { padding: 12px 15px; border-top: 1px solid #3F72AF; border-radius: 0; }
            .main-navigation.open ul li:first-child a { border-top: none; }
            .main-navigation.open ul li a:hover, .main-navigation.open ul li.active a { background-color: #3F72AF; color: #FFFFFF; }
            .hamburger-menu-placeholder { display: block; }
        }

        /* CSS untuk .main-content-area dan .flash-message telah dihapus */

    </style>
</head>
<body class="<?php echo implode(' ', $bodyClasses); ?>"> <?php // Class body dinamis bisa dikosongkan jika tidak ada yang relevan untuk header ?>
<?php // .page-wrapper bisa dihapus jika tidak ada elemen lain selain header, atau dipertahankan jika ada struktur lain yang membutuhkannya nanti ?>
<?php // <div class="page-wrapper"> ?>

    <header class="site-header">
        <div class="container">
            <div class="logo-area">
                <a href="<?php echo htmlspecialchars($baseUrl); ?>"><?php echo htmlspecialchars($currentAppName); ?></a>
            </div>

            <nav class="main-navigation" id="mainNav">
                <ul>
                    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">Dashboard Admin</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>">Logout (<?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?>)</a></li>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Home</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'user/dashboard'); ?>">Dashboard Saya</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>">Logout (<?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'User'); ?>)</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Home</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/login'); ?>">Login</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/register'); ?>">Registrasi</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="header-icons">
                <a href="#" class="icon-link search-icon-placeholder" aria-label="Cari">&#x1F50D;</a>
                <a href="#" class="icon-link hamburger-menu-placeholder" id="hamburgerIcon" aria-label="Menu">&#9776;</a>
            </div>
        </div>
    </header>

    <?php // Elemen <main class="main-content-area"> telah dihapus ?>

<?php // Penutup </div> untuk .page-wrapper jika digunakan ?>
<?php // </div> ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const mainNav = document.getElementById('mainNav');

        if (hamburgerIcon && mainNav) {
            hamburgerIcon.addEventListener('click', function(event) {
                event.preventDefault();
                mainNav.classList.toggle('open');
            });
        }
    });
</script>
</body>
</html>