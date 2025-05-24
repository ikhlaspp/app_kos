<?php

?>
<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle ?? 'Daftar Kos Tersedia'); ?></h2>
</div>

<?php if (isset($appConfig) && isset($daftar_kos)): ?>
    <?php if (!empty($daftar_kos)): ?>
        <div class="kos-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
            <?php foreach ($daftar_kos as $item_kos): ?>
                <div class="kos-item" style="border: 1px solid #e0e0e0; padding: 20px; border-radius: 8px; background-color: #ffffff; box-shadow: 0 4px 8px rgba(0,0,0,0.05); transition: box-shadow 0.3s ease;">
                    <?php if (!empty($item_kos['gambar_utama'])): ?>
                        <img src="<?php echo htmlspecialchars($appConfig['BASE_URL'] . ($item_kos['gambar_utama'])); ?>" 
                             alt="Gambar <?php echo htmlspecialchars($item_kos['nama_kos']); ?>" 
                             style="width: 100%; height: 180px; object-fit: cover; border-radius: 6px; margin-bottom: 15px;">
                    <?php else: ?>
                        <div style="width: 100%; height: 180px; background-color: #f0f0f0; border-radius: 6px; margin-bottom: 15px; display:flex; align-items:center; justify-content:center; color:#aaa;">
                            <span>Tidak ada gambar</span>
                        </div>
                    <?php endif; ?>

                    <h3 style="margin-top:0; margin-bottom: 8px; font-size: 1.25em; color: #333;">
                        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $item_kos['id']); ?>" style="text-decoration: none; color: #0056b3;">
                            <?php echo htmlspecialchars($item_kos['nama_kos']); ?>
                        </a>
                    </h3>
                    <p style="font-size: 0.9em; color: #555; margin-bottom: 8px;"><small><strong>Alamat:</strong> <?php echo htmlspecialchars($item_kos['alamat']); ?></small></p>
                    <p style="font-size: 1.1em; color: #007bff; font-weight: bold; margin-bottom: 8px;">Rp <?php echo number_format($item_kos['harga_per_bulan'], 0, ',', '.'); ?> / bulan</p>
                    <p style="font-size: 0.85em; color: #666; margin-bottom: 8px;">Ketersediaan: <strong><?php echo htmlspecialchars($item_kos['jumlah_kamar_tersedia'] ?? 0); ?></strong> / <?php echo htmlspecialchars($item_kos['jumlah_kamar_total'] ?? 0); ?> kamar</p>
                    <p style="font-size: 0.85em; margin-bottom: 15px;">
                        Status: 
                        <span style="font-weight: bold; padding: 3px 8px; border-radius: 4px; color: white; background-color: <?php 
                            $statusValue = $item_kos['status_kos'] ?? 'maintenance'; 
                            $bgColor = '#6c757d'; $textColor = 'white';
                            switch ($statusValue) {
                                case 'available': $bgColor = '#28a745'; break; 
                                case 'booked': $bgColor = '#dc3545'; break;    
                                case 'maintenance': $bgColor = '#ffc107'; $textColor = '#212529'; break; 
                            } echo $bgColor;
                        ?>; color: <?php echo $textColor; ?>;">
                            <?php echo ucfirst(htmlspecialchars($item_kos['status_kos'] ?? 'N/A')); ?>
                        </span>
                    </p>
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'kos/detail/' . $item_kos['id']); ?>" style="display: inline-block; padding: 10px 18px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 0.9em; transition: background-color 0.2s;">Lihat Detail</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Belum ada data kos yang tersedia saat ini.</p>
    <?php endif; ?>
    <p style="margin-top: 30px;"><a href="<?php echo htmlspecialchars($appConfig['BASE_URL']); ?>">Kembali ke Halaman Utama</a></p>
<?php else: ?>
    <p>Data konfigurasi aplikasi atau data kos tidak tersedia.</p>
<?php endif; ?>