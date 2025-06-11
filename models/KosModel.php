<?php

class KosModel {
    private PDO $pdo; // Correctly declared as pdo

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo; // Correctly assigned to pdo
        // If your PDO connection is not globally configured for error mode and fetch mode,
        // you might want to uncomment these lines:
        // $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves all 'kos' (boarding house) data with filtering, search, and pagination options.
     * Includes the path to the main image for each kos.
     *
     * @param array $filters Associative array of filters (status, kategori, min_harga, max_harga, fasilitas, search_term).
     * @param string $orderBy Column name to order results by.
     * @param string $orderDir Order direction ('ASC' or 'DESC').
     * @param int $limit Maximum number of results to return.
     * @param int $offset Offset for pagination.
     * @return array An array of kos data. Returns an empty array on failure.
     */
    public function getAllKos(array $filters = [], string $orderBy = 'id', string $orderDir = 'ASC', int $limit = 9, int $offset = 0): array {
        $validOrderByColumns = ['id', 'nama_kos', 'harga_per_bulan', 'created_at', 'jumlah_kamar_tersedia'];
        $validOrderDirections = ['ASC', 'DESC'];

        // Validate and sanitize orderBy and orderDir to prevent SQL injection.
        $orderBy = in_array($orderBy, $validOrderByColumns) ? $orderBy : 'id';
        $orderDir = in_array(strtoupper($orderDir), $validOrderDirections) ? strtoupper($orderDir) : 'ASC';

        // Base SQL query to select kos data and a subquery to get the main image.
        $sqlBase = "SELECT id, nama_kos, alamat, harga_per_bulan, status_kos, jumlah_kamar_total, jumlah_kamar_tersedia, kategori,
                             (SELECT gk.path FROM gambar_kos gk WHERE gk.kos_id = kos.id ORDER BY gk.id ASC LIMIT 1) as gambar_utama
                     FROM kos";
        
        $conditions = []; // Array to store WHERE clause conditions
        $params = [];     // Array to store PDO parameters for binding

        // Apply filters based on provided input.
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

        // Construct the WHERE clause if conditions exist.
        $sqlWhere = "";
        if (count($conditions) > 0) {
            $sqlWhere = " WHERE " . implode(' AND ', $conditions);
        }
        
        // Final SQL query with ordering, limit, and offset.
        $sql = $sqlBase . $sqlWhere . " ORDER BY {$orderBy} {$orderDir} LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->pdo->prepare($sql); // Correct: uses $this->pdo

            // Bind parameters dynamically based on their type.
            foreach ($params as $key => &$val) {
                if (is_int($val)) {
                    $stmt->bindValue($key, $val, PDO::PARAM_INT);
                } elseif (is_float($val)) { 
                    $stmt->bindValue($key, $val, PDO::PARAM_STR); // Explicitly bind floats as string
                } else {
                    $stmt->bindValue($key, $val, PDO::PARAM_STR);
                }
            }
            unset($val);

            // Bind limit and offset parameters as integers.
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("KosModel::getAllKos PDOException: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            return [];
        }
    }

    /**
     * Counts the total number of 'kos' items after applying filters.
     *
     * @param array $filters Associative array of filters.
     * @return int The total count of filtered kos. Returns 0 on failure.
     */
    public function countAllKosFiltered(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM kos";
        $conditions = [];
        $params = [];

        // Conditions logic is identical to getAllKos for consistency.
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
        if (!empty($filters['search_term'])) {
            $conditions[] = "(nama_kos LIKE :search_term OR alamat LIKE :search_term_alamat)";
            $params[':search_term'] = '%' . $filters['search_term'] . '%';
            $params[':search_term_alamat'] = '%' . $filters['search_term'] . '%';
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        try {
            $stmt = $this->pdo->prepare($sql); // Correct: uses $this->pdo
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("KosModel::countAllKosFiltered PDOException: " . $e->getMessage() . " | SQL: " . $sql . " | Params: " . print_r($params, true));
            return 0;
        }
    }

    /**
     * Retrieves a single kos item by its ID, including all associated images.
     * Uses JSON_ARRAYAGG to fetch images efficiently.
     *
     * @param int $id The ID of the kos.
     * @return array|false The kos data with an array of images, or false if not found/error.
     */
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
            $stmt = $this->pdo->prepare($sql); // Correct: uses $this->pdo
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
    
    /**
     * Creates a new kos (boarding house) record.
     * Automatically sets initial `jumlah_kamar_tersedia` and `status_kos`.
     *
     * @param array $data Associative array of kos data (nama_kos, alamat, deskripsi, etc.).
     * @return string|null The ID of the newly created kos if successful, null otherwise.
     */
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
            $stmt = $this->pdo->prepare($sql); // Correct: uses $this->pdo
            $stmt->bindParam(':nama_kos', $data['nama_kos']);
            $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':deskripsi', $data['deskripsi']);
            $stmt->bindParam(':harga_per_bulan', $data['harga_per_bulan']);
            $stmt->bindParam(':fasilitas_kos', $data['fasilitas_kos']);
            $stmt->bindParam(':kategori', $kategori_input, $kategori_input === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':jumlah_kamar_total', $jumlah_kamar_total, PDO::PARAM_INT);
            $stmt->bindParam(':jumlah_kamar_tersedia', $jumlah_kamar_tersedia, PDO::PARAM_INT);
            $stmt->bindParam(':status_kos', $status_kos);
            
