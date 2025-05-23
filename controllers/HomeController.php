<?php

class HomeController extends BaseController {

    /**
     * Menampilkan halaman utama (beranda).
     */
    public function index(): void {
        $pageTitle = "Selamat Datang";
        
        $data = [
            'pesanSelamatDatang' => "Selamat datang di " . ($this->appConfig['APP_NAME'] ?? 'Aplikasi Kami') . "! Jelajahi fitur yang tersedia.",
            // Data lain yang mungkin ingin Anda kirim ke view home/index.php
        ];
        
        // Memuat view 'home/index' dengan data dan judul halaman.
        // BaseController akan menangani header, footer, dan membuat $appConfig serta variabel dari $data tersedia di view.
        $this->loadView('home/index', $data, $pageTitle);
    }

    /**
     * Menampilkan halaman "Tentang Kami".
     */
    public function about(): void {
        $pageTitle = "Tentang Aplikasi Ini";
        
        $data = [
            'namaAplikasi' => $this->appConfig['APP_NAME'] ?? 'Aplikasi Kos',
            'versi'        => '1.0.0',
            'deskripsi'    => 'Aplikasi ini dirancang untuk mempermudah proses pencarian dan pemesanan kamar kos secara online, dibangun menggunakan PHP native.',
        ];

        // Memuat view 'home/about'
        $this->loadView('home/about', $data, $pageTitle);
    }
}
?>