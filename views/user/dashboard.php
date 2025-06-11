<?php
// views/user/dashboard.php
// Assumes $user, $bookings, $availableVouchers, $claimedVouchers are passed from UserController
// Assumes $appConfig is available from BaseController

// Custom Color Palette for consistency with other views
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#E9F1F7';   // New background for headers
$paletteMediumBlue = '#4A90E2'; // Used for primary accents/buttons
$paletteDarkBlue = '#1A3A5B';   // Used for darker text/hover states (now for all titles)
$paletteTextPrimary = '#0D2A57'; // Main text color
$paletteTextSecondary = '#555555'; // Secondary text color (e.g., address)
$paletteAccentBlue = '#6A9EFF'; // For lighter blue accents if needed

// Status Colors - map to your palette or similar
$statusSuccess = '#28a745'; // Green
$statusWarning = '#ffc107'; // Yellow
$statusDanger = '#dc3545';  // Red
$statusInfo = '#17a2b8';    // Teal/Light Blue
$statusSecondary = '#6c757d'; // Grey
$statusPrimary = '#007bff'; // Bootstrap primary, can be adjusted to $paletteMediumBlue

// Helper to adjust color brightness (used for card borders)
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

<style>
    /* Main H2 Title Color Consistency */
    h2 {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; /* Applies to the main dashboard title */
    }

    /* Card Header Customization (All headers now use light background with dark text) */
    .card-header-custom,
    .card-header-info,
    .card-header-secondary {
        background-color: <?php echo htmlspecialchars($paletteLightBlue); ?> !important; /* Unified light background */
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?> !important; /* Unified dark text color */
        font-weight: 700; /* Bolder text for all headers */
        border-bottom: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -20)); ?> !important; /* Subtle border */
    }

    /* Table Header Customization (for Booking History) */
    .table-header-custom th {
        background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
        border-color: <?php echo htmlspecialchars(adjustBrightness($paletteDarkBlue, -10)); ?>;
    }
    .table-hover tbody tr:hover {
        background-color: <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, 5)); ?>; /* Lighter hover for rows, use palette base */
    }

    /* Custom Button for Edit Profile */
    .btn-outline-custom-primary {
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        transition: all 0.2s ease;
    }
    .btn-outline-custom-primary:hover {
        background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
    }

    /* Voucher Card Styling for Available Vouchers */
    .card-voucher-available {
        border-color: <?php echo htmlspecialchars($paletteAccentBlue); ?> !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .card-voucher-available .card-title {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; /* Consistent title color */
        font-weight: 700;
    }
    .card-voucher-available .card-subtitle {
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
    }
    .card-voucher-available .card-text {
        font-size: 0.9rem;
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
    }
    .card-voucher-available .card-text strong {
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
    }

    /* Claim Voucher Button */
    .btn-claim-voucher {
        background-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        border-color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        color: <?php echo htmlspecialchars($paletteWhite); ?>;
        font-weight: 600;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    .btn-claim-voucher:hover {
        background-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        border-color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
    }

    /* Voucher Card Styling for Claimed/Used Vouchers */
    .card-voucher-claimed {
        border: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -10)); ?>; /* Subtle border matching header */
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
        height: 100%;
    }
    .card-voucher-claimed .card-title {
        font-size: 1.1rem;
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>; /* Consistent title color */
        font-weight: 600;
    }
    .card-voucher-claimed .card-subtitle {
        font-size: 0.85rem;
        color: <?php echo htmlspecialchars($paletteTextSecondary); ?>;
        margin-bottom: 0.5rem;
    }
    .card-voucher-claimed .card-text {
        font-size: 0.8rem;
        line-height: 1.4;
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
    }
    .card-voucher-claimed .card-footer {
        font-size: 0.8rem;
        padding: 0.75rem 1rem;
        background-color: <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -5)); ?>; /* Slightly darker than body */
        border-top: 1px solid <?php echo htmlspecialchars(adjustBrightness($paletteLightBlue, -20)); ?>;
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-voucher-claimed .badge {
        font-size: 0.75rem;
    }

    /* Overrides for default Bootstrap badges to match palette */
    .badge.bg-success { background-color: <?php echo htmlspecialchars($statusSuccess); ?> !important; }
    .badge.bg-warning { background-color: <?php echo htmlspecialchars($statusWarning); ?> !important; }
    .badge.bg-danger { background-color: <?php echo htmlspecialchars($statusDanger); ?> !important; }
    .badge.bg-info { background-color: <?php echo htmlspecialchars($statusInfo); ?> !important; }
    .badge.bg-primary { background-color: <?php echo htmlspecialchars($statusPrimary); ?> !important; }
    .badge.bg-secondary { background-color: <?php echo htmlspecialchars($statusSecondary); ?> !important; }

