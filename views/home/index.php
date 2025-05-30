<?php
// Variabel $pageTitle, $appConfig, dan $pesanSelamatDatang
// telah di-passing dari HomeController melalui BaseController::loadView().

// Default values for demonstration if not passed
$pageTitle = $pageTitle ?? 'Sistem Booking Kos Modern';
$pesanSelamatDatang = $pesanSelamatDatang ?? 'Temukan Kos Impian Anda Bersama Kami!';
$namaAplikasi = $appConfig['nama_aplikasi'] ?? 'KosKita';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - <?php echo htmlspecialchars($namaAplikasi); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Custom Styles based on the provided color palette */
        /* Palette:
           - Lightest Blue/White: #FFFFFF or #F0F4FF
           - Light Blue: #D6E4FF or #C0D6FF
           - Medium Blue: #6A9EFF or #4285F4 (Bootstrap primary can be overridden)
           - Dark Blue: #1C3A6E or #0D2A57
        */
        html {
            scroll-behavior: smooth; /* Untuk efek scroll yang halus */
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F0F4FF; /* Lightest blue for page background */
            color: #0D2A57; /* Darkest blue for primary text */
        }

        .navbar-custom {
            background-color: #0D2A57; /* Darkest blue for navbar */
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #FFFFFF;
            font-weight: 600;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: #D6E4FF; /* Light blue for hover/active link */
        }
        .navbar-brand i {
            margin-right: 8px;
            color: #6A9EFF; /* Medium blue for icon */
        }

        .hero-section {
            background: linear-gradient(135deg, #4285F4, #1C3A6E); /* Gradient from medium to dark blue */
            color: #FFFFFF;
            padding: 6rem 1rem;
            text-align: center;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
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
        .hero-section .btn-custom-primary {
            background-color: #FFFFFF;
            color: #1C3A6E; /* Dark blue text on white button */
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
            border: 2px solid #D6E4FF; /* Light blue border */
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            margin-left: 10px;
            transition: all 0.3s ease;
        }
        .hero-section .btn-custom-secondary:hover {
            background-color: #D6E4FF;
            color: #1C3A6E; /* Dark blue text */
        }


        .page-header h2 {
            color: #1C3A6E; /* Dark blue for section titles */
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .content-section {
            padding: 2rem 0;
        }

        .feature-card {
            background-color: #FFFFFF;
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(28, 58, 110, 0.1); /* Subtle shadow with dark blue tint */
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%; /* Ensure cards have same height in a row */
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(28, 58, 110, 0.15);
        }
        .feature-card .card-body {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .feature-card .card-title {
            color: #1C3A6E; /* Dark blue */
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .feature-card .card-text, .feature-card ul li {
            color: #0D2A57; /* Darkest blue, slightly lighter than title */
            font-weight: 400;
            margin-bottom: 1rem; /* Added margin for spacing */
        }
        .feature-card i {
            font-size: 2rem;
            color: #4285F4; /* Medium blue for icons */
            margin-bottom: 1rem;
            display: block;
        }
        .feature-card .btn-outline-custom {
            color: #4285F4;
            border-color: #4285F4;
            font-weight: 600;
            border-radius: 50px;
            margin-top: auto; /* Pushes button to the bottom */
        }
        .feature-card .btn-outline-custom:hover {
            background-color: #4285F4;
            color: #FFFFFF;
        }

        .promo-section {
            background-color: #D6E4FF; /* Light blue background */
            padding: 3rem 1rem; /* Adjusted padding for responsiveness */
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
            background-color: #F8F9FA; /* Lighter than page background for contrast */
            border: 1px solid #D6E4FF; /* Light blue border */
            border-radius: 0.5rem !important; /* Rounded corners for accordion items, added !important for specificity */
            margin-bottom: 0.5rem;
            overflow: hidden; /* Ensures child border-radius is contained */
        }
        .additional-info-section .accordion-header { /* Ensure header also has rounded corners */
             border-radius: calc(0.5rem - 1px);
        }

        .additional-info-section .accordion-button {
            color: #1C3A6E; /* Dark blue for accordion button text */
            font-weight: 600;
            background-color: #E9F0FF; /* Very light blue for accordion button background */
            border-radius: calc(0.5rem - 1px) !important; /* Match item's border radius */
        }
        .additional-info-section .accordion-button:not(.collapsed) {
            background-color: #D6E4FF; /* Light blue when expanded */
            color: #0D2A57;
            box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
        }
        .additional-info-section .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(66, 133, 244, 0.25); /* Medium blue focus ring */
        }
        .additional-info-section .accordion-body {
            color: #0D2A57;
            background-color: #FFFFFF; /* White background for content */
            padding: 1.25rem;
             border-bottom-left-radius: calc(0.5rem - 1px); /* Rounded bottom corners for body */
            border-bottom-right-radius: calc(0.5rem - 1px);
        }
        .additional-info-section .accordion-button::after { /* Custom accordion icon color */
            filter: invert(15%) sepia(45%) saturate(2500%) hue-rotate(210deg) brightness(90%) contrast(95%);
        }
         .additional-info-section .btn-learn-more {
            background-color: #4285F4; /* Medium blue */
            color: white;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border: none;
            transition: background-color 0.3s ease;
        }
        .additional-info-section .btn-learn-more:hover {
            background-color: #1C3A6E; /* Darker blue on hover */
        }


        .footer-custom {
            background-color: #0D2A57; /* Darkest blue for footer */
            color: #D6E4FF; /* Light blue text */
            padding: 2rem 0;
            margin-top: 3rem;
            text-align: center;
        }
        .footer-custom p {
            margin-bottom: 0;
            font-weight: 300;
        }

        /* Target specific scroll padding for fixed navbar */
        [id] {
            scroll-margin-top: 80px; /* Adjust this value based on your navbar height */
        }

    </style>
</head>
<body>

    <header class="hero-section">
        <div class="container">
            <h1><?php echo htmlspecialchars($pesanSelamatDatang); ?></h1>
            <p><h5>Platform terpercaya untuk menemukan kos idaman dengan mudah dan cepat. Jelajahi berbagai pilihan kos berkualitas di lokasi strategis.</h5></p>
        </div>
    </header>

    <div class="container content-section">
        <!-- <div class="page-header mt-5 mb-4">
            <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
        </div> -->

        <div id="fitur" class="text-center mb-5 pt-4">
             <h2 style="color: #1C3A6E; font-weight: 700;">Kenapa Memilih <?php echo htmlspecialchars($namaAplikasi); ?>?</h2>
             <p class="lead" style="color: #0D2A57;"><h6>Kami menyediakan fitur terbaik untuk pengalaman mencari kos yang tak terlupakan.</h6></p>
        </div>

        <div class="row">
            <div class="col-md-4 d-flex align-items-stretch">
                <div class="card feature-card text-center">
                    <div class="card-body">
                        <div> <i class="fas fa-search-location"></i>
                            <h5 class="card-title">Pencarian Cepat & Akurat</h5>
                            <p class="card-text">Temukan kamar kos impian Anda dengan filter pencarian yang lengkap dan hasil yang relevan.</p>
                        </div>
                        <a href="#infoAccordion" class="btn btn-outline-custom">Cari Sekarang</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-stretch">
                <div class="card feature-card text-center">
                    <div class="card-body">
                        <div> <i class="fas fa-star"></i>
                            <h5 class="card-title">Kos Populer & Terverifikasi</h5>
                            <p class="card-text">Lihat daftar kamar kos populer pilihan pengguna atau yang baru saja ditambahkan oleh pemilik.</p>
                        </div>
                        <a href="#infoAccordion" class="btn btn-outline-custom">Lihat Populer</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-stretch">
                <div class="card feature-card text-center">
                    <div class="card-body">
                        <div> <i class="fas fa-bullhorn"></i>
                            <h5 class="card-title">Promo & Informasi Penting</h5>
                            <p class="card-text">Dapatkan penawaran spesial, diskon, dan informasi penting seputar kos.</p>
                        </div>
                        <a href="#infoAccordion" class="btn btn-outline-custom">Promo & Informasi</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="additional-info-section" id="infoAccordionParent"> <h3><i class="fas fa-info-circle me-2" style="color: #4285F4;"></i>Informasi Tambahan</h3>
             <p><?php echo htmlspecialchars($pesanSelamatDatang ?? 'Selamat datang di aplikasi kami!'); // Original message ?></p>
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
                <a href="#fitur" class="btn btn-learn-more"><i class="fas fa-arrow-right me-2"></i>Pelajari Lebih Lanjut</a>
             </div>
        </div>
    </div>

    <footer class="footer-custom">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($namaAplikasi); ?>. All Rights Reserved.</p>
            <p>Dibuat dengan <i class="fas fa-heart" style="color: #ff7b7b;"></i> untuk kemudahan Anda.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>