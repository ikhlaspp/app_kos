<?php

// Variabel $pageTitle, $appConfig, $user, dan $bookingHistory tersedia.
?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Dashboard Saya'); ?></h2>

<div class="user-info" style="margin-bottom: 30px; padding: 15px; background-color: #e9ecef; border-radius: 5px;">
    <p>Selamat datang kembali, <strong><?php echo htmlspecialchars($user['nama'] ?? 'Pengguna'); ?></strong>!</p>
    <p>Email Anda: <?php echo htmlspecialchars($user['email'] ?? 'Tidak ada email'); ?></p>
    <?php if (isset($user['no_telepon']) && !empty($user['no_telepon'])): ?>
        <p>No. Telepon: <?php echo htmlspecialchars($user['no_telepon']); ?></p>
    <?php endif; ?>
    <?php if (isset($user['alamat']) && !empty($user['alamat'])): ?>
        <p>Alamat: <?php echo nl2br(htmlspecialchars($user['alamat'])); ?></p>
    <?php endif; ?>
    <p style="margin-top: 10px;">
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'user/editProfile'); ?>" style="text-decoration: none; color: #007bff; font-weight:bold;">Edit Profil Saya</a>
    </p>
</div>

<h3>Riwayat Pemesanan Anda</h3>
<?php if (!empty($bookingHistory)): ?>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size:0.9em;">
        <thead>
            <tr style="background-color: #007bff; color: white;">
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">ID</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Kos</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Tgl Pesan</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Mulai - Selesai</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: right;">Total (Rp)</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Status Booking</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Status Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookingHistory as $booking): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                    <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($booking['nama_kos']); ?></td>
                    <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['tanggal_pemesanan']))); ?></td>
                    <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars(date('d M Y', strtotime($booking['tanggal_mulai']))); ?> s/d <?php echo htmlspecialchars(date('d M Y', strtotime($booking['tanggal_selesai']))); ?></td>
                    <td style="padding: 8px; border: 1px solid #dee2e6; text-align: right;"><?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                    <td style="padding: 8px; border: 1px solid #dee2e6;">
                        <?php 
                            $statusPemesanan = $booking['status_pemesanan'] ?? 'pending';
                            $bgColorPemesanan = '#6c757d'; $textColorPemesanan = 'white';
                            switch ($statusPemesanan) {
                                case 'confirmed': $bgColorPemesanan = '#28a745'; break;
                                case 'pending': $bgColorPemesanan = '#ffc107'; $textColorPemesanan = '#212529'; break;
                                case 'rejected': case 'canceled': $bgColorPemesanan = '#dc3545'; break;
                                case 'completed': $bgColorPemesanan = '#17a2b8'; break;
                            }
                        ?>
                        <span style="padding: 3px 7px; border-radius: 3px; color: <?php echo $textColorPemesanan; ?>; background-color: <?php echo $bgColorPemesanan; ?>;">
                            <?php echo ucfirst(htmlspecialchars($statusPemesanan)); ?>
                        </span>
                        <?php if ($statusPemesanan === 'pending'): ?>
                            <br><small style="font-style:italic;">(Menunggu Konfirmasi Admin)</small>
                        <?php endif; ?>
                    </td>
                     <td style="padding: 8px; border: 1px solid #dee2e6;">
                        <?php 
                            $statusPembayaran = $booking['status_pembayaran'] ?? 'pending';
                            $bgColorPembayaran = '#6c757d'; $textColorPembayaran = 'white';
                            switch ($statusPembayaran) {
                                case 'paid': $bgColorPembayaran = '#28a745'; break;
                                case 'pending': $bgColorPembayaran = '#ffc107'; $textColorPembayaran = '#212529'; break;
                                case 'failed': $bgColorPembayaran = '#dc3545'; break;
                            }
                        ?>
                        <span style="padding: 3px 7px; border-radius: 3px; color: <?php echo $textColorPembayaran; ?>; background-color: <?php echo $bgColorPembayaran; ?>;">
                            <?php echo ucfirst(htmlspecialchars($statusPembayaran)); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Anda belum memiliki riwayat pemesanan.</p>
<?php endif; ?>