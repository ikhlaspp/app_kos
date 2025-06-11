<?php

// --- Configuration Variable Initialization ---
// Sets the page title
$pageTitle = $pageTitle ?? 'Sistem Booking Kos';

// Sets the welcome message
$pesanSelamatDatang = $pesanSelamatDatang ?? 'Temukan Kos Impian Anda Bersama Kami!';

// Retrieves the application name from configuration
$namaAplikasi = $appConfig['APP_NAME'] ?? 'KosKita';

// Retrieves the base URL from configuration
$baseUrl = $appConfig['BASE_URL'] ?? './';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - <?php echo htmlspecialchars($namaAplikasi); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Base body styles, sets font family, background, and default text color */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F0F4FF;
            color: #0D2A57;
        }

        /* Hero Section Styles */
        .hero-section {
            background: linear-gradient(135deg, #4285F4, #1C3A6E);
            color: #FFFFFF;
            padding: 6rem 1rem;
            text-align: center;
            border-radius: 50px;
            margin-bottom: 2rem;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        /* Hero Section Buttons */
        .hero-section .btn-custom-primary {
            background-color: #FFFFFF;
            color: #1C3A6E;
            border: 2px solid #FFFFFF;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .hero-section .btn-custom-primary:hover {
            background-color: transparent;
            color: #FFFFFF;
            border-color: #FFFFFF;
        }

        .hero-section .btn-custom-secondary {
            background-color: transparent;
            color: #FFFFFF;
            border: 2px solid #D6E4FF;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        .hero-section .btn-custom-secondary:hover {
            background-color: #D6E4FF;
            color: #1C3A6E;
        }

        /* Page Header Styles (for main content sections) */
        .page-header h2 {
            color: #1C3A6E;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        /* General Content Section Padding */
        .content-section {
            padding: 2rem 0;
        }

        /* Feature Card Styles */
        .feature-card {
            background-color: #FFFFFF;
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(28, 58, 110, 0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%; /* Ensures cards in a row have equal height */
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(28, 58, 110, 0.15);
        }

        .feature-card .card-body {
            padding: 2rem;
        }

        .feature-card .card-title {
            color: #1C3A6E;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .feature-card .card-text, .feature-card ul li {
            color: #0D2A57;
            font-weight: 400;
        }

        .feature-card i {
            font-size: 2rem;
            color: #4285F4;
            margin-bottom: 1rem;
            display: block; /* Ensures icon is on its own line */
        }

        .feature-card .btn-outline-custom {
            color: #4285F4;
            border-color: #4285F4;
            font-weight: 600;
            border-radius: 50px;
        }

        .feature-card .btn-outline-custom:hover {
            background-color: #4285F4;
            color: #FFFFFF;
        }

        /* Promo Section Styles */
        .promo-section {
            background-color: #D6E4FF;
            padding: 3rem 0;
            border-radius: 20px;
            margin-top: 2rem;
        }

        .promo-section h3 {
            color: #0D2A57;
            font-weight: 700;
        }

        .promo-section p {
            color: #1C3A6E;
        }

        .promo-section .btn-promo {
            background-color: #1C3A6E;
            color: #FFFFFF;
            font-weight: 600;
            padding: 0.6rem 1.8rem;
            border-radius: 50px;
            border: 2px solid #1C3A6E;
        }

        .promo-section .btn-promo:hover {
            background-color: #0D2A57;
            border-color: #0D2A57;
        }

        /* Additional Information Section Styles (Accordion) */
        .additional-info-section {
            background-color: #FFFFFF;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(28, 58, 110, 0.12);
            margin-top: 3rem;
        }

        .additional-info-section h3 {
            color: #1C3A6E;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .additional-info-section p {
            color: #0D2A57;
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }

        .additional-info-section .accordion-item {
            background-color: #F8F9FA;
            border: 1px solid #D6E4FF;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .additional-info-section .accordion-button {
            color: #1C3A6E;
            font-weight: 600;
            background-color: #E9F0FF;
            border-radius: calc(0.5rem - 1px);
        }

        .additional-info-section .accordion-button:not(.collapsed) {
            background-color: #D6E4FF;
            color: #0D2A57;
            box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
        }

        .additional-info-section .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(66, 133, 244, 0.25);
        }

        .additional-info-section .accordion-body {
            color: #0D2A57;
            background-color: #FFFFFF;
            padding: 1.25rem;
        }

        .additional-info-section .accordion-button::after {
            filter: invert(15%) sepia(45%) saturate(2500%) hue-rotate(210deg) brightness(90%) contrast(95%);
        }

        /* Learn More Button in Additional Info Section */
        .additional-info-section .btn-learn-more {
            background-color: #4285F4;
            color: white;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border: none;
            transition: background-color 0.3s ease;
        }

        .additional-info-section .btn-learn-more:hover {
            background-color: #1C3A6E;
        }

        /* Footer Styles */
        .footer-custom {
            background-color: #0D2A57;
            color: #D6E4FF;
            padding: 2rem 0;
            margin-top: 3rem;
            text-align: center;
        }

        .footer-custom p {
            margin-bottom: 0;
            font-weight: 300;
        }

        /* --- Responsive Adjustments (Media Queries) --- */

        /* Styles for screens smaller than 768px (e.g., tablets) */
        @media (max-width: 768px) {
            .hero-section h1 { font-size: 2.2rem; }
            .hero-section p { font-size: 1rem; }
            .hero-section .btn-custom-primary, .hero-section .btn-custom-secondary {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
                display: block; /* Stack buttons vertically */
                margin-bottom: 10px;
            }
            .hero-section .btn-custom-secondary { margin-left: 0; } /* Remove left margin for stacked buttons */
            .feature-card .card-body { padding: 1.5rem; }
            .additional-info-section { padding: 1.5rem; }
        }

        /* Styles for screens smaller than 576px (e.g., mobile phones) */
        @media (max-width: 576px) {
            .hero-section { padding: 3rem 1rem; border-radius: 0 0 25px 25px;} /* Adjust padding and border radius */
            .promo-section { padding: 2rem 1rem; }
        }
    </style>
</head>
<body>

 <header class="hero-section">
    <div class="container">
        <h1><?php echo htmlspecialchars($pesanSelamatDatang); ?></h1>
        <p>Platform terpercaya untuk menemukan kos idaman dengan mudah dan cepat. Jelajahi berbagai pilihan kos berkualitas di lokasi strategis.</p>
        <a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>" class="btn btn-custom-primary"><i class="fas fa-search me-2"></i>Mulai Cari Kos</a>
        <a href="#fitur" class="btn btn-custom-secondary">Lihat Fitur</a>
    </div>
</header>

<div class="container content-section">
    <div id="fitur" class="text-center mb-5 pt-4">
        <h2 style="color: #1C3A6E; font-weight: 700;">Kenapa Memilih <?php echo htmlspecialchars($namaAplikasi); ?>?</h2>
        <p class="lead" style="color: #0D2A57;">Kami menyediakan fitur terbaik untuk pengalaman mencari kos yang tak terlupakan.</p>
    </div>

    <div class="row">
        <div class="col-md-4 d-flex align-items-stretch">
            <div class="card feature-card text-center">
                <div class="card-body">
                    <i class="fas fa-search-location"></i>
                    <h5 class="card-title">Pencarian Cepat & Akurat</h5>
                    <p class="card-text">Temukan kamar kos impian Anda dengan filter pencarian yang lengkap dan hasil yang relevan.</p>
                    <a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>" class="btn btn-outline-custom mt-auto">Cari Sekarang</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex align-items-stretch">
            <div class="card feature-card text-center">
                <div class="card-body">
                    <i class="fas fa-star"></i>
                    <h5 class="card-title">Kos Populer & Terverifikasi</h5>
                    <p class="card-text">Lihat daftar kamar kos populer pilihan pengguna atau yang baru saja ditambahkan oleh pemilik.</p>
                    <a href="<?php echo htmlspecialchars($baseUrl . 'kos/daftar'); ?>" class="btn btn-outline-custom mt-auto">Lihat Populer</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex align-items-stretch">
            <div class="card feature-card text-center">
                <div class="card-body">
                    <i class="fas fa-tags"></i>
                    <h5 class="card-title">Promo & Informasi Terbaru</h5>
                    <p class="card-text">Jangan lewatkan promo menarik dan informasi penting lainnya seputar dunia kos.</p>
                    <a href="<?php echo htmlspecialchars($baseUrl . 'user/dashboard'); ?>" class="btn btn-outline-custom mt-auto">Cek Promo</a>
                </div>
            </div>
        </div>
    </div>

    <div class="additional-info-section">
        <h3><i class="fas fa-info-circle me-2" style="color: #4285F4;"></i>Informasi Tambahan</h3>
        <p><?php echo htmlspecialchars($pesanSelamatDatang); ?></p>
        <p>Ini adalah halaman utama yang dirancang untuk memberikan kemudahan bagi Anda dalam menemukan kos idaman. Anda dapat menyesuaikan konten ini lebih lanjut sesuai dengan fitur spesifik aplikasi booking kos Anda.</p>
        <p class="mb-3">Beberapa hal menarik yang bisa Anda temukan atau tampilkan di sini misalnya:</p>

        <div class="accordion" id="infoAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <i class="fas fa-bolt me-2"></i>Pencarian Cepat Kamar Kos
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#infoAccordion">
                    <div class="accordion-body">
                        Manfaatkan fitur pencarian instan kami untuk menemukan kos berdasarkan lokasi, harga, fasilitas, dan preferensi lainnya. Hemat waktu Anda dengan hasil yang akurat dan relevan.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                       <i class="fas fa-medal me-2"></i>Daftar Kos Populer & Terbaru
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#infoAccordion">
                    <div class="accordion-body">
                        Jelajahi daftar kos yang sedang tren atau baru saja ditambahkan oleh pemilik terverifikasi. Dapatkan inspirasi dan temukan tempat tinggal yang paling sesuai dengan gaya hidup Anda.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <i class="fas fa-bullhorn me-2"></i>Promo & Informasi Penting
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#infoAccordion">
                    <div class="accordion-body">
                        Jangan lewatkan berbagai penawaran spesial, diskon, dan informasi penting lainnya seputar dunia kos. Kami selalu memperbarui info agar Anda mendapatkan keuntungan maksimal.
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" class="btn btn-learn-more"><i class="fas fa-arrow-right me-2"></i>Pelajari Lebih Lanjut</button>
        </div>
    </div>

    <section class="promo-section text-center mt-5">
        <div class="container">
            <h3><i class="fas fa-gift me-2"></i> Dapatkan Diskon Spesial!</h3>
            <p class="my-3">Daftar sekarang dan dapatkan diskon hingga 20% untuk booking pertama Anda. Jangan sampai ketinggalan!</p>
            <a href="<?php echo htmlspecialchars($baseUrl . 'user/dashboard'); ?>" class="btn btn-promo">Klaim Diskon</a>
        </div>
    </section>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>