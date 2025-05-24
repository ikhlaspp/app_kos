<?php

// $pageTitle, $appConfig tersedia. Variabel input lain mungkin ada jika ada error sebelumnya.
?>
<h2><?php echo htmlspecialchars($pageTitle ?? 'Registrasi'); ?></h2>

<form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'auth/register'); ?>" method="POST" style="max-width: 500px;">
    <div style="margin-bottom: 10px;">
        <label for="nama" style="display: block; margin-bottom: 5px;">Nama Lengkap:</label>
        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($nama ?? ''); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="email" style="display: block; margin-bottom: 5px;">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="password" style="display: block; margin-bottom: 5px;">Password (min. 6 karakter):</label>
        <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="password_confirm" style="display: block; margin-bottom: 5px;">Konfirmasi Password:</label>
        <input type="password" id="password_confirm" name="password_confirm" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="no_telepon" style="display: block; margin-bottom: 5px;">No. Telepon (Opsional):</label>
        <input type="text" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($no_telepon ?? ''); ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="alamat" style="display: block; margin-bottom: 5px;">Alamat (Opsional):</label>
        <textarea id="alamat" name="alamat" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"><?php echo htmlspecialchars($alamat ?? ''); ?></textarea>
    </div>
    <button type="submit" style="padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Registrasi</button>
</form>
<p style="margin-top: 15px;">
    Sudah punya akun? <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'auth/login'); ?>">Login di sini</a>.
</p>