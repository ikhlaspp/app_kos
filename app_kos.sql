-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2025 at 03:49 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `u677146100_appkos`
--
CREATE DATABASE IF NOT EXISTS `u677146100_appkos` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `u677146100_appkos`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
CREATE TABLE `chat_messages` (
  `id` int NOT NULL,
  `conversation_id` varchar(50) NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message_text` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gambar_kos`
--

DROP TABLE IF EXISTS `gambar_kos`;
CREATE TABLE `gambar_kos` (
  `id` int NOT NULL,
  `kos_id` int NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `gambar_kos`
--

INSERT INTO `gambar_kos` (`id`, `kos_id`, `nama_file`, `path`, `created_at`) VALUES
(7, 1, 'kos_melati_indah.jpg', 'kos_images/kos_melati_indah_6831e6ad1cb723.08693562.jpg', '2025-05-24 15:33:01'),
(8, 1, 'kos_melati_indah_1.jpg', 'kos_images/kos_melati_indah_1_6831e6ad1d2b71.15838675.jpg', '2025-05-24 15:33:01'),
(9, 1, 'kos_melati_indah_2.jpg', 'kos_images/kos_melati_indah_2_6831e6ad1d6f77.21339410.jpg', '2025-05-24 15:33:01'),
(10, 2, 'kos_Wisma Bougenville Residence.jpg', 'kos_images/kos_WismaBougenvilleResidence_6831e6f88042a1.56299794.jpg', '2025-05-24 15:34:16'),
(11, 2, 'kos_Wisma Bougenville Residence_2.jpg', 'kos_images/kos_WismaBougenvilleResidence_2_6831e6f8809cd9.67439666.jpg', '2025-05-24 15:34:16'),
(12, 3, 'kos_Kos Flamboyan Exclusive (Putra).jpg', 'kos_images/kos_KosFlamboyanExclusivePutra_6831e766a2a610.21270199.jpg', '2025-05-24 15:36:06'),
(13, 3, 'kos_Kos Flamboyan Exclusive (Putra)_1.jpg', 'kos_images/kos_KosFlamboyanExclusivePutra_1_6831e766a30116.68338576.jpg', '2025-05-24 15:36:06'),
(14, 4, 'kos_Paviliun Kenanga Asri.jpg', 'kos_images/kos_PaviliunKenangaAsri_6831e83f0afdd0.12724730.jpg', '2025-05-24 15:39:43'),
(15, 4, 'kos_Paviliun Kenanga Asri_1.jpg', 'kos_images/kos_PaviliunKenangaAsri_1_6831e83f0b6d71.90199454.jpg', '2025-05-24 15:39:43'),
(16, 5, 'kos_Pondok Cempaka Hijau (Putri).jpeg', 'kos_images/kos_PondokCempakaHijauPutri_6831e96ab00377.95929242.jpeg', '2025-05-24 15:44:42'),
(17, 5, 'kos_Pondok Cempaka Hijau (Putri)_2.jpeg', 'kos_images/kos_PondokCempakaHijauPutri_2_6831e96ab048b4.10179233.jpeg', '2025-05-24 15:44:42');

-- --------------------------------------------------------

--
-- Table structure for table `kos`
--

DROP TABLE IF EXISTS `kos`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `kos`
--

INSERT INTO `kos` (`id`, `nama_kos`, `alamat`, `deskripsi`, `harga_per_bulan`, `fasilitas_kos`, `jumlah_kamar_total`, `jumlah_kamar_tersedia`, `status_kos`, `created_at`, `updated_at`) VALUES
(1, 'Kos Melati Indah Tipe A', 'Jl. Mawar No. 10, RT 01 RW 02, Kelurahan Kembang, Kecamatan Melati, Jakarta Pusat, DKI Jakarta 10210', 'Kos putri bersih dan nyaman dengan fasilitas lengkap. Lokasi strategis dekat area perkantoran Sudirman dan kampus ternama. Akses mudah ke transportasi umum (TransJakarta, MRT). Keamanan 24 jam dengan CCTV.', '1750000.00', 'AC, WiFi Cepat, Kamar Mandi Dalam (Shower, Kloset Duduk), Lemari Pakaian, Meja Belajar, Kursi, Kasur Spring Bed, Token Listrik per Kamar', 10, 8, 'available', '2025-05-24 04:58:27', '2025-05-24 15:33:01'),
(2, 'Wisma Bougenville Residence', 'Jl. Anggrek Boulevard Kav. B2 No. 25, Komplek Griya Asri, Kota Bandung, Jawa Barat 40111', 'Kos campur (pria/wanita) dengan bangunan baru dan modern. Suasana tenang dan asri, cocok untuk mahasiswa dan karyawan. Fasilitas umum lengkap, termasuk dapur bersama dan area parkir luas.', '1350000.00', 'WiFi, Kamar Mandi Dalam (Kloset Jongkok), Kasur Busa, Lemari, Meja Kecil, Dapur Bersama (Kulkas, Kompor), Parkir Motor & Mobil (terbatas)', 10, 0, 'booked', '2025-05-24 04:58:27', '2025-05-24 15:34:16'),
(3, 'Kos Flamboyan Exclusive (Putra)', 'Jl. Kamboja Raya No. 5 Blok C, Perumahan Elite Sejahtera, Kota Surabaya, Jawa Timur 60233', 'Kos putra eksklusif dengan perabotan modern dan layanan kebersihan kamar mingguan. Lingkungan aman dan nyaman, dekat dengan pusat bisnis dan mall.', '2200000.00', 'Full Furnished, AC, TV Kabel, WiFi, Kamar Mandi Dalam (Air Panas Dingin), Kulkas Mini di Kamar, Laundry (opsional), Keamanan 24 Jam', 10, 10, 'available', '2025-05-24 04:58:27', '2025-05-24 15:36:06'),
(4, 'Paviliun Kenanga Asri', 'Jl. Kenanga III Gg. Damai No. 1B, Belakang Kampus UGM, Sleman, Yogyakarta, DIY 55281', 'Paviliun/kos campur dengan beberapa unit terpisah, masing-masing dengan teras kecil. Lingkungan mahasiswa yang ramai namun tetap kondusif untuk belajar. Dekat dengan banyak tempat makan dan fotokopi.', '950000.00', 'Kamar Mandi Luar (bersama, bersih), Kasur, Lemari, Meja, WiFi (area umum), Parkir Motor', 10, 0, 'booked', '2025-05-24 04:58:27', '2025-05-24 15:39:43'),
(5, 'Pondok Cempaka Hijau (Putri)', 'Jl. Cempaka Wangi No. 30A, Medan Kota, Medan, Sumatera Utara 20212', 'Kos putri dengan harga terjangkau dan fasilitas standar. Cocok untuk mahasiswi atau karyawati. Bangunan sedang dalam tahap perbaikan ringan di beberapa bagian.', '750000.00', 'Kipas Angin, Kasur, Lemari Pakaian, Kamar Mandi Bersama, Dapur Umum Sederhana', 10, 10, 'available', '2025-05-24 04:58:27', '2025-05-24 15:44:42');

--
-- Triggers `kos`
--
DROP TRIGGER IF EXISTS `trg_kos_status_on_kamar_change_before_update`;
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

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `metode_pembayaran` varchar(100) NOT NULL,
  `jumlah_pembayaran` decimal(10,2) NOT NULL,
  `status_pembayaran` enum('pending','paid','failed') DEFAULT 'pending',
  `tanggal_pembayaran` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `is_admin`, `no_telepon`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$aoPgNPslKIlmf6BxSUh.yuJpmMSO8I7sWjKpMrnyFUSk3q7SMORsq', 1, '081234567890', 'Kantor Pusat', '2025-05-24 09:06:24', '2025-05-24 09:10:57'),
(2, 'user1', 'user1@gmail.com', '$2y$10$P9y0k8Q.ASxJ0L3q0Y.H1.H/k7Z2j.7wN4jU9.0X6eX1kP7W7X.Lq', 0, NULL, NULL, '2025-05-24 11:23:18', '2025-05-24 15:45:36'),
(3, 'user2', 'user2@gmail.com', '$2y$10$zS.E8g.N0e3n.P5o.Q7.X.Y/i8A3k.6wO5kV0.1Y7fY2lQ8X8Y.Mr', 0, NULL, NULL, '2025-05-24 11:23:18', '2025-05-24 15:45:36');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
