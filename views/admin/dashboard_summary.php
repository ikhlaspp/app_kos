<?php
// File: nama_proyek_kos/views/admin/dashboard_summary.php
// Variabel seperti $pageTitle, $appConfig, $totalUsers, $totalKos, 
// $pendingBookings, $recentConfirmedBookings, $recentLogs diharapkan sudah tersedia dari AdminController.
?>

<div class="dashboard-greeting" style="margin-bottom: 25px; padding-bottom:15px; border-bottom:1px solid #eee;">
    <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?>!</h3>
    <p>Ini adalah ringkasan aktivitas dan data terbaru dari sistem booking kos Anda.</p>
</div>

<div class="summary-cards-container" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
    <div class="summary-card" style="background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; flex: 1; min-width: 220px; box-shadow: 0 3px 6px rgba(0,0,0,0.07); text-align: center;">
        <h4 style="margin-top: 0; color: #555; font-size: 1.1em; margin-bottom: 10px;">Total Pengguna</h4>
        <p style="font-size: 2.5em; font-weight: bold; color: #3498db; margin-bottom: 5px;"><?php echo htmlspecialchars($totalUsers ?? 0); ?></p>
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>" style="font-size: 0.9em; text-decoration:none; color:#007bff;">Lihat Detail</a>
    </div>

    <div class="summary-card" style="background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; flex: 1; min-width: 220px; box-shadow: 0 3px 6px rgba(0,0,0,0.07); text-align: center;">
        <h4 style="margin-top: 0; color: #555; font-size: 1.1em; margin-bottom: 10px;">Total Properti Kos</h4>
        <p style="font-size: 2.5em; font-weight: bold; color: #2ecc71; margin-bottom: 5px;"><?php echo htmlspecialchars($totalKos ?? 0); ?></p>
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" style="font-size: 0.9em; text-decoration:none; color:#007bff;">Lihat Detail</a>
    </div>

    <div class="summary-card" style="background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; flex: 1; min-width: 220px; box-shadow: 0 3px 6px rgba(0,0,0,0.07); text-align: center;">
        <h4 style="margin-top: 0; color: #555; font-size: 1.1em; margin-bottom: 10px;">Pesanan Pending</h4>
        <p style="font-size: 2.5em; font-weight: bold; color: #e67e22; margin-bottom: 5px;"><?php echo htmlspecialchars($pendingBookings ?? 0); ?></p>
        <?php if (($pendingBookings ?? 0) > 0): ?>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>" style="font-size: 0.9em; text-decoration:none; color:#007bff;">Konfirmasi Pesanan</a>
        <?php else: ?>
            <span style="font-size: 0.9em; color: #7f8c8d;">Tidak ada pesanan pending</span>
        <?php endif; ?>
    </div>
</div>

<div class="chart-container" style="margin-bottom: 30px;">
    <h4>Grafik Pemesanan (Contoh Placeholder)</h4>
    <p style="text-align:center; padding: 50px; background-color:#f0f0f0; border-radius:5px; color:#777;">
        <em>Area ini akan diisi dengan chart interaktif menggunakan library JavaScript seperti Chart.js atau ApexCharts. <br> Data untuk chart akan diambil dari database (misalnya, jumlah booking per hari/bulan). <br> Animasi awal bisa ditambahkan saat chart dimuat.</em>
    </p>
    </div>


<div style="display:flex; flex-wrap:wrap; gap:30px;">
    <section style="flex:2; min-width: 320px;">
        <h4>🗓️ Pemesanan Terkonfirmasi Terbaru</h4>
        <?php if (!empty($recentConfirmedBookings)): ?>
            <ul style="list-style: none; padding-left: 0;">
                <?php foreach ($recentConfirmedBookings as $booking): ?>
                    <li style="background-color: #ffffff; padding: 12px; border: 1px solid #eee; margin-bottom: 8px; border-radius: 4px;">
                        <strong>ID: <?php echo htmlspecialchars($booking['id']); ?></strong> - Kos: <?php echo htmlspecialchars($booking['nama_kos']); ?><br>
                        Penyewa: <?php echo htmlspecialchars($booking['nama_penyewa']); ?> | Total: Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?>
                        <small style="color: #777; display: block; margin-top: 3px;">Dipesan: <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['tanggal_pemesanan']))); ?></small>
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingDetail/' . $booking['id']); ?>" style="font-size:0.85em; text-decoration:none; color:#007bff;">Lihat Detail</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>">Lihat Semua Pemesanan</a>
        <?php else: ?>
            <p>Belum ada pemesanan terkonfirmasi baru-baru ini.</p>
        <?php endif; ?>
    </section>

    <section style="flex:1; min-width: 280px;">
        <h4>⚠️ Log Audit / Aktivitas Sistem Terbaru</h4>
        <?php if (isset($recentLogs) && !empty($recentLogs)): ?>
             <ul style="list-style: none; padding-left: 0; font-size:0.8em; max-height: 250px; overflow-y:auto; border:1px solid #eee; padding:10px; background-color:#fff; border-radius:4px;">
                <?php foreach ($recentLogs as $log): ?>
                    <li style="padding: 6px 0; border-bottom: 1px dotted #f0f0f0;">
                        <strong style="color: #34495e;"><?php echo htmlspecialchars($log['aksi']); ?></strong><br>
                        <small style="color: #7f8c8d;">
                            <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($log['timestamp']))); ?>
                            <?php if (!empty($log['nama_pengguna'])): ?>
                                | Oleh: <?php echo htmlspecialchars($log['nama_pengguna']); ?>
                            <?php elseif(!empty($log['user_id'])): ?>
                                | User ID: <?php echo htmlspecialchars($log['user_id']); ?>
                            <?php else: ?>
                                | Sistem
                            <?php endif; ?>
                        </small>
                        <?php 
                            if (!empty($log['detail_aksi'])) {
                                echo "<br><small style='color:#95a5a6; font-style:italic;'>Detail: " . htmlspecialchars(substr($log['detail_aksi'],0,100)) . (strlen($log['detail_aksi']) > 100 ? '...' : '') . "</small>";
                            }
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Belum ada aktivitas tercatat atau fitur log belum aktif sepenuhnya.</p>
        <?php endif; ?>
    </section>
</div>