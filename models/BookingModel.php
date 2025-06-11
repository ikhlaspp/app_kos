<?php

class BookingModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Creates a new booking record in the database.
     *
     * @param int $userId The ID of the user making the booking.
     * @param int $kosId The ID of the kos being booked.
     * @param string $tanggalMulai The start date of the booking (YYYY-MM-DD).
     * @param string $tanggalSelesai The end date of the booking (YYYY-MM-DD).
     * @param string $durasiSewa The rental duration (e.g., '1 bulan', '3 bulan').
     * @param float $totalHarga The total price of the booking after any discounts.
     * @param string $status The initial status of the booking (e.g., 'pending', 'confirmed').
     * @param int|null $voucherId The ID of the voucher applied, if any.
     * @return int|false The ID of the newly created booking if successful, false otherwise.
     */
    public function createBooking(
        int $userId,
        int $kosId,
        string $tanggalMulai,
        string $tanggalSelesai,
        string $durasiSewa,
        float $totalHarga,
        string $status = 'pending',
        ?int $voucherId = null // ADDED: voucher_id parameter
    ) {
        // Ensure your bookings table has a 'voucher_id' column, nullable
        $sql = "INSERT INTO bookings (user_id, kos_id, tanggal_mulai, tanggal_selesai, durasi_sewa, total_harga, status_pemesanan, tanggal_pemesanan, voucher_id) 
                VALUES (:user_id, :kos_id, :tanggal_mulai, :tanggal_selesai, :durasi_sewa, :total_harga, :status, CURRENT_TIMESTAMP, :voucher_id)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':kos_id', $kosId, PDO::PARAM_INT);
            $stmt->bindParam(':tanggal_mulai', $tanggalMulai);
            $stmt->bindParam(':tanggal_selesai', $tanggalSelesai);
            $stmt->bindParam(':durasi_sewa', $durasiSewa);
            $stmt->bindParam(':total_harga', $totalHarga);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':voucher_id', $voucherId, PDO::PARAM_INT); // ADDED: Bind voucher_id

            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            error_log("BookingModel::createBooking failed to execute. PDO ErrorInfo: " . implode(", ", $this->pdo->errorInfo()));
            return false;
        } catch (PDOException $e) {
            error_log("BookingModel::createBooking Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates the status of a booking.
     *
     * @param int $bookingId The ID of the booking.
     * @param string $newStatus The new status (e.g., 'confirmed', 'rejected').
     * @return bool True on success, false on failure.
     */
    public function updateBookingStatus(int $bookingId, string $newStatus): bool {
        $allowedStatus = ['pending', 'confirmed', 'rejected', 'canceled', 'completed'];
        if (!in_array($newStatus, $allowedStatus)) {
            error_log("BookingModel::updateBookingStatus - Invalid status provided: {$newStatus}");
            return false;
        }
        $sql = "UPDATE bookings SET status_pemesanan = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':id', $bookingId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Retrieves all bookings for a specific user, including related kos, payment, and voucher details.
     *
     * @param int $userId The ID of the user.
     * @return array An array of booking records for the user.
     */
    public function getBookingsByUserId(int $userId): array {
        $sql = "SELECT 
                    b.id as booking_id, b.kos_id, b.user_id,
                    b.tanggal_pemesanan, b.tanggal_mulai, b.tanggal_selesai, 
                    b.durasi_sewa, b.total_harga, b.status_pemesanan,
                    k.nama_kos, k.alamat as alamat_kos,
                    p.metode_pembayaran, p.jumlah_pembayaran, p.status_pembayaran, p.tanggal_pembayaran,
                    v.code AS voucher_code, v.name AS voucher_name, v.type AS voucher_type, v.value AS voucher_value
                FROM bookings b
                JOIN kos k ON b.kos_id = k.id
                LEFT JOIN payments p ON b.id = p.booking_id
                LEFT JOIN vouchers v ON b.voucher_id = v.id -- ADDED: JOIN for voucher details
                WHERE b.user_id = :user_id
                ORDER BY b.tanggal_pemesanan DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves all booking records (for admin view), including user, kos, payment, and voucher details.
     *
     * @return array An array of all booking records.
     */
    public function getAllBookings(): array {
        $sql = "SELECT 
                    b.id as booking_id, b.kos_id, b.user_id,
                    b.tanggal_pemesanan, b.tanggal_mulai, b.tanggal_selesai, 
                    b.durasi_sewa, b.total_harga, b.status_pemesanan,
                    u.nama as nama_penyewa, u.email as email_penyewa,
                    k.nama_kos,
                    p.id as payment_id_val, p.status_pembayaran,
                    v.code AS voucher_code, v.name AS voucher_name -- ADDED: voucher details
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN kos k ON b.kos_id = k.id
                LEFT JOIN payments p ON b.id = p.booking_id
                LEFT JOIN vouchers v ON b.voucher_id = v.id -- ADDED: JOIN for voucher details
                ORDER BY b.tanggal_pemesanan DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves a single booking record by its ID, including related user, kos, payment, and voucher details.
     *
     * @param int $bookingId The ID of the booking.
     * @return array|false The booking record as an associative array, or false if not found.
     */
    public function getBookingById(int $bookingId) {
        $sql = "SELECT 
                    b.*, 
                    b.id as booking_id_val,
                    u.nama as user_nama, u.email as user_email, u.no_telepon as user_kontak,
                    k.nama_kos, k.alamat as kos_alamat, k.harga_per_bulan as kos_harga_per_bulan,
                    k.jumlah_kamar_tersedia as kos_kamar_tersedia, k.status_kos as status_kos_saat_ini,
                    p.id as payment_id_val, p.metode_pembayaran, p.jumlah_pembayaran, 
                    p.status_pembayaran, p.tanggal_pembayaran, p.bukti_pembayaran,
                    v.code AS voucher_code, v.name AS voucher_name, v.type AS voucher_type, v.value AS voucher_value,
                    v.min_transaction_amount AS voucher_min_transaction, v.max_discount_amount AS voucher_max_discount
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN kos k ON b.kos_id = k.id
                LEFT JOIN payments p ON b.id = p.booking_id
                LEFT JOIN vouchers v ON b.voucher_id = v.id -- ADDED: JOIN for voucher details
                WHERE b.id = :booking_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Counts the number of bookings with 'pending' status.
     *
     * @return int The total count of pending bookings.
     */
    public function countPendingBookings(): int {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status_pemesanan = 'pending'");
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("BookingModel::countPendingBookings Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Retrieves a limited number of recent confirmed bookings.
     *
     * @param int $limit The maximum number of bookings to retrieve.
     * @return array An array of recent confirmed booking records.
     */
    public function getRecentConfirmedBookings(int $limit = 5): array {
        try {
            $sql = "SELECT b.id, k.nama_kos, u.nama as nama_penyewa, b.tanggal_pemesanan, b.total_harga
                    FROM bookings b
                    JOIN kos k ON b.kos_id = k.id
                    JOIN users u ON b.user_id = u.id
                    WHERE b.status_pemesanan = 'confirmed'
                    ORDER BY b.tanggal_pemesanan DESC
                    LIMIT :limit";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("BookingModel::getRecentConfirmedBookings Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Counts the total number of booking records in the database.
     *
     * @return int The total count of bookings.
     */
    public function countTotalBookings(): int {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM bookings");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("BookingModel::countTotalBookings Error: " . $e->getMessage());
            return 0;
        }
    }

    public function getMonthlyBookingSummary(int $numMonths = 12): array {
        $summary = [];
        // Initialize summary for the last N months with 0 bookings
        for ($i = $numMonths - 1; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i month"));
            $summary[$month] = 0;
        }

        $sql = "SELECT
                    DATE_FORMAT(tanggal_pemesanan, '%Y-%m') AS month,
                    COUNT(id) AS total_bookings
                FROM
                    bookings
                WHERE
                    status_pemesanan = 'confirmed'
                    AND tanggal_pemesanan >= DATE_SUB(CURDATE(), INTERVAL :num_months MONTH)
                GROUP BY
                    month
                ORDER BY
                    month ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':num_months', $numMonths, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Merge actual results into the initialized summary
            foreach ($results as $row) {
                $summary[$row['month']] = (int)$row['total_bookings'];
            }
            return $summary;

        } catch (PDOException $e) {
            error_log("BookingModel::getMonthlyBookingSummary Error: " . $e->getMessage());
            return []; // Return empty array on failure
        }
    }
}