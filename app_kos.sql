-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2025 at 11:24 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_kos`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `kos_id` int NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `durasi_sewa` varchar(50) DEFAULT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_pemesanan` enum('pending','confirmed','rejected','canceled','completed') DEFAULT 'pending',
  `tanggal_pemesanan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int NOT NULL,
  `conversation_id` varchar(50) NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message_text` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gambar_kos`
--

CREATE TABLE `gambar_kos` (
  `id` int NOT NULL,
  `kos_id` int NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gambar_kos`
--

INSERT INTO `gambar_kos` (`id`, `kos_id`, `nama_file`, `path`, `created_at`) VALUES
(1, 1, 'kos1.png', 'kos_images/kos1_6831a784c946e4.62583473.png', '2025-05-24 11:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `kos`
--

CREATE TABLE `kos` (
  `id` int NOT NULL,
  `nama_kos` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `deskripsi` text,
  `harga_per_bulan` decimal(10,2) NOT NULL,
  `fasilitas_kos` text,
  `jumlah_kamar_total` int NOT NULL DEFAULT '1',
  `jumlah_kamar_tersedia` int NOT NULL DEFAULT '1',
  `status_kos` enum('available','booked','maintenance') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kos`
--

INSERT INTO `kos` (`id`, `nama_kos`, `alamat`, `deskripsi`, `harga_per_bulan`, `fasilitas_kos`, `jumlah_kamar_total`, `jumlah_kamar_tersedia`, `status_kos`, `created_at`, `updated_at`) VALUES
(1, 'Kos Melati Indah Tipe A', 'Jl. Mawar No. 10, RT 01 RW 02, Kelurahan Kembang, Kecamatan Melati, Jakarta Pusat, DKI Jakarta 10210', 'Kos putri bersih dan nyaman dengan fasilitas lengkap. Lokasi strategis dekat area perkantoran Sudirman dan kampus ternama. Akses mudah ke transportasi umum (TransJakarta, MRT). Keamanan 24 jam dengan CCTV.', '1750000.00', 'AC, WiFi Cepat, Kamar Mandi Dalam (Shower, Kloset Duduk), Lemari Pakaian, Meja Belajar, Kursi, Kasur Spring Bed, Token Listrik per Kamar', 10, 8, 'available', '2025-05-24 04:58:27', '2025-05-24 11:17:59'),
(2, 'Wisma Bougenville Residence', 'Jl. Anggrek Boulevard Kav. B2 No. 25, Komplek Griya Asri, Kota Bandung, Jawa Barat 40111', 'Kos campur (pria/wanita) dengan bangunan baru dan modern. Suasana tenang dan asri, cocok untuk mahasiswa dan karyawan. Fasilitas umum lengkap, termasuk dapur bersama dan area parkir luas.', '1350000.00', 'WiFi, Kamar Mandi Dalam (Kloset Jongkok), Kasur Busa, Lemari, Meja Kecil, Dapur Bersama (Kulkas, Kompor), Parkir Motor & Mobil (terbatas)', 10, 0, 'booked', '2025-05-24 04:58:27', '2025-05-24 06:05:32'),
(3, 'Kos Flamboyan Exclusive (Putra)', 'Jl. Kamboja Raya No. 5 Blok C, Perumahan Elite Sejahtera, Kota Surabaya, Jawa Timur 60233', 'Kos putra eksklusif dengan perabotan modern dan layanan kebersihan kamar mingguan. Lingkungan aman dan nyaman, dekat dengan pusat bisnis dan mall.', '2200000.00', 'Full Furnished, AC, TV Kabel, WiFi, Kamar Mandi Dalam (Air Panas Dingin), Kulkas Mini di Kamar, Laundry (opsional), Keamanan 24 Jam', 10, 10, 'available', '2025-05-24 04:58:27', '2025-05-24 06:05:32'),
(4, 'Paviliun Kenanga Asri', 'Jl. Kenanga III Gg. Damai No. 1B, Belakang Kampus UGM, Sleman, Yogyakarta, DIY 55281', 'Paviliun/kos campur dengan beberapa unit terpisah, masing-masing dengan teras kecil. Lingkungan mahasiswa yang ramai namun tetap kondusif untuk belajar. Dekat dengan banyak tempat makan dan fotokopi.', '950000.00', 'Kamar Mandi Luar (bersama, bersih), Kasur, Lemari, Meja, WiFi (area umum), Parkir Motor', 10, 0, 'booked', '2025-05-24 04:58:27', '2025-05-24 06:05:32'),
(5, 'Pondok Cempaka Hijau (Putri)', 'Jl. Cempaka Wangi No. 30A, Medan Kota, Medan, Sumatera Utara 20212', 'Kos putri dengan harga terjangkau dan fasilitas standar. Cocok untuk mahasiswi atau karyawati. Bangunan sedang dalam tahap perbaikan ringan di beberapa bagian.', '750000.00', 'Kipas Angin, Kasur, Lemari Pakaian, Kamar Mandi Bersama, Dapur Umum Sederhana', 10, 10, 'available', '2025-05-24 04:58:27', '2025-05-24 06:38:58'),
(6, 'Graha Mawar Executive', 'Jl. Mawar Indah No. 88, Sektor Bisnis Terpadu, Jakarta Selatan, DKI Jakarta 12190', 'Kos eksekutif campur dengan fasilitas premium. Akses kartu untuk setiap lantai, lobi yang nyaman, dan rooftop garden. Cocok untuk profesional muda.', '3500000.00', 'AC, Smart TV, WiFi Kencang, Kamar Mandi Dalam (Water Heater, Shower Box), Full Furnished Modern, Mini Pantry, Cleaning Service Harian, Gym Mini, Rooftop Garden', 10, 10, 'available', '2025-05-24 04:58:27', '2025-05-24 06:05:32'),
(7, 'Kost Harian Sederhana Pak Budi', 'Jl. Pasar Lama Gg. Buntu No. 7, Dekat Stasiun Kota, Semarang, Jawa Tengah 50137', 'Menyediakan kamar untuk harian dan bulanan dengan harga sangat terjangkau. Fasilitas dasar, lebih cocok untuk transit atau pekerja proyek jangka pendek.', '450000.00', 'Kipas Angin, Kasur Lipat, Kamar Mandi Luar', 10, 0, 'booked', '2025-05-24 04:58:27', '2025-05-24 10:28:34'),
(8, 'The Orchid Coliving Space', 'Jl. Inovasi Digital No. 1, BSD City, Tangerang Selatan, Banten 15345', 'Konsep coliving modern dengan banyak area komunal, ruang kerja bersama, dan event mingguan. Ideal untuk startup enthusiast dan digital nomad.', '2800000.00', 'AC, WiFi, Kamar Tidur Privat (KM Dalam/Luar tergantung tipe), Dapur Bersama Super Lengkap, Ruang Kerja, Ruang Komunal, Area Event, Laundry Koin', 10, 0, 'booked', '2025-05-24 04:58:27', '2025-05-24 06:05:32'),
(9, 'Kos UPN 223', 'Jln. Rungkut Asri no 23', 'Kos gacor', '500000.00', 'Kipas Angin, Kasur, Lemari Pakaian, Kamar Mandi Bersama, Dapur Umum Sederhana', 10, 10, 'available', '2025-05-24 06:46:24', '2025-05-24 06:46:24');

--
-- Triggers `kos`
--
DELIMITER $$
CREATE TRIGGER `trg_kos_status_on_kamar_change_before_update` BEFORE UPDATE ON `kos` FOR EACH ROW BEGIN
    -- Trigger ini hanya akan mengubah NEW.status_kos jika:
    -- 1. Nilai jumlah_kamar_tersedia pada baris yang diupdate (NEW.jumlah_kamar_tersedia)
    --    berbeda dengan nilai lama (OLD.jumlah_kamar_tersedia). Ini berarti field jumlah_kamar_tersedia
    --    memang sedang diubah oleh statement UPDATE yang memicu trigger ini.
    -- 2. DAN, status kos yang AKAN di-set oleh statement UPDATE (NEW.status_kos),
    --    atau status lama jika status tidak diubah oleh statement UPDATE, BUKAN 'maintenance'.
    --    Ini memastikan status 'maintenance' adalah override manual dan tidak diubah oleh trigger ini.

    IF OLD.jumlah_kamar_tersedia <> NEW.jumlah_kamar_tersedia AND NEW.status_kos <> 'maintenance' THEN
        IF NEW.jumlah_kamar_tersedia <= 0 THEN
            SET NEW.status_kos = 'booked';
        ELSE -- jika NEW.jumlah_kamar_tersedia > 0
            SET NEW.status_kos = 'available';
        END IF;
    END IF;

    -- Catatan: Kolom `updated_at` akan otomatis terupdate jika didefinisikan dengan 
    -- ON UPDATE CURRENT_TIMESTAMP di skema tabel Anda dan ada perubahan pada baris.
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `metode_pembayaran` varchar(100) NOT NULL,
  `jumlah_pembayaran` decimal(10,2) NOT NULL,
  `status_pembayaran` enum('pending','paid','failed') DEFAULT 'pending',
  `tanggal_pembayaran` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `is_admin`, `no_telepon`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$aoPgNPslKIlmf6BxSUh.yuJpmMSO8I7sWjKpMrnyFUSk3q7SMORsq', 1, '081234567890', 'Kantor Pusat', '2025-05-24 09:06:24', '2025-05-24 09:10:57'),
(2, 'user1', 'user1@example.com', '$2y$10$P9y0k8Q.ASxJ0L3q0Y.H1.H/k7Z2j.7wN4jU9.0X6eX1kP7W7X.Lq', 0, NULL, NULL, '2025-05-24 11:23:18', '2025-05-24 11:23:18'),
(3, 'user2', 'user2@example.com', '$2y$10$zS.E8g.N0e3n.P5o.Q7.X.Y/i8A3k.6wO5kV0.1Y7fY2lQ8X8Y.Mr', 0, NULL, NULL, '2025-05-24 11:23:18', '2025-05-24 11:23:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kos_id` (`kos_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_conversation_timestamp` (`conversation_id`,`sent_at`);

--
-- Indexes for table `gambar_kos`
--
ALTER TABLE `gambar_kos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kos_id` (`kos_id`);

--
-- Indexes for table `kos`
--
ALTER TABLE `kos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gambar_kos`
--
ALTER TABLE `gambar_kos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kos`
--
ALTER TABLE `kos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`kos_id`) REFERENCES `kos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gambar_kos`
--
ALTER TABLE `gambar_kos`
  ADD CONSTRAINT `gambar_kos_ibfk_1` FOREIGN KEY (`kos_id`) REFERENCES `kos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
