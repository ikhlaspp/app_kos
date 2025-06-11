<?php

if (!isset($appConfig) && file_exists(__DIR__ . '/../config/app.php')) {
    $appConfig = require __DIR__ . '/../config/app.php';
}

$currentYear = date('Y');
$appName = $appConfig['APP_NAME'] ?? 'Aplikasi Booking Kos';
$baseUrl = $appConfig['BASE_URL'] ?? '/';
$assetsUrl = $appConfig['ASSETS_URL'] ?? rtrim($baseUrl, '/') . '/assets/';

$contactEmail = $appConfig['APP_EMAIL'] ?? 'info@bookingkos.com';
$contactPhone = $appConfig['APP_PHONE'] ?? '+62 123 456 7890';
$facebookUrl = $appConfig['FACEBOOK_URL'] ?? '#';
$instagramUrl = $appConfig['INSTAGRAM_URL'] ?? '#';
$twitterUrl = $appConfig['TWITTER_URL'] ?? '#';

?>
<style>
    /* Styling untuk Sticky Footer */
    html {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* Tinggi body minimal setinggi layar */
        margin: 0; /* Hapus margin default dari browser */
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; /* Default font, sesuaikan jika perlu */
        background-color: #F9F7F7; /* Warna latar body dari palet */
        color: #112D4E; /* Warna teks default dari palet */
    }

    /* Asumsikan Anda memiliki elemen <main> yang membungkus konten utama halaman Anda, */
    /* yang berada di antara header dan footer. */
    /* Jika Anda menggunakan class lain (misalnya .content-wrapper), sesuaikan selector ini. */
    main {
        flex: 1 0 auto; /* Ini membuat <main> tumbuh mengisi ruang yang tersedia */
    }
    /* Jika header Anda bukan bagian dari flex item body secara langsung atau butuh perlakuan khusus */
    /* header { flex-shrink: 0; } */


    /* Styling Footer yang sudah ada sebelumnya */
    .site-footer {
        background-color: #DBE2EF; /* Biru Langit Muda sebagai background footer */
        color: #112D4E; /* Biru Tua untuk teks utama */
        padding: 40px 0;
        font-size: 0.9em;
        line-height: 1.6;
        flex-shrink: 0; /* Mencegah footer menyusut jika konten terlalu banyak */
    }

    .site-footer .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .footer-widgets-area {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 30px;
        margin-bottom: 30px;
    }

    .footer-widget {
        flex: 1;
        min-width: 220px;
        margin-bottom: 20px;
    }

    .footer-widget h4 {
        color: #112D4E;
        font-size: 1.1em;
        margin-top: 0;
        margin-bottom: 15px;
        border-bottom: 2px solid #3F72AF;
        padding-bottom: 8px;
        display: inline-block;
    }

    .footer-widget ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-widget ul li {
        margin-bottom: 8px;
    }

    .footer-widget ul li a {
        color: #3F72AF;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-widget ul li a:hover {
        color: #112D4E;
        text-decoration: underline;
    }

    .footer-widget p {
        margin-bottom: 10px;
    }

    .footer-contact-info .contact-item {
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }
    .footer-contact-info .contact-item svg {
        margin-right: 8px;
        fill: #3F72AF;
        width: 16px;
        height: 16px;
    }

    .footer-social-links a {
        display: inline-block;
        margin-right: 10px;
        color: #3F72AF;
        font-size: 1.5em;
        transition: color 0.3s ease;
    }
    .footer-social-links a svg {
        width: 24px;
        height: 24px;
        fill: currentColor;
    }

    .footer-social-links a:hover {
        color: #112D4E;
    }

    .footer-bottom {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #B0C4DE;
        color: #112D4E;
    }

    .footer-bottom p {
        margin: 0;
    }

    @media (max-width: 768px) {
        .footer-widgets-area {
            flex-direction: column;
            align-items: flex-start;
        }
        .footer-widget {
            width: 100%;
            min-width: unset;
        }
    }
</style>

<?php
// Bagian HTML penutup dari konten utama Anda.
// Pastikan ada elemen <main> (atau elemen pembungkus konten utama Anda)
// yang ditutup di sini agar Flexbox bisa bekerja dengan benar.
?>
            </div> </main> </div> <?php // Tag </main> di sini penting untuk Flexbox ?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-widgets-area">
            <div class="footer-widget footer-about">
                <h4>Tentang <?php echo htmlspecialchars($appName); ?></h4>
                <p>Kami menyediakan solusi mudah dan cepat untuk menemukan dan memesan kos idaman Anda. Jelajahi berbagai pilihan kos dengan fasilitas lengkap dan harga terbaik.</p>
            </div>

            <div class="footer-widget footer-navigation">
                <h4>Navigasi Cepat</h4>
                <ul>
                    <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Beranda</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>">Daftar Kos</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl . 'home/about'); ?>">Tentang Kami</a></li>
                </ul>
            </div>

            <div class="footer-widget footer-contact-info">
                <h4>Hubungi Kami</h4>
                <div class="contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>"><?php echo htmlspecialchars($contactEmail); ?></a>
                </div>
                <div class="contact-item">
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                    <span><?php echo htmlspecialchars($contactPhone); ?></span>
                </div>
                 <?php if (!empty($appConfig['APP_ADDRESS'])): ?>
                <div class="contact-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    <span><?php echo htmlspecialchars($appConfig['APP_ADDRESS']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="footer-widget footer-social">
                <h4>Ikuti Kami</h4>
                <div class="footer-social-links">
                    <a href="<?php echo htmlspecialchars($facebookUrl); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <svg viewBox="0 0 24 24"><path d="M12 2.04C6.5 2.04 2 6.53 2 12.06c0 4.98 3.66 9.13 8.44 9.9v-7H7.9v-2.9h2.54V9.84c0-2.5 1.48-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7c4.78-.77 8.44-4.92 8.44-9.9C22 6.53 17.5 2.04 12 2.04z"/></svg>
                    </a>
                    <a href="<?php echo htmlspecialchars($instagramUrl); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <svg viewBox="0 0 24 24"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/></svg>
                    </a>
                    <a href="<?php echo htmlspecialchars($twitterUrl); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                        <svg viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.67.9-.53 1.59-1.37 1.92-2.38-.84.5-1.78.86-2.79 1.07C18.25 4.49 17.05 4 15.72 4c-2.38 0-4.31 1.94-4.31 4.31 0 .34.04.67.11.98C7.95 9.09 4.76 7.38 2.57 4.76c-.37.63-.58 1.37-.58 2.15 0 1.5.77 2.82 1.94 3.59-.71-.02-1.38-.22-1.97-.54v.05c0 2.09 1.49 3.84 3.46 4.23-.36.1-.74.15-1.13.15-.28 0-.55-.03-.81-.08.55 1.71 2.14 2.96 4.02 3-1.48 1.16-3.35 1.85-5.38 1.85-.35 0-.69-.02-1.03-.06 1.92 1.23 4.2 1.95 6.66 1.95 7.99 0 12.36-6.62 12.36-12.36 0-.19 0-.38-.01-.56.85-.61 1.58-1.37 2.16-2.24z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo $currentYear; ?> <?php echo htmlspecialchars($appName); ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<?php
if (isset($_SESSION['user_id']) && isset($appConfig) && is_array($appConfig)) {
    $chatUiPath = __DIR__ . '/chat_ui.php';
    if (file_exists($chatUiPath)) {
        require_once $chatUiPath;
    } else {
        error_log("Peringatan Kritis: File chat_ui.php tidak ditemukan di " . $chatUiPath);
    }
}
?>

</body>
</html>
