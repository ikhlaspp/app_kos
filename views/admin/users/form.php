<?php

// Variabel $pageTitle, $appConfig, $formAction, $user
?>
<h2><?php echo htmlspecialchars($pageTitle); ?></h2>

<form action="<?php echo htmlspecialchars($formAction); ?>" method="POST" style="max-width: 600px;">
    <div style="margin-bottom: 15px;">
        <label for="nama" style="display:block; margin-bottom:5px;">Nama Lengkap:</label>
        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama'] ?? ''); ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="email_display" style="display:block; margin-bottom:5px;">Email (tidak dapat diubah oleh admin di sini):</label>
        <input type="email" id="email_display" name="email_display" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; background-color:#e9ecef;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="no_telepon" style="display:block; margin-bottom:5px;">Nomor Telepon:</label>
        <input type="tel" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($user['no_telepon'] ?? ''); ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="alamat" style="display:block; margin-bottom:5px;">Alamat:</label>
        <textarea id="alamat" name="alamat" rows="3" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
    </div>
    <div style="margin-bottom: 20px;">
        <label for="is_admin" style="display:block; margin-bottom:5px;">Status Admin:</label>
        <input type="hidden" name="is_admin" value="0"> <input type="checkbox" id="is_admin" name="is_admin" value="1" <?php echo !empty($user['is_admin']) ? 'checked' : ''; ?>>
        <label for="is_admin">Jadikan sebagai Admin</label>
    </div>

    <button type="submit" style="padding:10px 20px; background-color:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">Simpan Perubahan</button>
    <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>" style="margin-left:10px; text-decoration:none;">Batal</a>
</form>