<?php
// File: views/admin/partials/_navbar.php
// Variabel $appConfig, $adminNama, $adminEmail diharapkan sudah ada dari layout_admin.php
$baseUrl = $appConfig['BASE_URL'] ?? './';
$assetsUrl = $appConfig['ASSETS_URL'] ?? './assets/';
$adminNama = $_SESSION['user_nama'] ?? 'Admin';
// $adminAvatar = $_SESSION['user_avatar'] ?? $assetsUrl . 'images/faces/face28.jpg'; // Contoh jika ada avatar
$adminAvatar = $assetsUrl . 'images/faces/face28.jpg'; // Default dari template
?>
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo me-5" href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>"><img src="<?php echo htmlspecialchars($assetsUrl); ?>images/logo.svg" class="me-2" alt="logo" /></a>
        <a class="navbar-brand brand-logo-mini" href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>"><img src="<?php echo htmlspecialchars($assetsUrl); ?>images/logo-mini.svg" alt="logo" /></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
            <li class="nav-item nav-search d-none d-lg-block">
                <div class="input-group">
                    <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                        <span class="input-group-text" id="search">
                            <i class="icon-search"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
                </div>
            </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
                    <i class="icon-bell mx-0"></i>
                    <span class="count"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Notifikasi</p>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail"><div class="preview-icon bg-success"><i class="ti-info-alt mx-0"></i></div></div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">Booking Baru</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">Baru saja</p>
                        </div>
                    </a>
                    </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                    <img src="<?php echo htmlspecialchars($adminAvatar); ?>" alt="profile" />
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="<?php echo htmlspecialchars($baseUrl . 'user/editProfile'); // atau admin/profileEdit ?>">
                        <i class="ti-settings text-primary"></i> Edit Profil
                    </a>
                    <a class="dropdown-item" href="<?php echo htmlspecialchars($baseUrl . 'auth/logout'); ?>">
                        <i class="ti-power-off text-primary"></i> Logout
                    </a>
                </div>
            </li>
            </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>