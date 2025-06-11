<?php

?>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle ?? 'Tentang Aplikasi'); ?></h2>
</div>

<div class="content-section">
    <p><strong>Nama Aplikasi:</strong> <?php echo htmlspecialchars($namaAplikasi ?? '[Nama Aplikasi Tidak Ditemukan]'); ?></p>
    <p><strong>Versi:</strong> <?php echo htmlspecialchars($versi ?? 'N/A'); ?></p>
    <p><strong>Deskripsi:</strong></p>
    <p><?php echo nl2br(htmlspecialchars($deskripsi ?? 'Tidak ada deskripsi yang tersedia.')); ?></p>
    
    <hr style="margin-top: 20px; margin-bottom: 20px;">
    
    <p>Aplikasi ini bertujuan untuk memberikan solusi mudah dan cepat bagi para pencari kos dan pemilik kos.</p>
    
</div>