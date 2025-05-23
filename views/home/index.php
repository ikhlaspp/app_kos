<?php
// Variabel $pageTitle, $appConfig, dan $pesanSelamatDatang
// telah di-passing dari HomeController melalui BaseController::loadView().
?>

<div class="page-header">
    <h2><?php echo htmlspecialchars($pageTitle ?? 'Selamat Datang'); ?></h2>
</div>

<div class="content-section">
    <p><?php echo htmlspecialchars($pesanSelamatDatang ?? 'Selamat datang di aplikasi kami!'); ?></p>
    <p>Ini adalah halaman utama. Anda dapat menyesuaikan konten ini sesuai kebutuhan aplikasi booking kos Anda.</p>
    <p>Beberapa hal yang bisa ditampilkan di sini misalnya:</p>
    <ul>
        <li>Pencarian cepat kamar kos.</li>
        <li>Daftar kamar kos populer atau yang baru ditambahkan.</li>
        <li>Promo atau informasi penting lainnya.</li>
    </ul>
    <p>
        atau pelajari lebih lanjut <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'home/about'); ?>">tentang aplikasi ini</a>.
    </p>
</div>