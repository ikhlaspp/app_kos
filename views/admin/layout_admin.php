<?php
// File: views/admin/layout_admin.php

// Fallback for appConfig values if not provided by the controller.
// Define default heights for admin header/footer for min-height calculation.
if (!isset($appConfig) || !is_array($appConfig)) {
    $appConfig = [
        'APP_NAME' => 'Aplikasi Default',
        'BASE_URL' => '/',
        'ASSETS_URL' => '/assets/',
        'SESSION_NAME' => 'PHPSESSID_APP',
        'ADMIN_HEADER_HEIGHT' => '0px', // Set to 0px as navbar is removed
        'ADMIN_FOOTER_HEIGHT' => '60px'
    ];
}
if (!isset($pageTitle)) {
    $pageTitle = $appConfig['APP_NAME'] ?? 'Admin Panel';
}
if (!isset($contentView)) {
    $contentView = 'admin/dashboard';
}

$assetsUrl = $appConfig['ASSETS_URL'] ?? './assets/';

// Custom Color Palette for consistency (copied from your main theme)
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#E9F1F7';
$paletteMediumBlue = '#4A90E2';
$paletteDarkBlue = '#1A3A5B';
$paletteTextPrimary = '#0D2A57';
$paletteTextSecondary = '#555555';
$paletteAccentBlue = '#6A9EFF';

// Status Colors for alerts/buttons (reusing your defined status colors)
$statusSuccess = '#28a745';
$statusError = '#dc3545';
$statusInfo = '#17a2b8';
$statusWarning = '#ffc107';

// Helper function to adjust color brightness (used for borders/accents)
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Admin Panel</title>

    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>vendors/feather/feather.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>vendors/mdi/css/materialdesignicons.min.css">
    
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>css/style.css"> 
    
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($assetsUrl); ?>images/favicon.png" />

    <style>
        /* General Body & Root Overrides */
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: <?php echo htmlspecialchars($paletteLightBlue); ?> !important;
            color: <?php echo htmlspecialchars($paletteTextPrimary); ?> !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        /* Text Color Consistency for Headings & Paragraphs */
        h1, h2, h3, h4, h5, h6 {
            color: <?php echo htmlspecialchars($paletteDarkBlue); ?> !important;
        }
        p {
            color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
        }
        a {
            color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
            text-decoration: none;
        }
        a:hover {
            color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
            text-decoration: underline;
        }

        /* Layout Main Content Wrapper Height (adjusts dynamically) */
        .content-wrapper {
            min-height: calc(100vh - <?php echo htmlspecialchars($appConfig['ADMIN_FOOTER_HEIGHT']); ?>); /* Only subtract footer height now */
            background-color: <?php echo htmlspecialchars($paletteLightBlue); ?>;
        }

        /* Aggressively collapse any space from .navbar related elements */
        .navbar {
            display: none !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: 0 !important;
        }
        .navbar-brand-wrapper, .navbar-menu-wrapper {
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: 0 !important;
        }

        /* Page Body Wrapper - ensure no top padding/margin */
        .page-body-wrapper {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }


        /* Sidebar Navigation - AGGRESSIVE OVERRIDES */
        .sidebar {
            background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?> !important;
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
            padding-top: 20px !important;
            top: 0 !important;
            position: fixed !important;
            height: 100vh !important;
            z-index: 1000;
        }
        .sidebar .nav-item .nav-link {
            color: <?php echo htmlspecialchars($paletteLightBlue); ?> !important;
            background-color: transparent !important;
        }
        .sidebar .nav-item .nav-link:hover {
            background-color: <?php echo htmlspecialchars(adjustBrightness($paletteMediumBlue, 10)); ?> !important;
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        }
        .sidebar .nav-item.active {
            background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?> !important;
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        }
        .sidebar .nav-item.active .nav-link {
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
            background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?> !important;
        }
        .sidebar .menu-title {
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        }
        .sidebar .nav-item .menu-icon {
            color: <?php echo htmlspecialchars($paletteAccentBlue); ?> !important;
        }

        /* Main Panel - FIX EMPTY AREA AND PUSH CONTENT FOR SIDEBAR */
        .main-panel {
            padding-top: 30px !important; /* Explicitly set desired top padding for main content */
            margin-top: 0 !important; /* Ensure no residual margin-top from template */
            margin-left: 260px !important; /* Push content to make space for fixed sidebar */
            width: calc(100% - 260px) !important; /* Ensure content panel takes remaining width */
            transition: all 0.2s ease;
        }


        /* Buttons (General Bootstrap Overrides) */
        .btn-primary {
            background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?> !important;
            border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?> !important;
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?> !important;
            border-color: <?php echo htmlspecialchars($paletteDarkBlue); ?> !important;
        }
        .btn-success {
            background-color: <?php echo htmlspecialchars($statusSuccess); ?> !important;
            border-color: <?php echo htmlspecialchars($statusSuccess); ?> !important;
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        }
        .btn-success:hover {
            background-color: <?php echo htmlspecialchars(adjustBrightness($statusSuccess, -20)); ?> !important;
            border-color: <?php echo htmlspecialchars(adjustBrightness($statusSuccess, -20)); ?> !important;
        }
        .btn-danger {
            background-color: <?php echo htmlspecialchars($statusError); ?> !important;
            border-color: <?php echo htmlspecialchars($statusError); ?> !important;
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        }
        .btn-danger:hover {
            background-color: <?php echo htmlspecialchars(adjustBrightness($statusError, -20)); ?> !important;
            border-color: <?php echo htmlspecialchars(adjustBrightness($statusError, -20)); ?> !important;
        }
        .btn-warning {
            background-color: <?php echo htmlspecialchars($statusWarning); ?> !important;
            border-color: <?php echo htmlspecialchars($statusWarning); ?> !important;
            color: <?php echo htmlspecialchars(adjustBrightness($statusWarning, -100)); ?> !important;
        }
        .btn-warning:hover {
            background-color: <?php echo htmlspecialchars(adjustBrightness($statusWarning, -20)); ?> !important;
            border-color: <?php echo htmlspecialchars(adjustBrightness($statusWarning, -20)); ?> !important;
        }
        .btn-info {
            background-color: <?php echo htmlspecialchars($statusInfo); ?> !important;
            border-color: <?php echo htmlspecialchars($statusInfo); ?> !important;
            color: <?php echo htmlspecialchars($paletteWhite); ?> !important;
        }
        .btn-info:hover {
            background-color: <?php echo htmlspecialchars(adjustBrightness($statusInfo, -20)); ?> !important;
            border-color: <?php echo htmlspecialchars(adjustBrightness($statusInfo, -20)); ?> !important;
        }

        /* Flash Message Alerts */
        .alert {
            border-radius: 0.25rem;
            font-size: 0.95em;
            font-weight: 500;
        }
        .alert-success { background-color: <?php echo htmlspecialchars(adjustBrightness($statusSuccess, 100)); ?> !important; border-color: <?php echo htmlspecialchars($statusSuccess); ?> !important; color: <?php echo htmlspecialchars(adjustBrightness($statusSuccess, -100)); ?> !important; }
        .alert-danger { background-color: <?php echo htmlspecialchars(adjustBrightness($statusError, 100)); ?> !important; border-color: <?php echo htmlspecialchars($statusError); ?> !important; color: <?php echo htmlspecialchars(adjustBrightness($statusError, -100)); ?> !important; }
        .alert-info { background-color: <?php echo htmlspecialchars(adjustBrightness($statusInfo, 100)); ?> !important; border-color: <?php echo htmlspecialchars($statusInfo); ?> !important; color: <?php echo htmlspecialchars(adjustBrightness($statusInfo, -100)); ?> !important; }
        .alert-warning { background-color: <?php echo htmlspecialchars(adjustBrightness($statusWarning, 100)); ?> !important; border-color: <?php echo htmlspecialchars($statusWarning); ?> !important; color: <?php echo htmlspecialchars(adjustBrightness($statusWarning, -100)); ?> !important; }

        /* General Card Overrides (Careful: Dashboard cards are excluded from general theming) */
        .card {
            border-color: <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -15)); ?>;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            background-color: <?php echo htmlspecialchars($paletteWhite); ?>;
        }
        .card-header {
            background-color: <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -5)); ?>;
            color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
            font-weight: 600;
            border-bottom: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -15)); ?>;
        }

    </style>
