<!-- <?php
// File: views/admin/partials/_navbar.php
// Variables $appConfig, $adminNama are expected from layout_admin.php context.

// Fallback for configuration variables
$baseUrl = $appConfig['BASE_URL'] ?? './';
$assetsUrl = $appConfig['ASSETS_URL'] ?? './assets/';
$adminNama = $_SESSION['user_nama'] ?? 'Admin';
$adminAvatar = $assetsUrl . 'images/faces/face28.jpg'; // Default avatar from template

// Custom Color Palette for consistency (copied from your main theme)
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#E9F1F7';
$paletteMediumBlue = '#4A90E2';
$paletteDarkBlue = '#1A3A5B';
$paletteTextPrimary = '#0D2A57';
$paletteTextSecondary = '#555555';
$paletteAccentBlue = '#6A9EFF';

// Status Colors for specific badges/icons (can be extended)
$statusSuccess = '#28a745';
$statusInfo = '#17a2b8';
?>

<style>
    /* Navbar Structure & Background */
    .navbar {
        background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?> !important;
        color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        border-bottom: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, -10)); ?>;
    }

    /* Brand Wrapper (Logo Area) */
    .navbar-brand-wrapper {
        background-color: <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, -15)); ?> !important; /* Slightly darker */
        border-right: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, -20)); ?>; /* Separator */
    }
    .navbar-brand {
        color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .navbar-brand:hover {
        color: <?php echo htmlspecialchars($paletteAccentBlue); ?> !important;
    }

    /* Toggler Icon (Hamburger Menu) */
    .navbar-toggler {
        color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        opacity: 0.8;
    }
    .navbar-toggler:hover {
        opacity: 1;
    }
    .navbar-toggler .icon-menu {
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
    }

    /* Search Input Group */
    .navbar-nav .nav-search .input-group .input-group-prepend .input-group-text {
        background-color: <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, -10)); ?>;
        border-color: <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, -20)); ?>;
        color: <?php echo htmlspecialchars($paletteAccentBlue); ?>;
    }
    .navbar-nav .nav-search .form-control {
        background-color: <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, -5)); ?>;
        border-color: <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, -20)); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
    }
    .navbar-nav .nav-search .form-control::placeholder {
        color: rgba(255,255,255,0.6);
    }
    .navbar-nav .nav-search .form-control:focus {
        background-color: <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, 0)); ?>;
        border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
    }


    /* Right Navbar Items (Icons, Profile) */
    .navbar-nav-right .nav-item .nav-link {
        color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }
    .navbar-nav-right .nav-item .nav-link:hover {
        opacity: 1;
    }
    .navbar-nav-right .nav-item .nav-link i {
        color: <?php echo htmlspecialchars($paletteWhite); ?> !important; /* Ensure icons are white */
    }

    /* Notification Dropdown */
    .navbar-dropdown {
        background-color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        border: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -15)); ?>;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 8px;
    }
    .navbar-dropdown .dropdown-header {
        background-color: <?php echo htmlspecialchars($paletteLightBlue); ?> !important;
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?> !important;
        font-weight: 600;
        border-bottom: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>;
        padding: 10px 15px;
    }
    .navbar-dropdown .dropdown-item {
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?> !important;
        padding: 10px 15px;
        transition: background-color 0.2s ease;
    }
    .navbar-dropdown .dropdown-item:hover {
        background-color: <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, 5)); ?> !important;
    }
    .navbar-dropdown .dropdown-item .preview-icon {
        background-color: <?php echo htmlspecialchars($paletteLightBlue); ?> !important; /* Default thumbnail background */
    }
    .navbar-dropdown .dropdown-item .preview-icon i {
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?> !important; /* Icon color */
    }
    .navbar-dropdown .dropdown-item .preview-thumbnail .bg-success { /* Specific notification status */
        background-color: <?php echo htmlspecialchars($statusSuccess); ?> !important;
    }
    .navbar-dropdown .dropdown-item .preview-thumbnail .bg-info {
        background-color: <?php echo htmlspecialchars($statusInfo); ?> !important;
    }
    .navbar-dropdown .dropdown-item .preview-item-content .preview-subject {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        font-weight: 500;
    }
    .navbar-dropdown .dropdown-item .preview-item-content .small-text {
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
    }


    /* Profile Dropdown */
    .nav-profile .dropdown-menu .dropdown-item {
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?> !important;
    }
    .nav-profile .dropdown-menu .dropdown-item i {
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?> !important; /* Icon color */
    }
    .nav-profile .dropdown-menu .dropdown-item:hover {
        background-color: <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, 5)); ?> !important;
    }


    <?php
    // Helper function to adjust color brightness (defined in layout, but needed here for consistency if partial used standalone)
    function adjustBrightness($hex, $steps) {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);
        $rgb = [];
        if (strlen($hex) == 3) {
            $rgb[0] = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $rgb[1] = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $rgb[2] = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $rgb[0] = hexdec(substr($hex, 0, 2));
            $rgb[1] = hexdec(substr($hex, 2, 2));
            $rgb[2] = hexdec(substr($hex, 4, 2));
        }
        $rgb[0] = max(0, min(255, $rgb[0] + $steps));
        $rgb[1] = max(0, min(255, $rgb[1] + $steps));
        $rgb[2] = max(0, min(255, $rgb[2] + $steps));
        return '#' . str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT)
                   . str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT)
                   . str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);
    }
    ?>
</style>

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-log me-5" href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">
            <img src="<?php echo htmlspecialchars($assetsUrl); ?>images/logo.svg" class="me-2" alt="logo" />
            <?php echo htmlspecialchars($appConfig['APP_NAME'] ?? 'Admin'); ?>
        </a>
        <a class="navbar-brand brand-log-mini" href="<?php echo htmlspecialchars($baseUrl . 'admin/dashboard'); ?>">
            <img src="<?php echo htmlspecialchars($assetsUrl); ?>images/logo-mini.svg" alt="logo" />
        </a>
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
                    <span class="count"></span> <?php // Notification count can be dynamically updated here ?>
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
                    <?php // Add more notification items here ?>
                </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                    <img src="<?php echo htmlspecialchars($adminAvatar); ?>" alt="profile" />
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="<?php echo htmlspecialchars($baseUrl . 'admin/profileEdit'); // Link to admin's own profile edit ?>">
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
</nav> -->