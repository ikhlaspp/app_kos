<?php
// File: views/admin/layout_admin.php
if (!isset($appConfig) || !is_array($appConfig)) { /* Fallback $appConfig */ }
if (!isset($pageTitle)) { $pageTitle = $appConfig['APP_NAME'] ?? 'Admin Panel'; }
if (!isset($contentView)) { $contentView = 'admin/dashboard_summary'; }

$assetsUrl = $appConfig['ASSETS_URL'] ?? './assets/';

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
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>vendors/datatables.net-bs5/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($assetsUrl); ?>css/style.css"> <link rel="shortcut icon" href="<?php echo htmlspecialchars($assetsUrl); ?>images/favicon.png" />
    <style>
        /* Tambahan CSS custom jika ada, atau untuk override kecil */
        .content-wrapper {
            min-height: calc(100vh - <?php echo (isset($appConfig['ADMIN_HEADER_HEIGHT']) ? $appConfig['ADMIN_HEADER_HEIGHT'] : '60px'); ?> - <?php echo (isset($appConfig['ADMIN_FOOTER_HEIGHT']) ? $appConfig['ADMIN_FOOTER_HEIGHT'] : '50px'); ?>); /* Perkiraan tinggi header & footer */
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <?php // Hapus bagian Pro Banner jika tidak ingin ditampilkan
        /*
        <div class="row p-0 m-0 proBanner" id="proBanner">
            <div class="col-md-12 p-0 m-0">
                <div class="card-body card-body-padding px-3 d-flex align-items-center justify-content-between">
                    ... (Isi Pro Banner) ...
                </div>
            </div>
        </div>
        */
        ?>

        <?php require_once $appConfig['VIEWS_PATH'] . 'admin/partials/_navbar.php'; ?>
        
        <div class="container-fluid page-body-wrapper">
            <?php require_once $appConfig['VIEWS_PATH'] . 'admin/partials/_sidebar.php'; ?>
            
            <div class="main-panel">
                <div class="content-wrapper">
                    <?php
                    // Menampilkan pesan flash
                    if (isset($_SESSION['flash_message']) && is_array($_SESSION['flash_message'])) {
                        $flash = $_SESSION['flash_message'];
                        echo '<div class="alert alert-' . htmlspecialchars($flash['type'] ?? 'info') . ' alert-dismissible fade show" role="alert">';
                        echo $flash['message']; 
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        unset($_SESSION['flash_message']); 
                    }
                    ?>

                    <?php
                    // Memuat konten view spesifik
                    $viewFilePath = $appConfig['VIEWS_PATH'] . $contentView . '.php';
                    if (isset($contentView) && file_exists($viewFilePath)) {
                        // Variabel dari $data yang di-pass dari controller sudah di-extract oleh loadAdminView
                        require_once $viewFilePath;
                    } elseif (isset($contentView)) {
                        echo "<div class='alert alert-danger'>Error: File view '{$contentView}' tidak ditemukan di '{$viewFilePath}'.</div>";
                    } else {
                        echo "<p>Selamat datang. Konten tidak ditentukan.</p>";
                    }
                    ?>
                </div> <?php require_once $appConfig['VIEWS_PATH'] . 'admin/partials/_footer.php'; ?>
            </div> </div>   </div> <script src="<?php echo htmlspecialchars($assetsUrl); ?>vendors/js/vendor.bundle.base.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>vendors/chart.js/chart.umd.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>js/off-canvas.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>js/template.js"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>js/jquery.cookie.js" type="text/javascript"></script>
    <script src="<?php echo htmlspecialchars($assetsUrl); ?>js/dashboard.js"></script> <?php 
    // Chat UI bisa juga dimuat di sini jika admin juga menggunakan chat yang sama
    if (isset($appConfig) && isset($_SESSION['user_id'])) {
        $chatUiPath = ($appConfig['INCLUDES_PATH'] ?? 'includes/') . 'chat_ui.php';
        if (file_exists($chatUiPath)) {
            // require_once $chatUiPath; 
        }
    }
    ?>
</body>
</html>