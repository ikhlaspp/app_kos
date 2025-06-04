<?php
// File: nama_proyek_kos/models/KosModel.php

class KosModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil semua data kos dengan opsi filter, search, dan pagination.
     */
    public function getAllKos(array $filters = [], string $orderBy = 'id', string $orderDir = 'ASC', int $limit = 9, int $offset = 0): array {
        $validOrderByColumns = ['id', 'nama_kos', 'harga_per_bulan', 'created_at', 'jumlah_kamar_tersedia'];
        $validOrderDirections = ['ASC', 'DESC'];

        $orderBy = in_array($orderBy, $validOrderByColumns) ? $orderBy : 'id';
        $orderDir = in_array(strtoupper($orderDir), $validOrderDirections) ? strtoupper($orderDir) : 'ASC';

        // Query dasar (kolom 'pemilik_id' sudah dihapus dari SELECT list)
        $sqlBase = "SELECT id, nama_kos, alamat, harga_per_bulan, status_kos, jumlah_kamar_total, jumlah_kamar_tersedia, kategori,
                           (SELECT gk.path FROM gambar_kos gk WHERE gk.kos_id = kos.id ORDER BY gk.id ASC LIMIT 1) as gambar_utama
                    FROM kos";
        
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $allowedStatusValues = ['available', 'booked', 'maintenance'];
            if (in_array($filters['status'], $allowedStatusValues)) {
                $conditions[] = "status_kos = :status_kos";
                $params[':status_kos'] = $filters['status'];
            }
        }
        if (!empty($filters['kategori'])) {
             $allowedKategori = ['putra', 'putri', 'campur'];
             if (in_array($filters['kategori'], $allowedKategori)) {
                $conditions[] = "kategori = :kategori";
                $params[':kategori'] = $filters['kategori'];
             }
        }
        if (!empty($filters['min_harga']) && is_numeric($filters['min_harga'])) {
            $conditions[] = "harga_per_bulan >= :min_harga";
            $params[':min_harga'] = (float)$filters['min_harga'];
        }
        if (!empty($filters['max_harga']) && is_numeric($filters['max_harga'])) {
            $conditions[] = "harga_per_bulan <= :max_harga";
            $params[':max_harga'] = (float)$filters['max_harga'];
        }
        if (!empty($filters['fasilitas'])) {
            $fasilitasArr = explode(',', $filters['fasilitas']);
            foreach ($fasilitasArr as $key => $fasil) {
                $trimmedFasil = trim($fasil);
                if (!empty($trimmedFasil)) {
                    $conditions[] = "fasilitas_kos LIKE :fasilitas{$key}";
                    $params[":fasilitas{$key}"] = '%' . $trimmedFasil . '%';
                }
            }
        }
        if (!empty($filters['search_term'])) {
            $conditions[] = "(nama_kos LIKE :search_term OR alamat LIKE :search_term_alamat)";
            $params[':search_term'] = '%' . $filters['search_term'] . '%';
            $params[':search_term_alamat'] = '%' . $filters['search_term'] . '%';
        }

        $sqlWhere = "";
        if (count($conditions) > 0) {
            $sqlWhere = " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql = $sqlBase . $sqlWhere . " ORDER BY {$orderBy} {$orderDir} LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => &$val) {
                if (is_int($val)) {
                    $stmt->bindValue($key, $val, PDO::PARAM_INT);
                } elseif (is_float($val)) { 
                    $stmt->bindValue($key, $val); 
                } else {
                    $stmt->bindValue($key, $val);
                }
            }
            unset($val); 
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("KosModel::getAllKos PDOException: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            return [];
        }
    }

    public function countAllKosFiltered(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM kos";
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) { $conditions[] = "status_kos = :status_kos"; $params[':status_kos'] = $filters['status']; }
        if (!empty($filters['kategori'])) { $conditions[] = "kategori = :kategori"; $params[':kategori'] = $filters['kategori']; }
        if (!empty($filters['min_harga']) && is_numeric($filters['min_harga'])) { $conditions[] = "harga_per_bulan >= :min_harga"; $params[':min_harga'] = (float)$filters['min_harga']; }
        if (!empty($filters['max_harga']) && is_numeric($filters['max_harga'])) { $conditions[] = "harga_per_bulan <= :max_harga"; $params[':max_harga'] = (float)$filters['max_harga']; }
        if (!empty($filters['fasilitas'])) {
            $fasilitasArr = explode(',', $filters['fasilitas']);
            foreach ($fasilitasArr as $key => $fasil) {
                $trimmedFasil = trim($fasil);
                if(!empty($trimmedFasil)){
                    $conditions[] = "fasilitas_kos LIKE :fasilitas{$key}";
                    $params[":fasilitas{$key}"] = '%' . $trimmedFasil . '%';
                }
            }
        }
        if (!empty($filters['search_term'])) { $conditions[] = "(nama_kos LIKE :search_term OR alamat LIKE :search_term_alamat)"; $params[':search_term'] = '%' . $filters['search_term'] . '%'; $params[':search_term_alamat'] = '%' . $filters['search_term'] . '%';}

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("KosModel::countAllKosFiltered PDOException: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            return 0;
        }
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
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $kos = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($kos) {
                $kos['gambar_kos'] = !empty($kos['gambar_kos_json']) ? json_decode($kos['gambar_kos_json'], true) : [];
                unset($kos['gambar_kos_json']); 
            }
            return $kos;
        } catch (PDOException $e) {
            error_log("KosModel::getKosById Error for ID {$id}: " . $e->getMessage());
            return false;
        }
    }
    
    public function createKos(array $data): ?string {
        $jumlah_kamar_total = $data['jumlah_kamar_total'] ?? 1;
        $jumlah_kamar_tersedia = $data['jumlah_kamar_tersedia'] ?? $jumlah_kamar_total; 
        $status_kos_input = $data['status_kos'] ?? null;
        $kategori_input = $data['kategori'] ?? null;

        if ($status_kos_input && in_array($status_kos_input, ['available', 'booked', 'maintenance'])) {
            $status_kos = $status_kos_input;
        } else {
            $status_kos = ($jumlah_kamar_tersedia > 0) ? 'available' : 'booked';
        }

        $sql = "INSERT INTO kos (nama_kos, alamat, deskripsi, harga_per_bulan, fasilitas_kos, kategori,
                                jumlah_kamar_total, jumlah_kamar_tersedia, status_kos) 
                VALUES (:nama_kos, :alamat, :deskripsi, :harga_per_bulan, :fasilitas_kos, :kategori,
                        :jumlah_kamar_total, :jumlah_kamar_tersedia, :status_kos)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nama_kos', $data['nama_kos']);
            $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':deskripsi', $data['deskripsi']);
            $stmt->bindParam(':harga_per_bulan', $data['harga_per_bulan']);
            $stmt->bindParam(':fasilitas_kos', $data['fasilitas_kos']);
            $stmt->bindParam(':kategori', $kategori_input, $kategori_input === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':jumlah_kamar_total', $jumlah_kamar_total, PDO::PARAM_INT);
            $stmt->bindParam(':jumlah_kamar_tersedia', $jumlah_kamar_tersedia, PDO::PARAM_INT);
            $stmt->bindParam(':status_kos', $status_kos);
            
            if ($stmt->execute()) { return $this->pdo->lastInsertId(); }
            return null;
        } catch (PDOException $e) {
            error_log("KosModel::createKos PDOException: " . $e->getMessage());
            return null;
        }
    }

    public function updateKos(int $id, array $data): bool {
        $sql = "UPDATE kos SET nama_kos = :nama_kos, alamat = :alamat, deskripsi = :deskripsi, 
                    harga_per_bulan = :harga_per_bulan, fasilitas_kos = :fasilitas_kos, kategori = :kategori,
                    jumlah_kamar_total = :jumlah_kamar_total, jumlah_kamar_tersedia = :jumlah_kamar_tersedia, 
                    status_kos = :status_kos, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nama_kos', $data['nama_kos']);
            $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':deskripsi', $data['deskripsi']);
            $stmt->bindParam(':harga_per_bulan', $data['harga_per_bulan']);
            $stmt->bindParam(':fasilitas_kos', $data['fasilitas_kos']);
            $stmt->bindParam(':kategori', $data['kategori'], ($data['kategori'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR));
            $stmt->bindParam(':jumlah_kamar_total', $data['jumlah_kamar_total'], PDO::PARAM_INT);
            $stmt->bindParam(':jumlah_kamar_tersedia', $data['jumlah_kamar_tersedia'], PDO::PARAM_INT);
            $stmt->bindParam(':status_kos', $data['status_kos']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("KosModel::updateKos Error for ID {$id}: " . $e->getMessage());
            return false;
        }
    }

    public function deleteKos(int $id): bool {
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
                    $this->pdo->commit(); return true; 
                }
            }
            $this->pdo->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("KosModel::deleteKos PDOException for ID {$id}: " . $e->getMessage());
            return false;
        }
    }    
    public function decrementKamarTersedia(int $kosId): bool {
        $sqlGetCurrent = "SELECT jumlah_kamar_tersedia, status_kos FROM kos WHERE id = :id FOR UPDATE";
        $stmtGetCurrent = $this->pdo->prepare($sqlGetCurrent);
        $stmtGetCurrent->bindParam(':id', $kosId, PDO::PARAM_INT);
        try {
            $stmtGetCurrent->execute();
            $currentKosData = $stmtGetCurrent->fetch(PDO::FETCH_ASSOC);
            if (!$currentKosData || ($currentKosData['jumlah_kamar_tersedia'] ?? 0) <= 0 || $currentKosData['status_kos'] !== 'available') {
                return false; 
            }
            $newJumlahTersedia = $currentKosData['jumlah_kamar_tersedia'] - 1;
            $newStatusKos = ($newJumlahTersedia <= 0) ? 'booked' : 'available';
            $sqlUpdate = "UPDATE kos SET jumlah_kamar_tersedia = :jumlah_kamar_tersedia, status_kos = :status_kos, updated_at = CURRENT_TIMESTAMP WHERE id = :id AND jumlah_kamar_tersedia > 0";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':jumlah_kamar_tersedia', $newJumlahTersedia, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':status_kos', $newStatusKos);
            $stmtUpdate->bindParam(':id', $kosId, PDO::PARAM_INT);
            if ($stmtUpdate->execute()) { return $stmtUpdate->rowCount() > 0; }
            return false;
        } catch (PDOException $e) {
            error_log("KosModel::decrementKamarTersedia PDOException for ID {$kosId}: " . $e->getMessage());
            return false;
        }
    }
    public function updateKosStatus(int $kosId, string $newStatus): bool {
        $allowedStatus = ['available', 'booked', 'maintenance'];
        if (!in_array($newStatus, $allowedStatus)) return false;
        $sql = "UPDATE kos SET status_kos = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':id', $kosId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("KosModel::updateKosStatus Error for ID {$kosId}: " . $e->getMessage());
            return false;
        }
    }
    public function addGambarKos(int $kos_id, string $nama_file, string $path): ?string {
        $sql = "INSERT INTO gambar_kos (kos_id, nama_file, path) VALUES (:kos_id, :nama_file, :path)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':kos_id', $kos_id, PDO::PARAM_INT);
            $stmt->bindParam(':nama_file', $nama_file);
            $stmt->bindParam(':path', $path);
            if ($stmt->execute()) { return $this->pdo->lastInsertId(); }
            return null;
        } catch (PDOException $e) {
            error_log("KosModel::addGambarKos Error for kos_id {$kos_id}: " . $e->getMessage());
            return null;
        }
    }
    public function deleteGambarKosById(int $gambar_id): ?array {
        $sqlGet = "SELECT id, kos_id, nama_file, path FROM gambar_kos WHERE id = :id";
        $stmtGet = $this->pdo->prepare($sqlGet);
        $stmtGet->bindParam(':id', $gambar_id, PDO::PARAM_INT);
        $stmtGet->execute();
        $gambarData = $stmtGet->fetch(PDO::FETCH_ASSOC);
        if ($gambarData) {
            $sqlDelete = "DELETE FROM gambar_kos WHERE id = :id";
            $stmtDelete = $this->pdo->prepare($sqlDelete);
            $stmtDelete->bindParam(':id', $gambar_id, PDO::PARAM_INT);
            if ($stmtDelete->execute() && $stmtDelete->rowCount() > 0) { return $gambarData; }
        }
        return null;
    }
    public function countTotalKos(): int {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM kos");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("KosModel::countTotalKos Error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
