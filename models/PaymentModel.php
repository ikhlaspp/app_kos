<?php

class PaymentModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Membuat catatan pembayaran baru.
     * @return string|false ID pembayaran jika berhasil, false jika gagal.
     */
    public function createPayment(int $bookingId, string $metodePembayaran, float $jumlahPembayaran, string $statusPembayaran = 'paid', ?string $buktiPembayaran = null) {
        $sql = "INSERT INTO payments (booking_id, metode_pembayaran, jumlah_pembayaran, status_pembayaran, bukti_pembayaran, tanggal_pembayaran)
                VALUES (:booking_id, :metode_pembayaran, :jumlah_pembayaran, :status_pembayaran, :bukti_pembayaran, CURRENT_TIMESTAMP)";
        // Di dalam method createPayment() di file models/PaymentModel.php
        // ...
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
            $stmt->bindParam(':metode_pembayaran', $metodePembayaran);
            $stmt->bindParam(':jumlah_pembayaran', $jumlahPembayaran);
            $stmt->bindParam(':status_pembayaran', $statusPembayaran);
            $stmt->bindParam(':bukti_pembayaran', $buktiPembayaran); // Ini mengizinkan NULL jika kolomnya mengizinkan

            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            // Jika execute() gagal tanpa melempar exception (jarang terjadi dengan ATTR_ERRMODE => ERRMODE_EXCEPTION)
            error_log("PaymentModel::createPayment execute() returned false. PDO ErrorInfo: " . implode(", ", $this->pdo->errorInfo()));
            return false;
        } catch (PDOException $e) {
            // --- MODIFIKASI BAGIAN INI ---
            $logMessage = "PaymentModel::createPayment PDOException: " . $e->getMessage() . "\n";
            $logMessage .= "SQL Query: " . $sql . "\n";
            $logMessage .= "Parameters: \n";
            $logMessage .= "  bookingId: " . $bookingId . "\n";
            $logMessage .= "  metodePembayaran: " . $metodePembayaran . "\n";
            $logMessage .= "  jumlahPembayaran: " . $jumlahPembayaran . "\n";
            $logMessage .= "  statusPembayaran: " . $statusPembayaran . "\n";
            $logMessage .= "  buktiPembayaran: " . ($buktiPembayaran ?? 'NULL') . "\n";
            error_log($logMessage); // Catat pesan error yang detail
            // --- AKHIR MODIFIKASI ---
            return false;
        }
        // ...
    }
    // Anda bisa menambahkan method getPaymentByBookingId, dll.
}
?>