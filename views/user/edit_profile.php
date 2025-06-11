<?php


?>

<h2><?php echo htmlspecialchars($pageTitle ?? 'Edit Profil'); ?></h2>

<form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'user/editProfile'); ?>" method="POST" style="max-width: 500px;">
    <div style="margin-bottom: 15px;">
        <label for="nama" style="display: block; margin-bottom: 5px;">Nama Lengkap:</label>
        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama'] ?? ''); ?>" required 
               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 15px;">
        <label for="email" style="display: block; margin-bottom: 5px;">Email (tidak dapat diubah):</label>
        <input type="email" id="email" name="email_display" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled 
               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background-color: #e9ecef;">
    </div>

    <div style="margin-bottom: 15px;">
        <label for="no_telepon" style="display: block; margin-bottom: 5px;">Nomor Telepon:</label>
        <input type="tel" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($user['no_telepon'] ?? ''); ?>" 
               placeholder="Contoh: 08123456789"
               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 20px;">
        <label for="alamat" style="display: block; margin-bottom: 5px;">Alamat:</label>
        <textarea id="alamat" name="alamat" rows="4" 
                  style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
    </div>

    <div style="margin-bottom: 15px;">
        <button type="submit" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Simpan Perubahan</button>
    </div>
</form>

<p><a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'user/dashboard'); ?>">Kembali ke Dashboard</a></p>