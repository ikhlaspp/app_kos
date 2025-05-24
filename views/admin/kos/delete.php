<?php

// Variabel $pageTitle, $appConfig, $kos, $formAction tersedia
?>
<h2><?php echo htmlspecialchars($pageTitle ?? 'Konfirmasi Penghapusan'); ?></h2>

<div style="padding: 15px; border: 1px solid #dc3545; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
    <p>Anda yakin ingin menghapus data kos berikut secara permanen?</p>
    <p><strong>ID Kos:</strong> <?php echo htmlspecialchars($kos['id']); ?></p>
    <p><strong>Nama Kos:</strong> <?php echo htmlspecialchars($kos['nama_kos']); ?></p>
    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($kos['alamat']); ?></p>
    <p style="font-weight: bold; margin-top:10px;">Perhatian: Semua data booking yang terkait dengan kos ini juga akan terhapus (jika database diatur dengan ON DELETE CASCADE).</p>
</div>

<form action="<?php echo htmlspecialchars($formAction); ?>" method="POST" style="display:inline-block; margin-right: 10px;">
    <button type="submit" style="padding: 10px 20px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
        Ya, Hapus Data Ini
    </button>
</form>

<a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" style="padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px;">
    Batal
</a>