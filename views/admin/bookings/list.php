<?php

?>
<h2><?php echo htmlspecialchars($pageTitle ?? 'Kelola Pemesanan'); ?></h2>

<?php if (!empty($daftarBooking)): ?>
    <table style="width: 100%; border-collapse: collapse; margin-top:15px; font-size:0.9em;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:left;">ID</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:left;">Tgl Pesan</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:left;">Penyewa</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:left;">Nama Kos</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:left;">Durasi</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:right;">Total (Rp)</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:left;">Status Booking</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:left;">Status Bayar</th>
                <th style="padding: 10px; border: 1px solid #dee2e6; text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($daftarBooking as $booking): ?>
            <tr style="<?php echo ($booking['status_pemesanan'] === 'pending') ? 'background-color: #fff3cd;' : ''; ?>">
                <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['tanggal_pemesanan']))); ?></td>
                <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($booking['nama_penyewa']); ?><br><small><?php echo htmlspecialchars($booking['email_penyewa']); ?></small></td>
                <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($booking['nama_kos']); ?></td>
                <td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo htmlspecialchars($booking['durasi_sewa']); ?></td>
                <td style="padding: 8px; border: 1px solid #dee2e6; text-align:right;"><?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                <td style="padding: 8px; border: 1px solid #dee2e6;">
                    <span style="font-weight:bold; padding: 3px 7px; border-radius:3px; color: <?php 
                        $statusPemesanan = $booking['status_pemesanan'] ?? 'pending';
                        $textColor = 'white';
                        switch ($statusPemesanan) {
                            case 'confirmed': $bgColor = '#28a745'; break;
                            case 'pending': $bgColor = '#ffc107'; $textColor = '#333'; break;
                            case 'rejected': case 'canceled': $bgColor = '#dc3545'; break;
                            case 'completed': $bgColor = '#17a2b8'; break;
                            default: $bgColor = '#6c757d';
                        }
                        echo $textColor;
                    ?>; background-color: <?php echo $bgColor; ?>;">
                        <?php echo ucfirst(htmlspecialchars($statusPemesanan)); ?>
                    </span>
                </td>
                <td style="padding: 8px; border: 1px solid #dee2e6;">
                     <span style="font-weight:bold; padding: 3px 7px; border-radius:3px; color: <?php 
                        $statusPembayaran = $booking['status_pembayaran'] ?? 'pending';
                        $textColorPembayaran = 'white';
                        switch ($statusPembayaran) {
                            case 'paid': $bgColorPembayaran = '#28a745'; break;
                            case 'pending': $bgColorPembayaran = '#ffc107'; $textColorPembayaran = '#333'; break;
                            case 'failed': $bgColorPembayaran = '#dc3545'; break;
                            default: $bgColorPembayaran = '#6c757d';
                        }
                        echo $textColorPembayaran;
                    ?>; background-color: <?php echo $bgColorPembayaran; ?>;">
                        <?php echo ucfirst(htmlspecialchars($statusPembayaran)); ?>
                    </span>
                </td>
                <td style="padding: 8px; border: 1px solid #dee2e6; text-align:center; white-space:nowrap;">
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingDetail/' . $booking['booking_id']); ?>" style="text-decoration:none; color: #007bff; margin-right:5px; padding:3px 5px; border:1px solid #007bff; border-radius:3px;">Detail</a>
                    <?php if ($booking['status_pemesanan'] === 'pending'): ?>
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingConfirm/' . $booking['booking_id']); ?>" 
                           onclick="return confirm('Anda yakin ingin MENGONFIRMASI pesanan ini? Pastikan kamar masih tersedia dan pembayaran sudah valid.');" 
                           style="text-decoration:none; color: #28a745; margin-right:5px; padding:3px 5px; border:1px solid #28a745; border-radius:3px;">Konfirmasi</a>
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingReject/' . $booking['booking_id']); ?>" 
                           onclick="return confirm('Anda yakin ingin MENOLAK pesanan ini?');"
                           style="text-decoration:none; color: #dc3545; padding:3px 5px; border:1px solid #dc3545; border-radius:3px;">Tolak</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada data pemesanan.</p>
<?php endif; ?>