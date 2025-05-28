<?php
// --- Color Palette Definition (Same as previous example) ---
$palette_white = '#FFFFFF';
$palette_light_blue_gray = '#EBF0F5';
$palette_medium_light_blue = '#D2DDE8';
$palette_medium_blue = '#6B8EB2';
$palette_dark_blue = '#2A4365';

// --- Theme Colors Based on Palette ---
$color_bg_page = $palette_light_blue_gray;
$color_bg_container = $palette_white;
$color_primary_text_heading = $palette_dark_blue;
$color_secondary_text_heading = $palette_medium_blue; // For less prominent headers or accents
$color_text_on_dark = $palette_white;
$color_text_on_light = '#333333';
$color_border_subtle = $palette_medium_light_blue;
$color_table_header_bg = $palette_light_blue_gray; // Lightest blue for table header
$color_table_header_text = $palette_dark_blue;

// --- Standard UX Colors ---
$color_success = '#28a745';
$color_warning = '#ffc107';
$color_text_on_warning = '#333333';
$color_danger = '#dc3545';
$color_info = '#17a2b8';
$color_muted = '#6c757d';
$color_action_link = $palette_medium_blue; // For general action links like "Detail"

// Assuming $appConfig, $daftarBooking, and $pageTitle are passed to this view file
// $appConfig = ['BASE_URL' => 'http://localhost/yourproject/'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Kelola Pemesanan'); ?> - Admin Panel</title>
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
            max-width: 1400px; /* Adjusted for potentially wider tables */
            margin: 30px auto;
            padding: 25px;
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
            font-size: 1.8em;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9em;
            border: 1px solid <?php echo $color_border_subtle; ?>;
            border-radius: 6px; /* Apply to table if you want rounded corners for the whole table */
            overflow: hidden; /* Important for border-radius on table */
        }
        .styled-table th,
        .styled-table td {
            padding: 12px 15px; /* Increased padding */
            border: 1px solid <?php echo $color_border_subtle; ?>;
            text-align: left;
        }
        .styled-table thead tr {
            background-color: <?php echo $color_table_header_bg; ?>;
            color: <?php echo $color_table_header_text; ?>;
            font-weight: bold;
        }
        .styled-table th {
             border-bottom: 2px solid <?php echo $palette_medium_blue; ?>; /* Stronger bottom border for headers */
        }
        .styled-table tbody tr {
            border-bottom: 1px solid <?php echo $color_border_subtle; ?>;
        }
        .styled-table tbody tr:nth-of-type(even) {
            /* background-color: #f9f9f9; /* Optional: subtle zebra striping, can use a very light palette color */
        }
        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid <?php echo $palette_medium_blue; ?>; /* Stronger border for last row */
        }
        .styled-table tbody tr:hover {
            background-color: <?php echo $palette_light_blue_gray; ?>; /* Hover effect */
        }
        .status-badge {
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            text-transform: capitalize;
            display: inline-block; /* Ensures padding and radius work well */
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .action-cell {
            white-space: nowrap;
        }
        .btn-action {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
            margin: 0 3px;
            border: 1px solid transparent;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .btn-action-detail {
            background-color: <?php echo $palette_medium_blue; ?>;
            color: <?php echo $color_text_on_dark; ?>;
            border-color: <?php echo $palette_medium_blue; ?>;
        }
        .btn-action-detail:hover {
            background-color: <?php echo $palette_dark_blue; ?>;
        }
        .btn-action-confirm {
            background-color: <?php echo $color_success; ?>;
            color: <?php echo $color_text_on_dark; ?>;
            border-color: <?php echo $color_success; ?>;
        }
        .btn-action-confirm:hover {
            opacity: 0.8;
        }
        .btn-action-reject {
            background-color: <?php echo $color_danger; ?>;
            color: <?php echo $color_text_on_dark; ?>;
            border-color: <?php echo $color_danger; ?>;
        }
        .btn-action-reject:hover {
            opacity: 0.8;
        }
        .row-pending {
            background-color: #fffadf !important; /* A light yellow, distinct from hover */
            font-weight: 500; /* Slightly bolder for pending */
        }
        .row-pending:hover {
            background-color: #fff7cc !important; /* Darker yellow on hover */
        }
        small { color: <?php echo $color_muted; ?>; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2 class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'Kelola Pemesanan'); ?></h2>

        <?php if (!empty($daftarBooking)): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tgl Pesan</th>
                        <th>Penyewa</th>
                        <th>Nama Kos</th>
                        <th>Durasi</th>
                        <th class="text-right">Total (Rp)</th>
                        <th>Status Booking</th>
                        <th>Status Bayar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftarBooking as $booking): ?>
                    <tr class="<?php echo ($booking['status_pemesanan'] === 'pending') ? 'row-pending' : ''; ?>">
                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                        <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['tanggal_pemesanan']))); ?></td>
                        <td><?php echo htmlspecialchars($booking['nama_penyewa']); ?><br><small><?php echo htmlspecialchars($booking['email_penyewa']); ?></small></td>
                        <td><?php echo htmlspecialchars($booking['nama_kos']); ?></td>
                        <td><?php echo htmlspecialchars($booking['durasi_sewa']); ?></td>
                        <td class="text-right"><?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                        <td>
                            <?php
                                $statusPemesanan = $booking['status_pemesanan'] ?? 'pending';
                                $textColor = $color_text_on_dark;
                                switch ($statusPemesanan) {
                                    case 'confirmed': $bgColor = $color_success; break;
                                    case 'pending': $bgColor = $color_warning; $textColor = $color_text_on_warning; break;
                                    case 'rejected': case 'canceled': $bgColor = $color_danger; break;
                                    case 'completed': $bgColor = $color_info; break;
                                    default: $bgColor = $color_muted;
                                }
                            ?>
                            <span class="status-badge" style="color: <?php echo $textColor; ?>; background-color: <?php echo $bgColor; ?>;">
                                <?php echo ucfirst(htmlspecialchars($statusPemesanan)); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                                $statusPembayaran = $booking['status_pembayaran'] ?? 'pending';
                                $textColorPembayaran = $color_text_on_dark;
                                switch ($statusPembayaran) {
                                    case 'paid': $bgColorPembayaran = $color_success; break;
                                    case 'pending': $bgColorPembayaran = $color_warning; $textColorPembayaran = $color_text_on_warning; break;
                                    case 'failed': $bgColorPembayaran = $color_danger; break;
                                    default: $bgColorPembayaran = $color_muted;
                                }
                            ?>
                            <span class="status-badge" style="color: <?php echo $textColorPembayaran; ?>; background-color: <?php echo $bgColorPembayaran; ?>;">
                                <?php echo ucfirst(htmlspecialchars($statusPembayaran)); ?>
                            </span>
                        </td>
                        <td class="text-center action-cell">
                            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingDetail/' . $booking['booking_id']); ?>" class="btn-action btn-action-detail">Detail</a>
                            <?php if ($booking['status_pemesanan'] === 'pending'): ?>
                                <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingConfirm/' . $booking['booking_id']); ?>"
                                   onclick="return confirm('Anda yakin ingin MENGONFIRMASI pesanan ini? Pastikan kamar masih tersedia dan pembayaran sudah valid.');"
                                   class="btn-action btn-action-confirm">Konfirmasi</a>
                                <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingReject/' . $booking['booking_id']); ?>"
                                   onclick="return confirm('Anda yakin ingin MENOLAK pesanan ini?');"
                                   class="btn-action btn-action-reject">Tolak</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada data pemesanan.</p>
        <?php endif; ?>
    </div>
</body>
</html>