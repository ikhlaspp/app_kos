<?php

?>
<h2><?php echo htmlspecialchars($pageTitle ?? 'Detail Pemesanan'); ?></h2>

<?php if (!empty($booking)): ?>
    <div style="display:flex; flex-wrap:wrap; gap: 20px;">
        <section style="flex:1 1 300px; background-color:#f8f9fa; padding:15px; border-radius:5px; border:1px solid #eee; margin-bottom:15px;">
            <h4>Informasi Booking (#<?php echo htmlspecialchars($booking['booking_id_val']); ?>)</h4>
            <p><strong>Tanggal Pemesanan:</strong> <?php echo htmlspecialchars(date('d M Y, H:i:s', strtotime($booking['tanggal_pemesanan']))); ?></p>
            <p><strong>Tanggal Mulai Sewa:</strong> <?php echo htmlspecialchars(date('d M Y', strtotime($booking['tanggal_mulai']))); ?></p>
            <p><strong>Tanggal Selesai Sewa:</strong> <?php echo htmlspecialchars(date('d M Y', strtotime($booking['tanggal_selesai']))); ?></p>
            <p><strong>Durasi Sewa:</strong> <?php echo htmlspecialchars($booking['durasi_sewa']); ?></p>
            <p><strong>Total Harga:</strong> Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></p>
            <p><strong>Status Pemesanan:</strong> 
                <span style="font-weight:bold; padding: 3px 7px; border-radius:3px; color: <?php 
                    $statusPemesanan = $booking['status_pemesanan'] ?? 'pending'; $textColor = 'white';
                    switch ($statusPemesanan) {
                        case 'confirmed': $bgColor = '#28a745'; break;
                        case 'pending': $bgColor = '#ffc107'; $textColor = '#333'; break;
                        case 'rejected': case 'canceled': $bgColor = '#dc3545'; break;
                        case 'completed': $bgColor = '#17a2b8'; break;
                        default: $bgColor = '#6c757d';
                    } echo $textColor;
                ?>; background-color: <?php echo $bgColor; ?>;">
                    <?php echo ucfirst(htmlspecialchars($statusPemesanan)); ?>
                </span>
            </p>
        </section>

        <section style="flex:1 1 300px; background-color:#f8f9fa; padding:15px; border-radius:5px; border:1px solid #eee; margin-bottom:15px;">
            <h4>Informasi Penyewa</h4>
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($booking['user_nama']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['user_email']); ?></p>
            <p><strong>Kontak:</strong> <?php echo htmlspecialchars($booking['user_kontak'] ?? '-'); ?></p>
        </section>
        
        <section style="flex:1 1 300px; background-color:#f8f9fa; padding:15px; border-radius:5px; border:1px solid #eee; margin-bottom:15px;">
            <h4>Informasi Kos Dipesan</h4>
            <p><strong>Nama Kos:</strong> <?php echo htmlspecialchars($booking['nama_kos']); ?></p>
            <p><strong>Alamat:</strong> <?php echo htmlspecialchars($booking['kos_alamat']); ?></p>
            <p><strong>Harga per Bulan (saat booking):</strong> Rp <?php echo number_format($booking['kos_harga_per_bulan'], 0, ',', '.'); ?></p>
            <p><strong>Kamar Tersisa (saat ini):</strong> <?php echo htmlspecialchars($booking['kos_kamar_tersedia'] ?? 'N/A'); ?></p>
            <p><strong>Status Kos (saat ini):</strong> <?php echo htmlspecialchars(ucfirst($booking['status_kos_saat_ini'] ?? 'N/A')); ?></p>
        </section>

        <?php if (isset($booking['payment_id_val'])): ?>
        <section style="flex:1 1 300px; background-color:#f8f9fa; padding:15px; border-radius:5px; border:1px solid #eee; margin-bottom:15px;">
            <h4>Informasi Pembayaran</h4>
            <p><strong>ID Pembayaran:</strong> <?php echo htmlspecialchars($booking['payment_id_val']); ?></p>
            <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($booking['metode_pembayaran']); ?></p>
            <p><strong>Jumlah Pembayaran:</strong> Rp <?php echo number_format($booking['jumlah_pembayaran'], 0, ',', '.'); ?></p>
            <p><strong>Status Pembayaran:</strong> 
                 <span style="font-weight:bold; padding: 3px 7px; border-radius:3px; color: <?php 
                    $statusPembayaran = $booking['status_pembayaran'] ?? 'pending'; $textColorPembayaran = 'white';
                    switch ($statusPembayaran) {
                        case 'paid': $bgColorPembayaran = '#28a745'; break;
                        case 'pending': $bgColorPembayaran = '#ffc107'; $textColorPembayaran = '#333'; break;
                        case 'failed': $bgColorPembayaran = '#dc3545'; break;
                        default: $bgColorPembayaran = '#6c757d';
                    } echo $textColorPembayaran;
                ?>; background-color: <?php echo $bgColorPembayaran; ?>;">
                    <?php echo ucfirst(htmlspecialchars($statusPembayaran)); ?>
                </span>
            </p>
            <p><strong>Tanggal Pembayaran:</strong> <?php echo htmlspecialchars(date('d M Y, H:i:s', strtotime($booking['tanggal_pembayaran']))); ?></p>
            <?php if(!empty($booking['bukti_pembayaran'])): ?>
                <p><strong>Bukti Pembayaran:</strong> <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . $booking['bukti_pembayaran']); ?>" target="_blank">Lihat Bukti</a></p>
            <?php endif; ?>
        </section>
        <?php else: ?>
        <section style="flex:1 1 300px; background-color:#f8f9fa; padding:15px; border-radius:5px; border:1px solid #eee;">
            <h4>Informasi Pembayaran</h4>
            <p>Belum ada data pembayaran yang tercatat untuk booking ini.</p>
        </section>
        <?php endif; ?>
    </div>

    <div style="margin-top: 30px; padding-top:20px; border-top:1px solid #eee;">
        <?php if ($booking['status_pemesanan'] === 'pending'): ?>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingConfirm/' . $booking['booking_id_val']); ?>" 
               onclick="return confirm('Anda yakin ingin MENGONFIRMASI pesanan ini? Pastikan kamar masih tersedia dan pembayaran sudah valid.');" 
               style="padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;">
                Konfirmasi Pesanan Ini
            </a>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingReject/' . $booking['booking_id_val']); ?>" 
               onclick="return confirm('Anda yakin ingin MENOLAK pesanan ini?');"
               style="padding: 10px 15px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 4px;">
                Tolak Pesanan Ini
            </a>
        <?php elseif($booking['status_pemesanan'] === 'confirmed'): ?>
             <p>Pesanan ini sudah dikonfirmasi. <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingReject/' . $booking['booking_id_val']); ?>" onclick="return confirm('Anda yakin ingin MEMBATALKAN (REJECT) pesanan yang sudah dikonfirmasi ini?');" style="color: #dc3545;">Batalkan Konfirmasi?</a></p>
        <?php else: ?>
            <p>Status pesanan: <?php echo ucfirst(htmlspecialchars($booking['status_pemesanan'])); ?></p>
        <?php endif; ?>
    </div>

    <p style="margin-top:30px;"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>">Kembali ke Daftar Pemesanan</a></p>
<?php else: ?>
    <p>Detail pemesanan tidak ditemukan.</p>
<?php endif; ?>