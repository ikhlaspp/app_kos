<?php

// Variabel $pageTitle, $appConfig, $formAction, $kos (bisa null untuk create), $mode ('create' atau 'edit')
$isEditMode = ($mode === 'edit' && !empty($kos));
?>
<h2><?php echo htmlspecialchars($pageTitle); ?></h2>

<form action="<?php echo htmlspecialchars($formAction); ?>" method="POST" style="max-width: 700px;">
    <div style="margin-bottom: 15px;">
        <label for="nama_kos" style="display:block; margin-bottom:5px;">Nama Kos:</label>
        <input type="text" id="nama_kos" name="nama_kos" value="<?php echo htmlspecialchars($kos['nama_kos'] ?? $_POST['nama_kos'] ?? ''); ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="alamat" style="display:block; margin-bottom:5px;">Alamat Lengkap:</label>
        <textarea id="alamat" name="alamat" rows="3" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"><?php echo htmlspecialchars($kos['alamat'] ?? $_POST['alamat'] ?? ''); ?></textarea>
    </div>
    <div style="margin-bottom: 15px;">
        <label for="deskripsi" style="display:block; margin-bottom:5px;">Deskripsi:</label>
        <textarea id="deskripsi" name="deskripsi" rows="5" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"><?php echo htmlspecialchars($kos['deskripsi'] ?? $_POST['deskripsi'] ?? ''); ?></textarea>
    </div>
    <div style="margin-bottom: 15px;">
        <label for="harga_per_bulan" style="display:block; margin-bottom:5px;">Harga per Bulan (Rp):</label>
        <input type="number" step="1000" id="harga_per_bulan" name="harga_per_bulan" value="<?php echo htmlspecialchars($kos['harga_per_bulan'] ?? $_POST['harga_per_bulan'] ?? ''); ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="fasilitas_kos" style="display:block; margin-bottom:5px;">Fasilitas (pisahkan dengan koma):</label>
        <input type="text" id="fasilitas_kos" name="fasilitas_kos" value="<?php echo htmlspecialchars($kos['fasilitas_kos'] ?? $_POST['fasilitas_kos'] ?? ''); ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="jumlah_kamar_total" style="display:block; margin-bottom:5px;">Jumlah Kamar Total:</label>
        <input type="number" min="0" id="jumlah_kamar_total" name="jumlah_kamar_total" value="<?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? $_POST['jumlah_kamar_total'] ?? '1'); ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>
    
    <?php if ($isEditMode): ?>
    <div style="margin-bottom: 15px;">
        <label for="jumlah_kamar_tersedia" style="display:block; margin-bottom:5px;">Jumlah Kamar Tersedia:</label>
        <input type="number" min="0" id="jumlah_kamar_tersedia" name="jumlah_kamar_tersedia" value="<?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? $_POST['jumlah_kamar_tersedia'] ?? '0'); ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="status_kos" style="display:block; margin-bottom:5px;">Status Kos:</label>
        <select id="status_kos" name="status_kos" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            <option value="available" <?php echo (($kos['status_kos'] ?? '') === 'available') ? 'selected' : ''; ?>>Available</option>
            <option value="booked" <?php echo (($kos['status_kos'] ?? '') === 'booked') ? 'selected' : ''; ?>>Booked</option>
            <option value="maintenance" <?php echo (($kos['status_kos'] ?? '') === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
        </select>
    </div>
    <?php endif; ?>

    <button type="submit" style="padding:10px 20px; background-color:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">
        <?php echo $isEditMode ? 'Simpan Perubahan' : 'Tambah Kos'; ?>
    </button>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" style="margin-left:10px; text-decoration:none;">Batal</a>
</form>