<?php
// File: nama_proyek_kos/views/admin/kos/form.php

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
$color_secondary_text_heading = $palette_medium_blue;
$color_text_on_dark = $palette_white;
$color_text_on_light = '#333333';
$color_border_subtle = $palette_medium_light_blue;
$color_border_input = $palette_medium_light_blue; // Border for form inputs
$color_primary_action = $palette_dark_blue;
$color_secondary_action_link = $palette_medium_blue; // For cancel links
$color_danger_link = '#dc3545'; // For delete links

// Assuming $pageTitle, $formAction, $mode, $kos, $appConfig are passed
$isEditMode = ($mode === 'edit' && !empty($kos));
$gambarKos = $isEditMode ? ($kos['gambar_kos'] ?? []) : [];

// Default values for $appConfig if not set (for standalone testing)
$appConfig = $appConfig ?? [
    'BASE_URL' => './',
    'UPLOADS_URL_PATH' => 'uploads/' // Example path
];
$kos = $kos ?? []; // Ensure $kos is an array

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Form Kos'); ?> - Admin Panel</title>
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
            max-width: 900px; /* Adjusted for form layout */
            margin: 30px auto;
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
            margin-bottom: 30px;
            font-size: 1.8em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: <?php echo $palette_dark_blue; ?>;
            font-size: 0.95em;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid <?php echo $color_border_input; ?>;
            border-radius: 5px;
            box-sizing: border-box; /* Important for width 100% and padding */
            font-size: 1em;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .form-control:focus {
            border-color: <?php echo $palette_medium_blue; ?>;
            box-shadow: 0 0 0 0.2rem <?php echo $palette_medium_light_blue; ?>40; /* Subtle focus ring */
            outline: none;
        }
        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }
        .file-upload-section {
            margin-bottom: 25px;
            padding: 20px;
            border: 1px dashed <?php echo $palette_medium_blue; ?>;
            border-radius: 6px;
            background-color: <?php echo $palette_light_blue_gray; ?>20; /* Very light background */
        }
        .file-upload-section label {
            font-weight: bold;
            color: <?php echo $color_primary_text_heading; ?>;
        }
        .file-upload-section small {
            display: block;
            margin-top: 8px;
            color: <?php echo $palette_medium_blue; ?>;
            font-size: 0.85em;
        }
        .existing-images-section h4 {
            color: <?php echo $color_secondary_text_heading; ?>;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.25em;
        }
        .image-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .image-preview-item {
            border: 1px solid <?php echo $color_border_subtle; ?>;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            width: 160px; /* Fixed width for consistency */
            background-color: <?php echo $palette_white; ?>;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .image-preview-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            margin-bottom: 8px;
            border-radius: 4px;
        }
        .image-preview-item p.filename {
            font-size: 0.8em;
            margin-bottom: 8px;
            word-break: break-all;
            color: <?php echo $color_text_on_light; ?>;
            height: 3.2em; /* Allow for 2 lines of text */
            overflow: hidden;
        }
        .delete-image-link {
            color: <?php echo $color_danger_link; ?>;
            font-size: 0.85em;
            text-decoration: none;
            font-weight: bold;
        }
        .delete-image-link:hover {
            text-decoration: underline;
        }
        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid <?php echo $color_border_subtle; ?>;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .btn {
            padding: 12px 25px;
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
        .btn-primary {
            background-color: <?php echo $color_primary_action; ?>;
            color: <?php echo $color_text_on_dark; ?>;
        }
        .btn-link-secondary {
            color: <?php echo $color_secondary_action_link; ?>;
            text-decoration: none;
            font-weight: bold;
            padding: 10px; /* Make it easier to click */
        }
        .btn-link-secondary:hover {
            text-decoration: underline;
            color: <?php echo $palette_dark_blue; ?>;
        }

        /* --- New Modal Styles for Delete Confirmation --- */
        .modal {
            display: none; /* Changed to none, so it's hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            /* The following two lines will still apply when JS changes display to 'flex' */
            align-items: center; /* Center vertically */
            justify-content: center; /* Center horizontally */
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 25px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 90%;
            max-width: 400px; /* Max width for the modal */
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            text-align: center;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content h3 {
            margin-top: 0;
            color: <?php echo $color_primary_text_heading; ?>;
            font-size: 1.3em;
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .modal-btn {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            border: none;
            transition: opacity 0.2s ease;
        }

        .modal-btn:hover {
            opacity: 0.85;
        }

        .modal-btn-confirm {
            background-color: #dc3545; /* Red for confirm delete */
            color: white;
        }

        .modal-btn-cancel {
            background-color: #6c757d; /* Gray for cancel */
            color: white;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2 class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'Form Data Kos'); ?></h2>

        <form action="<?php echo htmlspecialchars($formAction ?? '#'); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_kos">Nama Kos:</label>
                <input type="text" id="nama_kos" name="nama_kos" class="form-control" value="<?php echo htmlspecialchars($kos['nama_kos'] ?? $_POST['nama_kos'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat Lengkap:</label>
                <textarea id="alamat" name="alamat" rows="3" class="form-control" required><?php echo htmlspecialchars($kos['alamat'] ?? $_POST['alamat'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" rows="5" class="form-control"><?php echo htmlspecialchars($kos['deskripsi'] ?? $_POST['deskripsi'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="harga_per_bulan">Harga per Bulan (Rp):</label>
                <input type="number" step="1000" id="harga_per_bulan" name="harga_per_bulan" class="form-control" value="<?php echo htmlspecialchars($kos['harga_per_bulan'] ?? $_POST['harga_per_bulan'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="fasilitas_kos">Fasilitas (pisahkan dengan koma):</label>
                <input type="text" id="fasilitas_kos" name="fasilitas_kos" class="form-control" value="<?php echo htmlspecialchars($kos['fasilitas_kos'] ?? $_POST['fasilitas_kos'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="jumlah_kamar_total">Jumlah Kamar Total:</label>
                <input type="number" min="0" id="jumlah_kamar_total" name="jumlah_kamar_total" class="form-control" value="<?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? $_POST['jumlah_kamar_total'] ?? '1'); ?>" required>
            </div>

            <?php if ($isEditMode): ?>
            <div class="form-group">
                <label for="jumlah_kamar_tersedia">Jumlah Kamar Tersedia:</label>
                <input type="number" min="0" id="jumlah_kamar_tersedia" name="jumlah_kamar_tersedia" class="form-control" value="<?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? $_POST['jumlah_kamar_tersedia'] ?? '0'); ?>" required>
            </div>
            <div class="form-group">
                <label for="status_kos">Status Kos:</label>
                <select id="status_kos" name="status_kos" class="form-control">
                    <option value="available" <?php echo (($kos['status_kos'] ?? '') === 'available') ? 'selected' : ''; ?>>Available</option>
                    <option value="booked" <?php echo (($kos['status_kos'] ?? '') === 'booked') ? 'selected' : ''; ?>>Fully Booked</option>
                    <option value="maintenance" <?php echo (($kos['status_kos'] ?? '') === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="file-upload-section">
                <label for="gambar_kos_baru">Upload Gambar Baru (Bisa lebih dari satu):</label>
                <input type="file" id="gambar_kos_baru" name="gambar_kos_baru[]" class="form-control" multiple accept="image/jpeg, image/png, image/gif, image/webp" style="padding: 5px;"> <small>Tipe file yang diizinkan: JPG, PNG, GIF, WEBP. Maksimal 5MB per file.</small>
            </div>

            <?php if ($isEditMode && !empty($gambarKos)): ?>
            <div class="existing-images-section">
                <h4>Gambar yang Sudah Ada:</h4>
                <div class="image-grid">
                    <?php foreach ($gambarKos as $gambar): ?>
                        <div class="image-preview-item">
                            <img src="<?php echo htmlspecialchars(($appConfig['UPLOADS_URL_PATH'] ?? 'uploads/') . ($gambar['path'] ?? 'placeholder.jpg')); ?>"
                                 alt="<?php echo htmlspecialchars($gambar['nama_file'] ?? 'Gambar Kos'); ?>">
                            <p class="filename" title="<?php echo htmlspecialchars($gambar['nama_file'] ?? 'Nama File Tidak Tersedia'); ?>"><?php echo htmlspecialchars($gambar['nama_file'] ?? 'N/A'); ?></p>
                            <a href="#"
                               data-delete-url="<?php echo htmlspecialchars(($appConfig['BASE_URL'] ?? './') . 'admin/kosDeleteGambar/' . ($gambar['id'] ?? '0') . '/' . ($kos['id'] ?? '0')); ?>"
                               class="delete-image-link open-delete-modal">Hapus Gambar</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php elseif ($isEditMode): ?>
            <div class="form-group">
                 <p>Belum ada gambar untuk kos ini.</p>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?php echo $isEditMode ? 'Simpan Perubahan Kos' : 'Tambah Kos Baru'; ?>
                </button>
                <a href="<?php echo htmlspecialchars(($appConfig['BASE_URL'] ?? './') . 'admin/kos'); ?>" class="btn-link-secondary">Batal</a>
            </div>
        </form>
    </div>

    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Hapus Gambar</h3>
            <p>Anda yakin ingin menghapus gambar ini?</p>
            <div class="modal-buttons">
                <button id="confirmDeleteBtn" class="modal-btn modal-btn-confirm">Hapus</button>
                <button id="cancelDeleteBtn" class="modal-btn modal-btn-cancel">Batal</button>
            </div>
        </div>
    </div>

    <script>
        // --- JavaScript for Delete Confirmation Modal ---
        const deleteModal = document.getElementById('deleteConfirmModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const deleteLinks = document.querySelectorAll('.open-delete-modal');

        let currentDeleteUrl = ''; // To store the URL for the clicked delete link

        deleteLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default link behavior
                currentDeleteUrl = this.dataset.deleteUrl; // Get the URL from data-delete-url
                deleteModal.style.display = 'flex'; // Show the modal
            });
        });

        // Handle confirm button click
        confirmDeleteBtn.addEventListener('click', function() {
            if (currentDeleteUrl) {
                window.location.href = currentDeleteUrl; // Redirect to the delete URL
            }
        });

        // Handle cancel button click and outside modal click
        cancelDeleteBtn.addEventListener('click', function() {
            deleteModal.style.display = 'none'; // Hide the modal
        });

        deleteModal.addEventListener('click', function(event) {
            // If clicked directly on the modal background (not on the content inside)
            if (event.target === deleteModal) {
                deleteModal.style.display = 'none'; // Hide the modal
            }
        });
    </script>
</body>
</html>