<?php

// Variabel $pageTitle, $appConfig, dan $kos (detail kos) tersedia dari KosController
?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Detail Kos'); ?></h2>

<?php if (!empty($kos)): ?>
    <div class="kos-detail-card" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        
        <?php // Menampilkan gambar-gambar kos jika ada ?>
        <?php if (!empty($kos['gambar_kos']) && is_array($kos['gambar_kos'])): ?>
            <div class="kos-gallery" style="margin-bottom: 20px;">
                <?php foreach ($kos['gambar_kos'] as $gambar): ?>
                    <img src="<?php echo htmlspecialchars($appConfig['UPLOADS_URL_PATH'] . $gambar['path']); // Asumsi path adalah relatif dari BASE_URL atau path absolut sudah benar ?>" 
                         alt="<?php echo htmlspecialchars($gambar['nama_file']); ?>" 
                         style="max-width: 200px; height: auto; border-radius: 4px; margin-right: 10px; margin-bottom:10px; border: 1px solid #eee;">
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($kos['gambar_utama'])): // Fallback jika hanya ada gambar_utama ?>
             <img src="<?php echo htmlspecialchars($appConfig['ASSETS_URL'] . 'images/' . $kos['gambar_utama']); ?>" 
                 alt="Gambar <?php echo htmlspecialchars($kos['nama_kos']); ?>" 
                 style="max-width: 100%; height: auto; border-radius: 5px; margin-bottom: 20px; border: 1px solid #eee;">
        <?php else: ?>
            <img src="<?php echo htmlspecialchars($appConfig['ASSETS_URL'] . 'images/default-kos.jpg'); ?>" 
                 alt="Gambar Kos Default" 
                 style="max-width: 100%; height: auto; border-radius: 5px; margin-bottom: 20px; border: 1px solid #eee;">
        <?php endif; ?>
        
        <h3><?php echo htmlspecialchars($kos['nama_kos']); ?></h3>
        <p><strong>Alamat:</strong> <?php echo htmlspecialchars($kos['alamat']); ?></p>
        <p style="font-size: 1.2em; color: #007bff; font-weight: bold;">Harga: Rp <?php echo number_format($kos['harga_per_bulan'], 0, ',', '.'); ?> / bulan</p>
        
        <p>
            <strong>Status:</strong> 
            <span style="font-weight: bold; color: <?php 
                $status_kos_view = $kos['status_kos'] ?? 'maintenance';
                echo $status_kos_view === 'available' ? 'green' : ($status_kos_view === 'booked' ? 'red' : 'darkorange'); 
            ?>;">
                <?php echo ucfirst(htmlspecialchars($status_kos_view)); ?>
            </span>
        </p>
        <p>
            <strong>Kamar Tersedia:</strong> 
            <?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? 0); ?> dari <?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? 0); ?> unit
        </p>
        
        <h4 style="margin-top: 20px;">Deskripsi:</h4>
        <p><?php echo nl2br(htmlspecialchars($kos['deskripsi'] ?? 'Tidak ada deskripsi.')); ?></p>
        
        <h4 style="margin-top: 20px;">Fasilitas:</h4>
        <p><?php echo htmlspecialchars($kos['fasilitas_kos'] ?? 'Tidak ada informasi fasilitas.'); ?></p>

        <?php // Tombol Booking kondisional ?>
        <?php if ($kos['status_kos'] === 'available' && ($kos['jumlah_kamar_tersedia'] ?? 0) > 0): ?>
            <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'booking/pesan/' . $kos['id']); ?>" 
               style="display: inline-block; padding: 12px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; font-size: 1.1em;">
                Pesan Sekarang!
            </a>
        <?php elseif (($kos['jumlah_kamar_tersedia'] ?? 0) <= 0 || $kos['status_kos'] === 'booked'): ?>
            <p style="padding: 10px 15px; background-color: #ffe0b2; color: #856404; display: inline-block; border-radius: 5px; margin-top: 20px; border: 1px solid #ffc107;">
                Semua kamar sudah terpesan atau kos penuh.
            </p>
        <?php else: // maintenance atau status lain ?>
            <p style="padding: 10px 15px; background-color: #f8d7da; color: #721c24; display: inline-block; border-radius: 5px; margin-top: 20px; border: 1px solid #f5c6cb;">
                Saat ini tidak tersedia untuk dipesan (<?php echo htmlspecialchars($kos['status_kos']); ?>).
            </p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <p>Detail kos tidak ditemukan atau tidak valid.</p>
<?php endif; ?>

<p style="margin-top: 30px;">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/daftar'); ?>">Kembali ke Daftar Kos</a>
</p>