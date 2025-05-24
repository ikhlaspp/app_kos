<?php

?>
<h2><?php echo htmlspecialchars($pageTitle ?? 'Kelola Pengguna'); ?></h2>
<?php /* Tombol tambah pengguna oleh admin bisa ditambahkan nanti jika perlu 
<p style="margin-bottom: 20px;">
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/userCreate'); ?>" style="padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;">
        + Tambah Pengguna Baru
    </a>
</p>
*/ ?>
<?php if (!empty($daftarPengguna)): ?>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px; border: 1px solid #ddd; text-align:left;">ID</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:left;">Nama</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:left;">Email</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:left;">No. Telepon</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:center;">Admin?</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($daftarPengguna as $pengguna): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($pengguna['id']); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($pengguna['nama']); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($pengguna['email']); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($pengguna['no_telepon'] ?? '-'); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align:center;"><?php echo $pengguna['is_admin'] ? 'Ya' : 'Tidak'; ?></td>
                <td style="padding: 8px; border: 1px solid #ddd; text-align:center;">
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/userEdit/' . $pengguna['id']); ?>" style="text-decoration:none; color: #007bff;">Edit</a>
                    <?php /* Link Hapus Pengguna akan dibuat nanti 
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/userDelete/' . $pengguna['id']); ?>" 
                       onclick="return confirm('Anda yakin ingin menghapus pengguna ini?');" 
                       style="text-decoration:none; color: #dc3545; margin-left:10px;">Hapus</a>
                    */ ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada data pengguna.</p>
<?php endif; ?>