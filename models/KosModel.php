<?php

class KosModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllKos(string $orderBy = 'created_at', string $orderDir = 'DESC') {
        $sql = "SELECT id, nama_kos, alamat, harga_per_bulan, status_kos, jumlah_kamar_total, jumlah_kamar_tersedia,
                       (SELECT gk.path FROM gambar_kos gk WHERE gk.kos_id = kos.id ORDER BY gk.id ASC LIMIT 1) as gambar_utama
                FROM kos 
                ORDER BY {$orderBy} {$orderDir}";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getKosById(int $id) {
        $sql = "SELECT k.*, 
                       (SELECT JSON_ARRAYAGG(
                                  JSON_OBJECT('id', gk.id, 'nama_file', gk.nama_file, 'path', gk.path)
                                ) 
                        FROM gambar_kos gk 
                        WHERE gk.kos_id = k.id
                       ) AS gambar_kos_json
                FROM kos k 
                WHERE k.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $kos = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kos) {
            $kos['gambar_kos'] = !empty($kos['gambar_kos_json']) ? json_decode($kos['gambar_kos_json'], true) : [];
            unset($kos['gambar_kos_json']); 
        }
        return $kos;
    }
    
    public function decrementKamarTersedia(int $kosId): bool {
        // Langkah 1: Ambil data kamar saat ini dan kunci baris tersebut untuk update.
        // Ini mencegah user lain memodifikasi baris ini sampai transaksi selesai.
        $sqlGetCurrent = "SELECT jumlah_kamar_tersedia, status_kos FROM kos WHERE id = :id FOR UPDATE";
        $stmtGetCurrent = $this->pdo->prepare($sqlGetCurrent);
        $stmtGetCurrent->bindParam(':id', $kosId, PDO::PARAM_INT);
        
        try {
            $stmtGetCurrent->execute();
            $currentKosData = $stmtGetCurrent->fetch(PDO::FETCH_ASSOC);

            if (!$currentKosData) {
                error_log("KosModel::decrementKamarTersedia: Kos ID {$kosId} tidak ditemukan saat SELECT FOR UPDATE.");
                return false; // Kos tidak ditemukan
            }
            
            // Periksa lagi ketersediaan dan status sebelum mengurangi
            if (($currentKosData['jumlah_kamar_tersedia'] ?? 0) <= 0 || $currentKosData['status_kos'] !== 'available') {
                error_log("KosModel::decrementKamarTersedia: Kamar tidak tersedia atau status kos bukan 'available' untuk ID {$kosId}. Jumlah saat ini: " . ($currentKosData['jumlah_kamar_tersedia'] ?? 'N/A') . ", Status: " . ($currentKosData['status_kos'] ?? 'N/A'));
                return false; 
            }

            $newJumlahTersedia = $currentKosData['jumlah_kamar_tersedia'] - 1;
            // Status akan diupdate berdasarkan jumlah kamar baru (kecuali jika Anda menggunakan trigger DB)
            // Jika Anda MENGGUNAKAN trigger DB yang sudah kita bahas, Anda bisa HAPUS update status_kos dari query di bawah.
            // Jika TIDAK menggunakan trigger DB, biarkan query di bawah apa adanya.
            $newStatusKos = ($newJumlahTersedia <= 0) ? 'booked' : 'available';

            $sqlUpdate = "UPDATE kos SET 
                            jumlah_kamar_tersedia = :jumlah_kamar_tersedia, 
                            status_kos = :status_kos, 
                            updated_at = CURRENT_TIMESTAMP 
                          WHERE id = :id"; 
            
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':jumlah_kamar_tersedia', $newJumlahTersedia, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':status_kos', $newStatusKos); // Baris ini mengatur status kos
            $stmtUpdate->bindParam(':id', $kosId, PDO::PARAM_INT);
            
            if ($stmtUpdate->execute()) {
                return $stmtUpdate->rowCount() > 0; // Pastikan baris benar-benar terupdate
            } else {
                error_log("KosModel::decrementKamarTersedia gagal mengeksekusi UPDATE untuk kos ID {$kosId}. PDO ErrorInfo: " . implode(", ", $stmtUpdate->errorInfo()));
                return false;
            }

        } catch (PDOException $e) {
            error_log("KosModel::decrementKamarTersedia PDOException: " . $e->getMessage());
            // Jangan rollback di sini, biarkan controller yang menangani transaksi utama
            return false;
        }
    }

    // ... (createKos, updateKos, deleteKos, updateKosStatus yang sudah ada) ...
    // Pastikan createKos juga mengatur jumlah_kamar_total dan jumlah_kamar_tersedia dengan benar
    public function createKos(array $data) {
        $jumlah_kamar_total = $data['jumlah_kamar_total'] ?? 1;
        $jumlah_kamar_tersedia = $data['jumlah_kamar_tersedia'] ?? $jumlah_kamar_total; // Bisa diset admin saat create
        $status_kos_input = $data['status_kos'] ?? null;

        if ($status_kos_input && in_array($status_kos_input, ['available', 'booked', 'maintenance'])) {
            $status_kos = $status_kos_input;
        } else {
            $status_kos = ($jumlah_kamar_tersedia > 0) ? 'available' : 'booked';
        }


        $sql = "INSERT INTO kos (nama_kos, alamat, deskripsi, harga_per_bulan, fasilitas_kos, 
                                jumlah_kamar_total, jumlah_kamar_tersedia, status_kos, created_at, updated_at) 
                VALUES (:nama_kos, :alamat, :deskripsi, :harga_per_bulan, :fasilitas_kos, 
                        :jumlah_kamar_total, :jumlah_kamar_tersedia, :status_kos, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        try {
            $stmt = $this->pdo->prepare($sql);
            // ... (bindParam untuk semua field) ...
            $stmt->bindParam(':nama_kos', $data['nama_kos']);
            $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':deskripsi', $data['deskripsi']);
            $stmt->bindParam(':harga_per_bulan', $data['harga_per_bulan']);
            $stmt->bindParam(':fasilitas_kos', $data['fasilitas_kos']);
            $stmt->bindParam(':jumlah_kamar_total', $jumlah_kamar_total, PDO::PARAM_INT);
            $stmt->bindParam(':jumlah_kamar_tersedia', $jumlah_kamar_tersedia, PDO::PARAM_INT);
            $stmt->bindParam(':status_kos', $status_kos);
            
            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("KosModel::createKos Error: " . $e->getMessage());
            return false;
        }
    }
     public function updateKos(int $id, array $data): bool {
        $sql = "UPDATE kos SET 
                    nama_kos = :nama_kos, 
                    alamat = :alamat, 
                    deskripsi = :deskripsi, 
                    harga_per_bulan = :harga_per_bulan, 
                    fasilitas_kos = :fasilitas_kos,
                    jumlah_kamar_total = :jumlah_kamar_total,
                    jumlah_kamar_tersedia = :jumlah_kamar_tersedia,
                    status_kos = :status_kos, 
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            // ... (bindParam untuk semua field) ...
            $stmt->bindParam(':nama_kos', $data['nama_kos']);
            $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':deskripsi', $data['deskripsi']);
            $stmt->bindParam(':harga_per_bulan', $data['harga_per_bulan']);
            $stmt->bindParam(':fasilitas_kos', $data['fasilitas_kos']);
            $stmt->bindParam(':jumlah_kamar_total', $data['jumlah_kamar_total'], PDO::PARAM_INT);
            $stmt->bindParam(':jumlah_kamar_tersedia', $data['jumlah_kamar_tersedia'], PDO::PARAM_INT);
            $stmt->bindParam(':status_kos', $data['status_kos']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("KosModel::updateKos Error: " . $e->getMessage());
            return false;
        }
    }
    public function deleteKos(int $id): bool { // sudah ada dari sebelumnya
        $this->pdo->beginTransaction();
        try {
            $sqlDeleteGambar = "DELETE FROM gambar_kos WHERE kos_id = :kos_id";
            $stmtDeleteGambar = $this->pdo->prepare($sqlDeleteGambar);
            $stmtDeleteGambar->bindParam(':kos_id', $id, PDO::PARAM_INT);
            $stmtDeleteGambar->execute(); 

            $sqlDeleteKos = "DELETE FROM kos WHERE id = :id";
            $stmtDeleteKos = $this->pdo->prepare($sqlDeleteKos);
            $stmtDeleteKos->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmtDeleteKos->execute()) {
                if ($stmtDeleteKos->rowCount() > 0) {
                    $this->pdo->commit();
                    return true; 
                } else {
                    $this->pdo->rollBack();
                    return false; 
                }
            } else {
                $this->pdo->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("KosModel::deleteKos Error: " . $e->getMessage());
            return false;
        }
    }
    public function updateKosStatus(int $kosId, string $newStatus): bool { // sudah ada
        $allowedStatus = ['available', 'booked', 'maintenance'];
        if (!in_array($newStatus, $allowedStatus)) return false;
        $sql = "UPDATE kos SET status_kos = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':id', $kosId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>