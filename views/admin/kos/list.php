<?php

?>
<h2><?php echo htmlspecialchars($pageTitle ?? 'Kelola Data Kos'); ?></h2>

<p style="margin-bottom: 20px;">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosCreate'); ?>" style="padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;">
        + Tambah Kos Baru
    </a>
</p>

<?php if (!empty($daftarKos)): ?>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px; border: 1px solid #ddd; text-align:left;">ID</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:left;">Nama Kos</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:left;">Harga/Bulan</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:center;">Total Kamar</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:center;">Kamar Tersedia</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:left;">Status</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($daftarKos as $kos): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($kos['id']); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($kos['nama_kos']); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd;">Rp <?php echo number_format($kos['harga_per_bulan'], 0, ',', '.'); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align:center;"><?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? 0); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align:center;"><?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? 0); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars(ucfirst($kos['status_kos'])); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align:center;">
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosEdit/' . $kos['id']); ?>" style="text-decoration:none; color: #007bff; margin-right:10px;">Edit</a>
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosDelete/' . $kos['id']); ?>" 
                       style="text-decoration:none; color: #dc3545;">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada data kos.</p>
<?php endif; ?>