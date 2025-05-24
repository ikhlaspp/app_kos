<?php

?>
<h2><?php echo htmlspecialchars($pageTitle ?? 'Admin Dashboard'); ?></h2>
<p>Selamat datang di area administrasi, <?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?>!</p>

<ul style="list-style-type: none; padding: 0;">
    <li style="margin-bottom: 10px;">
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/kos'); ?>" style="font-size: 1.2em; text-decoration: none; color: #007bff; padding: 10px; border: 1px solid #007bff; border-radius: 4px; display: inline-block;">
            Kelola Data Kos
        </a>
    </li>
    <li style="margin-bottom: 10px;">
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/users'); ?>" style="font-size: 1.2em; text-decoration: none; color: #007bff; /* Diaktifkan */ padding: 10px; border: 1px solid #007bff; border-radius: 4px; display: inline-block;">
            Kelola Pengguna
        </a>
    </li>
    <li style="margin-bottom: 10px;">
        <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'admin/bookings'); ?>" style="font-size: 1.2em; text-decoration: none; color: #007bff; /* Diaktifkan */ padding: 10px; border: 1px solid #007bff; border-radius: 4px; display: inline-block;">
            Kelola Pemesanan
        </a>
    </li>
</ul>