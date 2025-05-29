<?php
// File: nama_proyek_kos/views/admin/layout_admin.php
// File ini akan menjadi "template" untuk semua halaman admin.
// Variabel $appConfig, $pageTitle, dan $contentView (path ke view spesifik admin)
// akan di-pass ke sini dari AdminController.

// Pastikan $appConfig dan $pageTitle ada
if (!isset($appConfig) || !is_array($appConfig)) {
    // Fallback jika $appConfig tidak ada, ini seharusnya tidak terjadi jika BaseController benar
    $appConfig = ['BASE_URL' => './', 'APP_NAME' => 'Admin Panel', 'ASSETS_URL' => './assets/'];
}
$baseUrl = $appConfig['BASE_URL'] ?? './';
$assetsUrl = $appConfig['ASSETS_URL'] ?? './assets/';
$appName = $appConfig['APP_NAME'] ?? 'Admin Panel';
$currentPageTitle = $pageTitle ?? $appName;

// Session data untuk header (nama & email admin)
$adminNama = $_SESSION['user_nama'] ?? 'Admin';
$adminEmail = $_SESSION['user_email'] ?? 'admin@example.com';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentPageTitle); ?> - <?php echo htmlspecialchars($appName); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>css/admin_style.css"> 
    <style>
        /* CSS Dasar untuk Layout Admin - Pindahkan ke admin_style.css nanti */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6; /* Warna latar belakang lebih netral */
            display: flex;
            height: 100vh;
            overflow: hidden; /* Mencegah scroll di body utama */
        }

        .admin-sidebar {
            width: 250px;
            background-color: #2c3e50; /* Warna gelap untuk sidebar */
            color: #ecf0f1;
            padding: 20px 0;
            height: 100vh;
            position: fixed; /* Fixed sidebar */
            left: 0;
            top: 0;
            overflow-y: auto; /* Scroll jika menu banyak */
            transition: width 0.3s ease;
        }
        .admin-sidebar .sidebar-header {
            padding: 0 20px 20px 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }
        .admin-sidebar .sidebar-header h3 {
            margin: 0;
            font-size: 1.5em;
            color: #ffffff;
        }
         .admin-sidebar .sidebar-header h3 a {
            color: #ffffff;
            text-decoration: none;
        }

        .admin-sidebar ul.admin-menu {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .admin-sidebar ul.admin-menu li a {
            display: block;
            padding: 12px 20px;
            color: #bdc3c7; /* Warna link menu */
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease, padding-left 0.2s ease;
            font-size: 0.95em;
            border-left: 3px solid transparent;
        }
        .admin-sidebar ul.admin-menu li a:hover,
        .admin-sidebar ul.admin-menu li.active a {
            background-color: #34495e; /* Warna hover/aktif */
            color: #ffffff;
            border-left: 3px solid #3498db; /* Aksen biru */
            padding-left: 25px;
        }
        /* .admin-sidebar ul.admin-menu li a i { margin-right: 10px; } */ /* Untuk ikon */

        .admin-main-content {
            margin-left: 250px; /* Sesuaikan dengan lebar sidebar */
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .admin-top-header {
            background-color: #ffffff;
            padding: 0 25px;
            height: 60px; /* Tinggi header */
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            flex-shrink: 0; /* Mencegah header menyusut */
        }
        .admin-top-header .page-title {
            font-size: 1.4em;
            font-weight: 600;
            color: #333;
        }
        .admin-top-header .profile-dropdown {
            position: relative;
        }
        .admin-top-header .profile-dropdown .profile-trigger {
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
         .admin-top-header .profile-dropdown .profile-trigger:hover {
            background-color: #f0f0f0;
        }
        .admin-top-header .profile-dropdown .profile-trigger img { /* Jika pakai gambar profil */
            width: 32px; height: 32px; border-radius: 50%; margin-right: 8px;
        }
        .admin-top-header .profile-dropdown .profile-trigger span { font-weight: 500; }
        .admin-top-header .profile-dropdown .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%; /* Muncul di bawah trigger */
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 200px;
            z-index: 100;
            margin-top: 5px; /* Jarak sedikit dari header */
        }
        .admin-top-header .profile-dropdown .dropdown-menu.show { display: block; }
        .admin-top-header .profile-dropdown .dropdown-menu .dropdown-header {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        .admin-top-header .profile-dropdown .dropdown-menu .dropdown-header strong { display: block; }
        .admin-top-header .profile-dropdown .dropdown-menu .dropdown-header small { color: #777; }
        .admin-top-header .profile-dropdown .dropdown-menu a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-size: 0.9em;
        }
        .admin-top-header .profile-dropdown .dropdown-menu a:hover { background-color: #f5f5f5; }
        .admin-top-header .profile-dropdown .dropdown-menu .dropdown-divider {
            height: 1px; background-color: #eee; margin: 5px 0;
        }


        .admin-content-wrapper {
            flex-grow: 1;
            padding: 25px;
            overflow-y: auto; /* Konten utama bisa di-scroll */
            background-color: #ffffff; /* Latar konten */
        }
        
        /* Flash Message (jika ingin styling beda untuk admin) */
        .flash-message-admin { padding: 1rem; margin-bottom: 1.5rem; border: 1px solid transparent; border-radius: 0.25rem; font-size: 0.95em; }
        .flash-message-admin.success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        /* ... (style flash message lainnya) ... */

        /* Chart styling (placeholder) */
        .chart-container {
            width: 100%;
            max-width: 600px; /* Atau sesuaikan */
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h3><a href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>"><?php echo htmlspecialchars(explode(' ', $appName)[0] ?? 'Admin'); ?> Panel</a></h3>
        </div>
        <ul class="admin-menu">
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">Dashboard</a>
            </li>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/kos') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($baseUrl . 'admin/kos'); ?>">Kelola Kos</a>
            </li>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($baseUrl . 'admin/users'); ?>">Kelola Pengguna</a>
            </li>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/bookings') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/bookingConfirm') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/bookingReject') !== false) ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($baseUrl . 'admin/bookings'); ?>">Kelola Pemesanan</a>
            </li>
            <li><a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>">Logout</a></li>
        </ul>
    </aside>

    <div class="admin-main-content">
        <header class="admin-top-header">
            <div class="page-title">
                <?php echo htmlspecialchars($currentPageTitle); ?>
            </div>
            <div class="profile-dropdown">
                <div class="profile-trigger" id="profileDropdownTrigger">
                    <span><?php echo htmlspecialchars($adminNama); ?> &#9662;</span> </div>
                <div class="dropdown-menu" id="profileDropdownMenu">
                    <div class="dropdown-header">
                        <strong><?php echo htmlspecialchars($adminNama); ?></strong>
                        <small><?php echo htmlspecialchars($adminEmail); ?></small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="<?php echo htmlspecialchars($baseUrl . 'user/editProfile'); // Atau admin/profileEdit ?>">Edit Profil</a>
                    <a href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>">Logout</a>
                </div>
            </div>
        </header>

        <main class="admin-content-wrapper">
            <?php
            // Menampilkan pesan flash
            if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) {
                $flash = $_SESSION['flash_message'];
                // Gunakan class admin jika ada, atau class flash-message standar
                echo '<div class="flash-message-admin ' . htmlspecialchars($flash['type'] ?? 'info') . '">';
                echo $flash['message']; 
                echo '</div>';
                unset($_SESSION['flash_message']); 
            }
            ?>

            <?php
            // Ini adalah tempat konten view spesifik akan dimuat
            if (isset($contentView) && file_exists($appConfig['VIEWS_PATH'] . $contentView . '.php')) {
                require_once $appConfig['VIEWS_PATH'] . $contentView . '.php';
            } elseif (isset($contentView)) {
                echo "<p style='color:red;'>Error: File view '{$contentView}' tidak ditemukan.</p>";
            } else {
                echo "<p>Selamat datang di Admin Panel. Silakan pilih menu di samping.</p>"; // Default jika tidak ada contentView
            }
            ?>
        </main>
    </div>

<script>
    // JavaScript untuk dropdown profil
    document.addEventListener('DOMContentLoaded', function() {
        const profileTrigger = document.getElementById('profileDropdownTrigger');
        const profileMenu = document.getElementById('profileDropdownMenu');

        if (profileTrigger && profileMenu) {
            profileTrigger.addEventListener('click', function(event) {
                event.stopPropagation(); // Mencegah window click langsung menutup
                profileMenu.classList.toggle('show');
            });

            // Menutup dropdown jika klik di luar
            window.addEventListener('click', function(event) {
                if (profileMenu.classList.contains('show') && !profileTrigger.contains(event.target)) {
                    profileMenu.classList.remove('show');
                }
            });
        }

        // (Opsional) Tambahkan JavaScript untuk toggle sidebar jika ingin bisa diciutkan
    });
</script>
<?php 
// Chat UI hanya dimuat jika $appConfig ada, dan user login (sudah dihandle di chat_ui.php)
// Jika ingin chat UI di admin panel, pastikan $appConfig tersedia di sini
if (isset($appConfig) && isset($_SESSION['user_id'])) {
    $chatUiPath = $appConfig['INCLUDES_PATH'] . 'chat_ui.php';
    if (file_exists($chatUiPath)) {
        require_once $chatUiPath;
    }
}
?>
</body>
</html>