</head>
<body>
    <div class="container-scroller">
        <?php // The template usually has a top navbar partial here, but it's removed as requested. ?>
        <?php // require_once $appConfig['VIEWS_PATH'] . 'admin/partials/_navbar.php'; ?>

        <div class="container-fluid page-body-wrapper">
            <?php require_once $appConfig['VIEWS_PATH'] . 'admin/partials/_sidebar.php'; ?>
            
            <div class="main-panel">
                <div class="content-wrapper">
                    <?php
                    // Display flash messages (Bootstrap alerts)
                    if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) {
                        $flash = $_SESSION['flash_message'];
                        echo '<div class="alert alert-' . htmlspecialchars($flash['type'] ?? 'info') . ' alert-dismissible fade show" role="alert">';
                        echo htmlspecialchars($flash['message']); 
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        unset($_SESSION['flash_message']); // Clear flash message after display
                    }
                    ?>

                    <?php
                    // Load the specific content view for the current page
                    $viewFilePath = $appConfig['VIEWS_PATH'] . $contentView . '.php';
                    if (isset($contentView) && file_exists($viewFilePath)) {
                        // Variables from $data passed from controller are already extracted by loadAdminView
                        require_once $viewFilePath;
                    } elseif (isset($contentView)) {
                        echo "<div class='alert alert-danger'>Error: File view '" . htmlspecialchars($contentView) . "' tidak ditemukan di '" . htmlspecialchars($viewFilePath) . "'.</div>";
                    } else {
                        echo "<p>Selamat datang. Konten tidak ditentukan.</p>";
                    }
                    ?>
                </div> <?php // The template usually has a footer partial here, e.g.: ?>
                <?php require_once $appConfig['VIEWS_PATH'] . 'admin/partials/_footer.php'; ?>
            </div> </div> </div> <script src="<?php echo htmlspecialchars($assetsUrl); ?>vendors/js/vendor.bundle.base.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>vendors/chart.js/chart.umd.js"></script>
    
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>js/off-canvas.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>js/template.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>js/jquery.cookie.js" type="text/javascript"></script>
    
    <?php 
    // Include chat UI if user is logged in and chat file exists
    if (isset($_SESSION['user_id']) && isset($appConfig) && is_array($appConfig)) { 
        $chatUiPath = ($appConfig['INCLUDES_PATH'] ?? 'includes/') . 'chat_ui.php'; 
        if (file_exists($chatUiPath)) {
            require_once $chatUiPath; 
        } else {
            error_log("Warning: File " . $chatUiPath . " not found. Chat UI may not load.");
        }
    }
    ?>
</body>
</html>