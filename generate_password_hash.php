<?php
// File: generate_password_hash.php

// ❗ GANTI 'password_admin_baru_yang_aman' dengan password yang Anda inginkan untuk admin
$passwordPlainText = 'admin123'; 

// Membuat hash password
$hashedPassword = password_hash($passwordPlainText, PASSWORD_DEFAULT);

echo "<p>Password Asli: " . htmlspecialchars($passwordPlainText) . "</p>";
echo "<p><strong>Password Hash (salin ini):</strong></p>";
echo "<textarea rows='3' cols='70' readonly>" . htmlspecialchars($hashedPassword) . "</textarea>";
echo "<hr>";
echo "<p><strong>Langkah Selanjutnya:</strong></p>";
echo "<ol>";
echo "<li>Salin seluruh teks Password Hash di atas.</li>";
echo "<li>Buka phpMyAdmin atau SQL client Anda.</li>";
echo "<li>Jalankan perintah SQL berikut (ganti 'ID_ADMIN_ANDA' dengan ID admin yang benar, misalnya 1, dan 'HASH_DARI_ATAS' dengan hash yang Anda salin):</li>";
echo "</ol>";
echo "<pre>UPDATE users SET password = 'HASH_DARI_ATAS' WHERE id = ID_ADMIN_ANDA;</pre>";
echo "<p>Contoh jika ID admin adalah 1:</p>";
echo "<pre>UPDATE users SET password = '" . htmlspecialchars($hashedPassword) . "' WHERE id = 1;</pre>";
echo "<p style='color:red; font-weight:bold;'>Setelah selesai, segera hapus file 'generate_password_hash.php' ini dari server Anda demi keamanan!</p>";

?>