            if ($stmt->execute()) { return $this->pdo->lastInsertId(); } // Correct: uses $this->pdo
            return null;
        } catch (PDOException $e) {
            error_log("KosModel::createKos PDOException: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Updates an existing kos (boarding house) record.
     *
     * @param int $id The ID of the kos to update.
     * @param array $data Associative array of updated kos data.
     * @return bool True on success, false on failure.
     */
    public function updateKos(int $id, array $data): bool {
        $sql = "UPDATE kos SET nama_kos = :nama_kos, alamat = :alamat, deskripsi = :deskripsi,
                     harga_per_bulan = :harga_per_bulan, fasilitas_kos = :fasilitas_kos, kategori = :kategori,
                     jumlah_kamar_total = :jumlah_kamar_total, jumlah_kamar_tersedia = :jumlah_kamar_tersedia,
                     status_kos = :status_kos, updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql); // Correct: uses $this->pdo
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

    /**
     * Deletes a kos record and all associated images.
     * Uses a database transaction to ensure atomicity.
     *
     * @param int $id The ID of the kos to delete.
     * @return bool True on successful deletion, false otherwise.
     */
    public function deleteKos(int $id): bool {
        $this->pdo->beginTransaction(); // Correct: uses $this->pdo
        try {
            $sqlDeleteGambar = "DELETE FROM gambar_kos WHERE kos_id = :kos_id";
            $stmtDeleteGambar = $this->pdo->prepare($sqlDeleteGambar); // Correct: uses $this->pdo
            $stmtDeleteGambar->bindParam(':kos_id', $id, PDO::PARAM_INT);
            $stmtDeleteGambar->execute();

            $sqlDeleteKos = "DELETE FROM kos WHERE id = :id";
            $stmtDeleteKos = $this->pdo->prepare($sqlDeleteKos); // Correct: uses $this->pdo
            $stmtDeleteKos->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmtDeleteKos->execute()) {
                if ($stmtDeleteKos->rowCount() > 0) {
                    $this->pdo->commit(); return true; // Correct: uses $this->pdo
                }
            }
            $this->pdo->rollBack(); // Correct: uses $this->pdo
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack(); // Correct: uses $this->pdo
            error_log("KosModel::deleteKos PDOException for ID {$id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Decrements the number of available rooms for a kos.
     * Updates `status_kos` to 'booked' if available rooms become 0.
     * Uses `FOR UPDATE` for row-level locking during the transaction.
     *
     * @param int $kosId The ID of the kos to update.
     * @return bool True if successful (room decremented), false otherwise (e.g., no rooms available or kos not found).
     */
    public function decrementKamarTersedia(int $kosId): bool {
        // --- MODIFIED: Removed beginTransaction(), commit(), rollBack() ---
        try {
            // Select current available count and status with a row-level lock
            // FOR UPDATE is still good practice to prevent race conditions even without explicit beginTransaction here
            $sqlGetCurrent = "SELECT jumlah_kamar_tersedia, status_kos FROM kos WHERE id = :id FOR UPDATE";
            $stmtGetCurrent = $this->pdo->prepare($sqlGetCurrent); // Correct: uses $this->pdo
            $stmtGetCurrent->bindParam(':id', $kosId, PDO::PARAM_INT);
            $stmtGetCurrent->execute();
            $currentKosData = $stmtGetCurrent->fetch(PDO::FETCH_ASSOC);

            // Check if kos exists, has available rooms, and is currently 'available' (not 'maintenance')
            if (!$currentKosData || ($currentKosData['jumlah_kamar_tersedia'] ?? 0) <= 0 || $currentKosData['status_kos'] !== 'available') {
                return false; // Return false if conditions not met (controller will then handle the rollback)
            }

            $newJumlahTersedia = $currentKosData['jumlah_kamar_tersedia'] - 1;
            $newStatusKos = ($newJumlahTersedia <= 0) ? 'booked' : 'available';

            // Update the kos record
            $sqlUpdate = "UPDATE kos SET jumlah_kamar_tersedia = :jumlah_kamar_tersedia, status_kos = :status_kos, updated_at = CURRENT_TIMESTAMP WHERE id = :id AND jumlah_kamar_tersedia > 0";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate); // Correct: uses $this->pdo
            $stmtUpdate->bindParam(':jumlah_kamar_tersedia', $newJumlahTersedia, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':status_kos', $newStatusKos);
            $stmtUpdate->bindParam(':id', $kosId, PDO::PARAM_INT);

            if ($stmtUpdate->execute()) {
                return $stmtUpdate->rowCount() > 0; // Return true if row affected
            }
            return false; // Return false if update failed or no row affected
        } catch (PDOException $e) {
            // Re-throw PDOException so the controller's catch block can handle the rollback
            error_log("KosModel::decrementKamarTersedia PDOException for ID {$kosId}: " . $e->getMessage());
            throw $e; // Re-throw the exception
        }
        // --- END MODIFIED ---
    }

    /**
     * Updates the general status of a kos (e.g., 'available', 'booked', 'maintenance').
     *
     * @param int $kosId The ID of the kos to update.
     * @param string $newStatus The new status to set. Must be one of 'available', 'booked', 'maintenance'.
     * @return bool True on success, false on failure (e.g., invalid status, update failed).
     */
    public function updateKosStatus(int $kosId, string $newStatus): bool {
        $allowedStatus = ['available', 'booked', 'maintenance'];
        if (!in_array($newStatus, $allowedStatus)) return false;
        $sql = "UPDATE kos SET status_kos = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($sql); // Correct: uses $this->pdo
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':id', $kosId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("KosModel::updateKosStatus Error for ID {$kosId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Adds a new image record for a specific kos.
     *
     * @param int $kos_id The ID of the kos the image belongs to.
     * @param string $nama_file The original name of the image file.
     * @param string $path The stored path/filename of the image on the server.
     * @return string|null The ID of the newly added image record if successful, null otherwise.
     */
    public function addGambarKos(int $kos_id, string $nama_file, string $path): ?string {
        $sql = "INSERT INTO gambar_kos (kos_id, nama_file, path) VALUES (:kos_id, :nama_file, :path)";
        try {
            $stmt = $this->pdo->prepare($sql); // Correct: uses $this->pdo
            $stmt->bindParam(':kos_id', $kos_id, PDO::PARAM_INT);
            $stmt->bindParam(':nama_file', $nama_file);
            $stmt->bindParam(':path', $path);
            if ($stmt->execute()) { return $this->pdo->lastInsertId(); } // Correct: uses $this->pdo
            return null;
        } catch (PDOException $e) {
            error_log("KosModel::addGambarKos Error for kos_id {$kos_id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves the path of a single image by its ID.
     *
     * @param int $gambarId The ID of the image record.
     * @return string|null The path of the image if found, null otherwise.
     */
    public function getGambarPathById(int $gambarId): ?string {
        $stmt = $this->pdo->prepare("SELECT path FROM gambar_kos WHERE id = :id"); // FIXED: Changed $this->db to $this->pdo and kos_images to gambar_kos
        $stmt->bindParam(':id', $gambarId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['path'] : null;
    }

    /**
     * Deletes an image record by its ID.
     *
     * @param int $gambar_id The ID of the image record to delete.
     * @return array|null The deleted image data (id, kos_id, nama_file, path) if successful, null otherwise.
     */
    public function deleteGambarKosById(int $gambar_id): ?array {
        // First, retrieve the image data before deleting the record
        $sqlGet = "SELECT id, kos_id, nama_file, path FROM gambar_kos WHERE id = :id";
        $stmtGet = $this->pdo->prepare($sqlGet); // Correct: uses $this->pdo
        $stmtGet->bindParam(':id', $gambar_id, PDO::PARAM_INT);
        $stmtGet->execute();
        $gambarData = $stmtGet->fetch(PDO::FETCH_ASSOC);

        if ($gambarData) {
            // Then, delete the record
            $sqlDelete = "DELETE FROM gambar_kos WHERE id = :id";
            $stmtDelete = $this->pdo->prepare($sqlDelete); // Correct: uses $this->pdo
            $stmtDelete->bindParam(':id', $gambar_id, PDO::PARAM_INT);
            if ($stmtDelete->execute() && $stmtDelete->rowCount() > 0) {
                return $gambarData; // Return the fetched data (including path)
            }
        }
        return null; // Return null if not found or deletion failed
    }

    /**
     * Counts the total number of kos items in the database.
     *
     * @return int The total count of kos. Returns 0 on failure.
     */
    public function countTotalKos(): int {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM kos"); // Correct: uses $this->pdo
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("KosModel::countTotalKos Error: " . $e->getMessage());
            return 0;
        }
    }
}