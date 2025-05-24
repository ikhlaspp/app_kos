<?php

// $pageTitle, $appConfig tersedia. $email mungkin ada jika ada error sebelumnya.
?>
<h2><?php echo htmlspecialchars($pageTitle ?? 'Login'); ?></h2>

<form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'auth/login'); ?>" method="POST" style="max-width: 400px;">
    <div style="margin-bottom: 10px;">
        <label for="email" style="display: block; margin-bottom: 5px;">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="password" style="display: block; margin-bottom: 5px;">Password:</label>
        <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <button type="submit" style="padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Login</button>
</form>
<p style="margin-top: 15px;">
    Belum punya akun? <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'auth/register'); ?>">Registrasi di sini</a>.
</p>