</style>

<h2 class="mb-4"><?php echo htmlspecialchars($pageTitle ?? 'Dashboard Saya'); ?></h2>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header card-header-custom">
                Informasi Pengguna
            </div>
            <div class="card-body">
                <p class="card-text">Selamat datang kembali, <strong><?php echo htmlspecialchars($user['nama'] ?? 'Pengguna'); ?></strong>!</p>
                <p class="card-text">Email Anda: <?php echo htmlspecialchars($user['email'] ?? 'Tidak ada email'); ?></p>
                <?php if (isset($user['no_telepon']) && !empty($user['no_telepon'])): ?>
                    <p class="card-text">No. Telepon: <?php echo htmlspecialchars($user['no_telepon']); ?></p>
                <?php endif; ?>
                <?php if (isset($user['alamat']) && !empty($user['alamat'])): ?>
                    <p class="card-text">Alamat: <?php echo nl2br(htmlspecialchars($user['alamat'])); ?></p>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'user/editProfile'); ?>" class="btn btn-sm btn-outline-custom-primary mt-2">Edit Profil Saya</a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header card-header-info">
                Voucher Tersedia untuk Diklaim
            </div>
            <div class="card-body">
                <?php if (empty($availableVouchers)): ?>
                    <p class="text-muted card-text">Tidak ada voucher yang tersedia untuk Anda klaim saat ini.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($availableVouchers as $voucher): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 card-voucher-available">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($voucher['name']); ?></h5>
                                        <h6 class="card-subtitle mb-2">Kode: <strong><?php echo htmlspecialchars($voucher['code']); ?></strong></h6>
                                        <p class="card-text">
                                            Diskon: <strong><?php echo htmlspecialchars($voucher['value']); ?><?php echo $voucher['type'] === 'percentage' ? '%' : ' Rp'; ?></strong><br>
                                            Kadaluarsa: <?php echo date('d M Y', strtotime($voucher['expiration_date'])); ?><br>
                                            <?php if ($voucher['min_transaction_amount']): ?>
                                                Min. Transaksi: Rp <?php echo number_format($voucher['min_transaction_amount'], 0, ',', '.'); ?><br>
                                            <?php endif; ?>
                                            <?php if ($voucher['max_discount_amount']): ?>
                                                Max. Diskon: Rp <?php echo number_format($voucher['max_discount_amount'], 0, ',', '.'); ?><br>
                                            <?php endif; ?>
                                        </p>
                                        <form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'user/claimVoucher/' . $voucher['id']); ?>" method="POST" class="mt-auto">
                                            <button type="submit" class="btn btn-sm btn-claim-voucher w-100"><i class="fas fa-gift me-1"></i> Klaim Voucher</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($claimedVouchers)): /* Conditionally display this card */ ?>
        <div class="card mb-4">
            <div class="card-header card-header-secondary">
                Voucher Anda
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($claimedVouchers as $userVoucher): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 card-voucher-claimed">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($userVoucher['name']); ?></h5>
                                    <h6 class="card-subtitle">Kode: <strong><?php echo htmlspecialchars($userVoucher['code']); ?></strong></h6>
                                    <p class="card-text mt-2">
                                        Diskon: <strong><?php echo htmlspecialchars($userVoucher['value']); ?><?php echo $userVoucher['type'] === 'percentage' ? '%' : ' Rp'; ?></strong><br>
                                        Kadaluarsa: <?php echo date('d M Y', strtotime($userVoucher['expiration_date'])); ?><br>
                                        <?php if ($userVoucher['min_transaction_amount']): ?>
                                            Min. Transaksi: Rp <?php echo number_format($userVoucher['min_transaction_amount'], 0, ',', '.'); ?><br>
                                        <?php endif; ?>
                                        <?php if ($userVoucher['max_discount_amount']): ?>
                                            Max. Diskon: Rp <?php echo number_format($userVoucher['max_discount_amount'], 0, ',', '.'); ?><br>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <?php
                                    $isExpiredAfterClaim = (strtotime($userVoucher['expiration_date']) < time() && $userVoucher['status'] !== 'used');
                                    $statusBadgeClass = 'bg-secondary';
                                    $statusText = htmlspecialchars(ucfirst($userVoucher['status']));

                                    if ($userVoucher['status'] === 'available_to_claim') {
                                        $statusBadgeClass = 'bg-primary'; /* Should not happen here with new logic but kept for safety */
                                        $statusText = 'Tersedia untuk Klaim';
                                    } elseif ($userVoucher['status'] === 'claimed') {
                                        $statusBadgeClass = 'bg-info';
                                        $statusText = 'Terklaim';
                                    } elseif ($userVoucher['status'] === 'used') {
                                        $statusBadgeClass = 'bg-success';
                                        $statusText = 'Sudah Digunakan';
                                    } elseif ($isExpiredAfterClaim) {
                                        $statusBadgeClass = 'bg-danger';
                                        $statusText = 'Kadaluarsa';
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusBadgeClass; ?>"><?php echo $statusText; ?></span>
                                    <span>
                                        Digunakan: <?php echo htmlspecialchars($userVoucher['times_used']); ?> / <?php echo htmlspecialchars($userVoucher['usage_limit_per_user']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<div class="card mb-4">
    <div class="card-header card-header-secondary">
        Riwayat Pemesanan Anda
    </div>
    <div class="card-body">
        <?php if (empty($bookings)): ?>
            <p class="text-muted card-text">Anda belum memiliki riwayat pemesanan.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr class="table-header-custom">
                            <th>ID</th>
                            <th>Kos</th>
                            <th>Tgl Pesan</th>
                            <th>Mulai - Selesai</th>
                            <th>Total (Rp)</th>
                            <th>Voucher</th>
                            <th>Status Booking</th>
                            <th>Status Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['nama_kos']); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['tanggal_pemesanan']))); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y', strtotime($booking['tanggal_mulai']))); ?> s/d <?php htmlspecialchars(date('d M Y', strtotime($booking['tanggal_selesai']))); ?></td>
                                <td class="text-end"><?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($booking['voucher_code'] ?? '-'); ?></td>
                                <td>
                                    <?php
                                        $statusPemesanan = $booking['status_pemesanan'] ?? 'pending';
                                        $statusClassPemesanan = '';
                                        switch ($statusPemesanan) {
                                            case 'confirmed': $statusClassPemesanan = 'bg-success'; break;
                                            case 'pending': $statusClassPemesanan = 'bg-warning text-dark'; break;
                                            case 'rejected': case 'canceled': $statusClassPemesanan = 'bg-danger'; break;
                                            case 'completed': $statusClassPemesanan = 'bg-info'; break;
                                            default: $statusClassPemesanan = 'bg-secondary'; break;
                                        }
                                    ?>
                                    <span class="badge <?php echo $statusClassPemesanan; ?>">
                                        <?php echo ucfirst(htmlspecialchars($statusPemesanan)); ?>
                                    </span>
                                    <?php if ($statusPemesanan === 'pending'): ?>
                                        <br><small class="text-muted fst-italic">(Menunggu Konfirmasi Admin)</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $statusPembayaran = $booking['status_pembayaran'] ?? 'pending';
                                        $statusClassPembayaran = '';
                                        switch ($statusPembayaran) {
                                            case 'paid': $statusClassPembayaran = 'bg-success'; break;
                                            case 'pending': $statusClassPembayaran = 'bg-warning text-dark'; break;
                                            case 'failed': $statusClassPembayaran = 'bg-danger'; break;
                                            default: $statusClassPembayaran = 'bg-secondary'; break;
                                        }
                                    ?>
                                    <span class="badge <?php echo $statusClassPembayaran; ?>">
                                        <?php echo ucfirst(htmlspecialchars($statusPembayaran)); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>