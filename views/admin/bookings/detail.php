<?php
// File: views/admin/bookings/detail.php
// Assumes $booking and $pageTitle are passed to this view file
// Assumes $appConfig is defined elsewhere, e.g., from layout_admin.php

// --- Color Palette Definition (re-defined here for standalone view's styling context) ---
$palette_white = '#FFFFFF';
$palette_light_blue_gray = '#EBF0F5'; // Approximation of the lightest shade (used for page background)
$palette_medium_light_blue = '#D2DDE8';// Approximation (used for subtle borders)
$palette_medium_blue = '#6B8EB2';     // Approximation (used for links, secondary actions)
$palette_dark_blue = '#2A4365';      // Approximation of the darkest shade (used for main headings, strong text)

// --- Theme Colors Based on Palette ---
$color_bg_page = $palette_light_blue_gray;
$color_bg_container = $palette_white;
$color_primary_text_heading = $palette_dark_blue;
$color_secondary_text_heading = $palette_medium_blue;
$color_primary_action = $palette_dark_blue; // Using dark blue for main buttons
$color_secondary_action = $palette_medium_blue; // Using medium blue for secondary buttons
$color_text_on_light = '#333333'; // General body text on light backgrounds
$color_border_subtle = $palette_medium_light_blue;

// --- Standard UX Colors (kept for clarity, can be mapped to palette if needed) ---
$color_success = '#28a745'; // Green
$color_warning = '#ffc107'; // Yellow
$color_text_on_warning = '#333333'; // Dark text for yellow background
$color_danger = '#dc3545';  // Red
$color_info = '#17a2b8';    // Teal/Light Blue
$color_muted = '#6c757d';   // Gray (for default status/placeholder)

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Detail Pemesanan'); ?> - Admin Panel</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; /* Maintain template font */
            background-color: <?php echo htmlspecialchars($color_bg_page); ?>;
            color: <?php echo htmlspecialchars($color_text_on_light); ?>;
            line-height: 1.6;
        }
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 25px;
            background-color: <?php echo htmlspecialchars($color_bg_container); ?>;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .page-title {
            color: <?php echo htmlspecialchars($color_primary_text_heading); ?>;
            border-bottom: 2px solid <?php echo htmlspecialchars($color_border_subtle); ?>;
            padding-bottom: 15px;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        .content-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }
        .content-section {
            flex: 1 1 320px; /* Adjust basis for responsiveness */
            background-color: <?php echo htmlspecialchars($color_bg_container); ?>;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid <?php echo htmlspecialchars($color_border_subtle); ?>;
            margin-bottom: 0; /* Gap handles spacing */
        }
        .content-section h4 {
            color: <?php echo htmlspecialchars($color_secondary_text_heading); ?>;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.25em;
            border-bottom: 1px solid <?php echo htmlspecialchars($color_border_subtle); ?>;
            padding-bottom: 10px;
        }
        .content-section p {
            margin-bottom: 10px;
            font-size: 0.95em;
        }
        .content-section p strong {
            color: <?php echo htmlspecialchars($palette_dark_blue); ?>; /* Darker blue for strong tags */
        }
        .status-badge {
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 15px; /* Pill shape */
            font-size: 0.85em;
            text-transform: capitalize;
            display: inline-block; /* Ensure it respects padding/margin */
        }
        /* Specific status badge colors */
        .status-badge.status-confirmed { background-color: <?php echo htmlspecialchars($color_success); ?>; color: <?php echo htmlspecialchars($palette_white); ?>; }
        .status-badge.status-pending { background-color: <?php echo htmlspecialchars($color_warning); ?>; color: <?php echo htmlspecialchars($color_text_on_warning); ?>; }
        .status-badge.status-rejected, .status-badge.status-canceled { background-color: <?php echo htmlspecialchars($color_danger); ?>; color: <?php echo htmlspecialchars($palette_white); ?>; }
        .status-badge.status-completed { background-color: <?php echo htmlspecialchars($color_info); ?>; color: <?php echo htmlspecialchars($palette_white); ?>; }
        .status-badge.status-default { background-color: <?php echo htmlspecialchars($color_muted); ?>; color: <?php echo htmlspecialchars($palette_white); ?>; }


        .actions-bar {
            margin-top: 35px;
            padding-top: 25px;
            border-top: 1px solid <?php echo htmlspecialchars($color_border_subtle); ?>;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .btn {
            padding: 10px 20px;
            color: <?php echo htmlspecialchars($palette_white); ?>; /* Buttons have white text on dark background */
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            border: none;
            font-size: 0.95em;
            transition: opacity 0.2s ease-in-out;
        }
        .btn:hover {
            opacity: 0.85;
        }
        .btn-primary {
            background-color: <?php echo htmlspecialchars($color_primary_action); ?>;
        }
        .btn-danger {
            background-color: <?php echo htmlspecialchars($color_danger); ?>;
        }
        .btn-secondary {
            background-color: <?php echo htmlspecialchars($color_secondary_action); ?>;
        }
        .link-danger {
            color: <?php echo htmlspecialchars($color_danger); ?>;
            text-decoration: none;
            font-weight: bold;
        }
        .link-danger:hover {
            text-decoration: underline;
        }
        .back-link-container {
            margin-top: 30px;
        }
        .alert-message { /* For display when booking is empty */
            padding: 15px;
            background-color: <?php echo htmlspecialchars(adjustBrightness($color_info, 100)); ?>;
            color: <?php echo htmlspecialchars(adjustBrightness($color_info, -100)); ?>;
            border: 1px solid <?php echo htmlspecialchars($color_info); ?>;
            border-radius: 5px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .admin-container {
                margin: 15px;
                padding: 15px;
            }
            .page-title {
                font-size: 1.5em;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            .content-grid {
                flex-direction: column;
                gap: 20px;
            }
            .content-section {
                flex-basis: auto;
                min-width: unset;
            }
            .actions-bar {
                flex-direction: column;
                gap: 10px;
            }
            .actions-bar .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2 class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'Detail Pemesanan'); ?></h2>

        <?php if (!empty($booking)): ?>
            <div class="content-grid">
                <section class="content-section">
                    <h4>Informasi Booking (#<?php echo htmlspecialchars($booking['booking_id_val']); ?>)</h4>
                    <p><strong>Tanggal Pemesanan:</strong> <?php echo htmlspecialchars(date('d M Y, H:i:s', strtotime($booking['tanggal_pemesanan']))); ?></p>
                    <p><strong>Tanggal Mulai Sewa:</strong> <?php echo htmlspecialchars(date('d M Y', strtotime($booking['tanggal_mulai']))); ?></p>
                    <p><strong>Tanggal Selesai Sewa:</strong> <?php echo htmlspecialchars(date('d M Y', strtotime($booking['tanggal_selesai']))); ?></p>
                    <p><strong>Durasi Sewa:</strong> <?php echo htmlspecialchars($booking['durasi_sewa']); ?></p>
                    <p><strong>Total Harga:</strong> Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></p>
                    <?php if (isset($booking['voucher_code'])): ?>
                        <p><strong>Voucher Digunakan:</strong> <?php echo htmlspecialchars($booking['voucher_code']); ?> (<?php echo htmlspecialchars($booking['voucher_name']); ?>)</p>
                    <?php endif; ?>
                    <p><strong>Status Pemesanan:</strong>
                        <?php
                            $statusPemesanan = $booking['status_pemesanan'] ?? 'pending';
                            $statusClass = 'status-default'; // Default badge class
                            switch ($statusPemesanan) {
                                case 'confirmed': $statusClass = 'status-confirmed'; break;
                                case 'pending': $statusClass = 'status-pending'; break;
                                case 'rejected': case 'canceled': $statusClass = 'status-rejected'; break;
                                case 'completed': $statusClass = 'status-completed'; break;
                            }
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars(ucfirst($statusPemesanan)); ?>
                        </span>
                    </p>
                </section>

                <section class="content-section">
                    <h4>Informasi Penyewa</h4>
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($booking['user_nama']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['user_email']); ?></p>
                    <p><strong>Kontak:</strong> <?php echo htmlspecialchars($booking['user_kontak'] ?? '-'); ?></p>
                </section>

                <section class="content-section">
                    <h4>Informasi Kos Dipesan</h4>
                    <p><strong>Nama Kos:</strong> <?php echo htmlspecialchars($booking['nama_kos']); ?></p>
                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($booking['kos_alamat']); ?></p>
                    <p><strong>Harga per Bulan (saat booking):</strong> Rp <?php echo number_format($booking['kos_harga_per_bulan'], 0, ',', '.'); ?></p>
                    <p><strong>Kamar Tersisa (saat ini):</strong> <?php echo htmlspecialchars($booking['kos_kamar_tersedia'] ?? 'N/A'); ?></p>
                    <p><strong>Status Kos (saat ini):</strong> <?php echo htmlspecialchars(ucfirst($booking['status_kos_saat_ini'] ?? 'N/A')); ?></p>
                </section>

                <?php if (isset($booking['payment_id_val'])): ?>
                <section class="content-section">
                    <h4>Informasi Pembayaran</h4>
                    <p><strong>ID Pembayaran:</strong> <?php echo htmlspecialchars($booking['payment_id_val']); ?></p>
                    <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($booking['metode_pembayaran']); ?></p>
                    <p><strong>Jumlah Pembayaran:</strong> Rp <?php echo number_format($booking['jumlah_pembayaran'], 0, ',', '.'); ?></p>
                    <p><strong>Status Pembayaran:</strong>
                        <?php
                            $statusPembayaran = $booking['status_pembayaran'] ?? 'pending';
                            $statusClass = 'status-default'; // Default badge class
                            switch ($statusPembayaran) {
                                case 'paid': $statusClass = 'status-confirmed'; break; /* Using confirmed green for paid */
                                case 'pending': $statusClass = 'status-pending'; break; /* Using pending yellow */
                                case 'failed': $statusClass = 'status-rejected'; break; /* Using rejected red */
                            }
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars(ucfirst($statusPembayaran)); ?>
                        </span>
                    </p>
                    <p><strong>Tanggal Pembayaran:</strong> <?php echo htmlspecialchars(date('d M Y, H:i:s', strtotime($booking['tanggal_pembayaran']))); ?></p>
                    <?php if(!empty($booking['bukti_pembayaran'])): ?>
                        <p><strong>Bukti Pembayaran:</strong> <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . $booking['bukti_pembayaran']); ?>" target="_blank">Lihat Bukti</a></p>
                    <?php endif; ?>
                </section>
                <?php else: ?>
                <section class="content-section">
                    <h4>Informasi Pembayaran</h4>
                    <p>Belum ada data pembayaran yang tercatat untuk booking ini.</p>
                </section>
                <?php endif; ?>
            </div>

            <div class="actions-bar">
                <?php if ($booking['status_pemesanan'] === 'pending'): ?>
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingConfirm/' . $booking['booking_id_val']); ?>"
                       onclick="return confirm('Anda yakin ingin MENGONFIRMASI pesanan ini? Pastikan kamar masih tersedia dan pembayaran sudah valid.');"
                       class="btn btn-primary">
                        Konfirmasi Pesanan Ini
                    </a>
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingReject/' . $booking['booking_id_val']); ?>"
                       onclick="return confirm('Anda yakin ingin MENOLAK pesanan ini?');"
                       class="btn btn-danger">
                        Tolak Pesanan Ini
                    </a>
                <?php elseif($booking['status_pemesanan'] === 'confirmed'): ?>
                    <p>Pesanan ini sudah dikonfirmasi. <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingReject/' . $booking['booking_id_val']); ?>" onclick="return confirm('Anda yakin ingin MEMBATALKAN (REJECT) pesanan yang sudah dikonfirmasi ini?');" class="link-danger">Batalkan Konfirmasi?</a></p>
                <?php else: ?>
                    <p>Status pesanan: <?php echo htmlspecialchars(ucfirst($booking['status_pemesanan'])); ?></p>
                <?php endif; ?>
            </div>

            <div class="back-link-container">
                <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>" class="btn btn-secondary">Kembali ke Daftar Pemesanan</a>
            </div>

        <?php else: ?>
            <p class="alert-message">Detail pemesanan tidak ditemukan.</p>
            <div class="back-link-container">
                <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>" class="btn btn-secondary">Kembali ke Daftar Pemesanan</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>