<?php

?>
<style>

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); /* Responsif */
        gap: 20px;
        margin-bottom: 30px;
    }
    .dashboard-card {
        color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Agar link "Lihat Detail" bisa di bawah */
        min-height: 150px; /* Tinggi minimal kartu */
    }
    .dashboard-card .card-title {
        font-size: 1.1em;
        font-weight: 500;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    .dashboard-card .card-count {
        font-size: 2.8em;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 10px;
    }
    .dashboard-card .card-link a {
        font-size: 0.9em;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        display: inline-block;
        margin-top: auto; /* Mendorong link ke bawah */
    }
    .dashboard-card .card-link a:hover {
        color: #fff;
        text-decoration: underline;
    }

    /* Warna-warna kartu (Anda bisa sesuaikan dengan palet Skydash) */
    .card-color-1 { background-color: #4747A1; } /* Ungu Kebiruan (mirip Total Materi) */
    .card-color-2 { background-color: #F95F53; } /* Merah Oranye (mirip Total Pendaftar) */
    .card-color-3 { background-color: #25A689; } /* Hijau Tosca (mirip Total Peserta) */
    .card-color-4 { background-color: #FF9F0E; } /* Oranye Kuning (mirip Total Tugas) */

    /* Aktivitas list styling (opsional, dari sebelumnya) */
    .activity-list { list-style: none; padding-left: 0; }
    .activity-list li { background-color: #f9f9f9; padding: 12px; border-bottom: 1px solid #eee; margin-bottom: 8px; border-radius: 4px;}
    .activity-list small { color: #777; display: block; margin-top: 3px; }
</style>

<div class="dashboard-greeting" style="margin-bottom: 25px; padding-bottom:15px; border-bottom:1px solid #eee;">
    <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?>!</h3>
    <p>Ini adalah ringkasan aktivitas dan data terbaru dari sistem booking kos Anda.</p>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card card-color-1">
        <p class="card-title">TOTAL PENGGUNA</p>
        <p class="card-count"><?php echo htmlspecialchars($totalUsers ?? 0); ?></p>
        <p class="card-link"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>">Lihat Detail &rarr;</a></p>
    </div>

    <div class="dashboard-card card-color-2">
        <p class="card-title">TOTAL PROPERTI KOS</p>
        <p class="card-count"><?php echo htmlspecialchars($totalKos ?? 0); ?></p>
        <p class="card-link"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>">Lihat Detail &rarr;</a></p>
    </div>

    <div class="dashboard-card card-color-3">
        <p class="card-title">TOTAL PESANAN</p> <p class="card-count"><?php echo htmlspecialchars($totalBookings ?? 0); ?></p>
        <p class="card-link"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>">Lihat Detail &rarr;</a></p>
    </div>

    <div class="dashboard-card card-color-4">
        <p class="card-title">PESANAN PENDING</p>
        <p class="card-count"><?php echo htmlspecialchars($pendingBookings ?? 0); ?></p>
        <?php if (($pendingBookings ?? 0) > 0): ?>
            <p class="card-link"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>">Konfirmasi Pesanan &rarr;</a></p>
        <?php else: ?>
            <p class="card-link"><span style="opacity:0.7;">Tidak ada pesanan pending</span></p>
        <?php endif; ?>
    </div>
</div>

<div class="chart-container" style="margin-bottom: 30px;">
    <h4>Grafik Pemesanan (Contoh Placeholder)</h4>
    <p style="text-align:center; padding: 50px; background-color:#f0f0f0; border-radius:5px; color:#777;">
        <em>Area ini akan diisi dengan chart interaktif...</em>
    </p>
</div>

<div style="display:flex; flex-wrap:wrap; gap:30px; margin-top: 30px;">
    <section style="flex:2; min-width: 320px;">
        <h4>🗓️ Pemesanan Terkonfirmasi Terbaru</h4>
        <?php if (!empty($recentConfirmedBookings)): ?>
            <ul class="activity-list">
                <?php foreach ($recentConfirmedBookings as $booking): ?>
                    <li >
                        <strong>ID: <?php echo htmlspecialchars($booking['id']); ?></strong> - Kos: <?php echo htmlspecialchars($booking['nama_kos']); ?><br>
                        Penyewa: <?php echo htmlspecialchars($booking['nama_penyewa']); ?> | Total: Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?>
                        <small>Dipesan: <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($booking['tanggal_pemesanan']))); ?></small>
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookingDetail/' . $booking['id']); ?>" style="font-size:0.85em; text-decoration:none;">Lihat Detail</a>
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
             <ul class="activity-list">
                <?php foreach ($recentLogs as $log): ?>
                    <li>
                        <strong style="color: #34495e;"><?php echo htmlspecialchars($log['aksi']); ?></strong><br>
                        <small>
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
            <p>Belum ada aktivitas tercatat.</p>
        <?php endif; ?>
    </section>
</div>

<hr style="margin: 40px 0;">
<h3>Navigasi Cepat Manajemen</h3>
<div class="quick-nav" style="list-style-type: none; padding: 0; display:flex; gap:15px; flex-wrap:wrap; margin-top:15px;">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" style="padding: 12px 20px; background-color: #007bff; color:white; text-decoration:none; border-radius:5px; font-size:1em;">Kelola Data Kos</a>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>" style="padding: 12px 20px; background-color: #17a2b8; color:white; text-decoration:none; border-radius:5px; font-size:1em;">Kelola Pengguna</a>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>" style="padding: 12px 20px; background-color: #28a745; color:white; text-decoration:none; border-radius:5px; font-size:1em;">Kelola Pemesanan</a>
</div>