<?php

class BookingModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createBooking(int $userId, int $kosId, string $tanggalMulai, string $tanggalSelesai, string $durasiSewa, float $totalHarga, string $status = 'pending') {
        $sql = "INSERT INTO bookings (user_id, kos_id, tanggal_mulai, tanggal_selesai, durasi_sewa, total_harga, status_pemesanan, tanggal_pemesanan) 
                VALUES (:user_id, :kos_id, :tanggal_mulai, :tanggal_selesai, :durasi_sewa, :total_harga, :status, CURRENT_TIMESTAMP)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':kos_id', $kosId, PDO::PARAM_INT);
            $stmt->bindParam(':tanggal_mulai', $tanggalMulai);
            $stmt->bindParam(':tanggal_selesai', $tanggalSelesai);
            $stmt->bindParam(':durasi_sewa', $durasiSewa);
            $stmt->bindParam(':total_harga', $totalHarga);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("BookingModel::createBooking Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateBookingStatus(int $bookingId, string $newStatus): bool {
        $allowedStatus = ['pending', 'confirmed', 'rejected', 'canceled', 'completed'];
        if (!in_array($newStatus, $allowedStatus)) {
            return false;
        }
        $sql = "UPDATE bookings SET status_pemesanan = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':id', $bookingId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function getBookingsByUserId(int $userId): array {
        $sql = "SELECT 
                    b.id as booking_id, b.kos_id,
                    b.tanggal_pemesanan, b.tanggal_mulai, b.tanggal_selesai, 
                    b.durasi_sewa, b.total_harga, b.status_pemesanan,
                    k.nama_kos, k.alamat as alamat_kos,
                    p.metode_pembayaran, p.jumlah_pembayaran, p.status_pembayaran, p.tanggal_pembayaran
                FROM bookings b
                JOIN kos k ON b.kos_id = k.id
                LEFT JOIN payments p ON b.id = p.booking_id
                WHERE b.user_id = :user_id
                ORDER BY b.tanggal_pemesanan DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBookings(): array {
        $sql = "SELECT 
                    b.id as booking_id, b.kos_id,
                    b.tanggal_pemesanan, b.tanggal_mulai, b.tanggal_selesai, 
                    b.durasi_sewa, b.total_harga, b.status_pemesanan,
                    u.nama as nama_penyewa, u.email as email_penyewa,
                    k.nama_kos,
                    p.id as payment_id_val, p.status_pembayaran
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN kos k ON b.kos_id = k.id
                LEFT JOIN payments p ON b.id = p.booking_id
                ORDER BY b.tanggal_pemesanan DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingById(int $bookingId) {
        $sql = "SELECT 
                    b.*, 
                    b.id as booking_id_val,
                    u.nama as user_nama, u.email as user_email, u.no_telepon as user_kontak,
                    k.nama_kos, k.alamat as kos_alamat, k.harga_per_bulan as kos_harga_per_bulan,
                    k.jumlah_kamar_tersedia as kos_kamar_tersedia, k.status_kos as status_kos_saat_ini,
                    p.id as payment_id_val, p.metode_pembayaran, p.jumlah_pembayaran, 
                    p.status_pembayaran, p.tanggal_pembayaran, p.bukti_pembayaran
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN kos k ON b.kos_id = k.id
                LEFT JOIN payments p ON b.id = p.booking_id
                WHERE b.id = :booking_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>