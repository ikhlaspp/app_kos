<?php
// File: nama_proyek_kos/views/admin/kos/form.php
$isEditMode = ($mode === 'edit' && !empty($kos));
$gambarKos = $isEditMode ? ($kos['gambar_kos'] ?? []) : []; // Ambil array gambar jika ada
?>
<h2><?php echo htmlspecialchars($pageTitle); ?></h2>

<form action="<?php echo htmlspecialchars($formAction); ?>" method="POST" enctype="multipart/form-data" style="max-width: 700px;">
    <div style="margin-bottom: 15px;">
        <label for="nama_kos">Nama Kos:</label>
        <input type="text" id="nama_kos" name="nama_kos" value="<?php echo htmlspecialchars($kos['nama_kos'] ?? $_POST['nama_kos'] ?? ''); ?>" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="alamat">Alamat Lengkap:</label>
        <textarea id="alamat" name="alamat" rows="3" required style="width:100%; padding:8px;"><?php echo htmlspecialchars($kos['alamat'] ?? $_POST['alamat'] ?? ''); ?></textarea>
    </div>
     <div style="margin-bottom: 15px;">
        <label for="deskripsi">Deskripsi:</label>
        <textarea id="deskripsi" name="deskripsi" rows="5" style="width:100%; padding:8px;"><?php echo htmlspecialchars($kos['deskripsi'] ?? $_POST['deskripsi'] ?? ''); ?></textarea>
    </div>
    <div style="margin-bottom: 15px;">
        <label for="harga_per_bulan">Harga per Bulan (Rp):</label>
        <input type="number" step="1000" id="harga_per_bulan" name="harga_per_bulan" value="<?php echo htmlspecialchars($kos['harga_per_bulan'] ?? $_POST['harga_per_bulan'] ?? ''); ?>" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="fasilitas_kos">Fasilitas (pisahkan dengan koma):</label>
        <input type="text" id="fasilitas_kos" name="fasilitas_kos" value="<?php echo htmlspecialchars($kos['fasilitas_kos'] ?? $_POST['fasilitas_kos'] ?? ''); ?>" style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="jumlah_kamar_total">Jumlah Kamar Total:</label>
        <input type="number" min="0" id="jumlah_kamar_total" name="jumlah_kamar_total" value="<?php echo htmlspecialchars($kos['jumlah_kamar_total'] ?? $_POST['jumlah_kamar_total'] ?? '1'); ?>" required style="width:100%; padding:8px;">
    </div>

    <?php if ($isEditMode): ?>
    <div style="margin-bottom: 15px;">
        <label for="jumlah_kamar_tersedia">Jumlah Kamar Tersedia:</label>
        <input type="number" min="0" id="jumlah_kamar_tersedia" name="jumlah_kamar_tersedia" value="<?php echo htmlspecialchars($kos['jumlah_kamar_tersedia'] ?? $_POST['jumlah_kamar_tersedia'] ?? '0'); ?>" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="status_kos">Status Kos:</label>
        <select id="status_kos" name="status_kos" style="width:100%; padding:8px;">
            <option value="available" <?php echo (($kos['status_kos'] ?? '') === 'available') ? 'selected' : ''; ?>>Available</option>
            <option value="booked" <?php echo (($kos['status_kos'] ?? '') === 'booked') ? 'selected' : ''; ?>>Booked</option>
            <option value="maintenance" <?php echo (($kos['status_kos'] ?? '') === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
        </select>
    </div>
    <?php endif; ?>

    <div style="margin-bottom: 20px; padding:15px; border:1px dashed #ccc; border-radius:5px;">
        <label for="gambar_kos_baru" style="display:block; margin-bottom:10px; font-weight:bold;">Upload Gambar Baru (Bisa lebih dari satu):</label>
        <input type="file" id="gambar_kos_baru" name="gambar_kos_baru[]" multiple accept="image/jpeg, image/png, image/gif, image/webp">
        <small style="display:block; margin-top:5px;">Tipe file yang diizinkan: JPG, PNG, GIF, WEBP. Maksimal 5MB per file.</small>
    </div>

    <?php if ($isEditMode && !empty($gambarKos)): ?>
    <div style="margin-bottom: 20px;">
        <h4 style="margin-bottom:10px;">Gambar yang Sudah Ada:</h4>
        <div style="display:flex; flex-wrap:wrap; gap:10px;">
            <?php foreach ($gambarKos as $gambar): ?>
                <div style="border:1px solid #eee; padding:5px; border-radius:4px; text-align:center; width: 150px;">
                    <img src="<?php echo htmlspecialchars($appConfig['UPLOADS_URL_PATH'] . $gambar['path']); ?>" 
                         alt="<?php echo htmlspecialchars($gambar['nama_file']); ?>" 
                         style="width:100%; height:100px; object-fit:cover; margin-bottom:5px;">
                    <p style="font-size:0.8em; margin-bottom:5px; word-break:break-all;"><?php echo htmlspecialchars($gambar['nama_file']); ?></p>
                    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kosDeleteGambar/' . $gambar['id'] . '/' . $kos['id']); ?>" 
                       onclick="return confirm('Anda yakin ingin menghapus gambar ini?');"
                       style="color:red; font-size:0.8em; text-decoration:none;">Hapus Gambar</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php elseif ($isEditMode): ?>
    <p>Belum ada gambar untuk kos ini.</p>
    <?php endif; ?>


    <button type="submit" style="padding:10px 20px; background-color:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">
        <?php echo $isEditMode ? 'Simpan Perubahan Kos' : 'Tambah Kos Baru'; ?>
    </button>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" style="margin-left:10px; text-decoration:none;">Batal</a>
</form>