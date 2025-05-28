<?php
// --- Color Palette Definition (Same as previous examples) ---
$palette_white = '#FFFFFF';
$palette_light_blue_gray = '#EBF0F5';
$palette_medium_light_blue = '#D2DDE8';
$palette_medium_blue = '#6B8EB2';
$palette_dark_blue = '#2A4365';

// --- Theme Colors Based on Palette ---
$color_bg_page = $palette_light_blue_gray;
$color_bg_container = $palette_white;
$color_primary_text_heading = $palette_dark_blue;
$color_text_on_dark = $palette_white;
$color_text_on_light = '#333333';
$color_border_subtle = $palette_medium_light_blue;
$color_secondary_action = $palette_medium_blue; // For cancel/secondary buttons

// --- Standard UX Colors ---
$color_danger = '#dc3545'; // Red for delete/danger actions
$color_warning_text = '#856404'; // Dark yellow/brown for text on warning
$color_warning_bg_subtle = '#fff3cd'; // Light yellow for warning box background
$color_warning_border = '#ffeeba'; // Slightly darker yellow for warning box border


// Assuming $pageTitle, $appConfig, $kos, $formAction are passed to this view file
// $appConfig = ['BASE_URL' => 'http://localhost/yourproject/'];
// $kos = ['id' => 'K001', 'nama_kos' => 'Kos Melati Indah', 'alamat' => 'Jl. Mawar No. 10'];
// $formAction = $appConfig['BASE_URL'] . 'admin/kosDoDelete/' . $kos['id'];
// $pageTitle = 'Konfirmasi Penghapusan Kos: ' . $kos['nama_kos'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Konfirmasi Penghapusan'); ?> - Admin Panel</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: <?php echo $color_bg_page; ?>;
            color: <?php echo $color_text_on_light; ?>;
            line-height: 1.6;
        }
        .admin-container {
            max-width: 800px; /* Suitable for confirmation pages */
            margin: 40px auto;
            padding: 30px;
            background-color: <?php echo $color_bg_container; ?>;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .page-title {
            color: <?php echo $color_primary_text_heading; ?>;
            border-bottom: 2px solid <?php echo $color_border_subtle; ?>;
            padding-bottom: 15px;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.7em;
        }
        .confirmation-box {
            padding: 20px;
            border: 1px solid <?php echo $color_warning_border; ?>;
            background-color: <?php echo $color_warning_bg_subtle; ?>;
            color: <?php echo $color_warning_text; ?>;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        .confirmation-box p {
            margin: 0 0 10px 0;
            font-size: 0.95em;
        }
        .confirmation-box p strong {
            color: <?php echo $palette_dark_blue; ?>; /* Darker for emphasis */
        }
        .confirmation-box .attention {
            font-weight: bold;
            margin-top: 15px;
            color: <?php echo $color_danger; ?>; /* Emphasize the warning about cascading deletes */
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 15px; /* Space between buttons */
        }
        .btn {
            padding: 12px 25px;
            color: <?php echo $color_text_on_dark; ?>;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            border: none;
            font-size: 1em;
            transition: opacity 0.2s ease-in-out;
        }
        .btn:hover {
            opacity: 0.85;
        }
        .btn-danger {
            background-color: <?php echo $color_danger; ?>;
        }
        .btn-secondary {
            background-color: <?php echo $color_secondary_action; ?>;
            color: <?php echo $color_text_on_dark; ?>;
        }
        .btn-secondary:hover {
             background-color: <?php echo $palette_dark_blue; ?>; /* Darken secondary on hover */
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2 class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'Konfirmasi Penghapusan'); ?></h2>

        <div class="confirmation-box">
            <p>Anda yakin ingin menghapus data kos berikut secara permanen?</p>
            <p><strong>ID Kos:</strong> <?php echo htmlspecialchars($kos['id'] ?? 'N/A'); ?></p>
            <p><strong>Nama Kos:</strong> <?php echo htmlspecialchars($kos['nama_kos'] ?? 'N/A'); ?></p>
            <p><strong>Alamat:</strong> <?php echo htmlspecialchars($kos['alamat'] ?? 'N/A'); ?></p>
            <p class="attention">Perhatian: Tindakan ini tidak dapat diurungkan. Semua data terkait (misalnya booking) mungkin juga akan terhapus jika database diatur dengan ON DELETE CASCADE.</p>
        </div>

        <div class="action-buttons">
            <form action="<?php echo htmlspecialchars($formAction ?? '#'); ?>" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus Data Ini
                </button>
            </form>

            <a href="<?php echo htmlspecialchars(($appConfig['BASE_URL'] ?? '') . 'admin/kos'); ?>" class="btn btn-secondary">
                Batal
            </a>
        </div>
    </div>
</body>
</html>