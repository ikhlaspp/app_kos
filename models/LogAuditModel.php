<?php

class LogAuditModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function addLog(string $aksi, ?int $userId = null, ?string $detailAksi = null): bool {
        $sql = "INSERT INTO log_audit (user_id, aksi, detail_aksi) VALUES (:user_id, :aksi, :detail_aksi)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, $userId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':aksi', $aksi);
            $stmt->bindParam(':detail_aksi', $detailAksi, $detailAksi === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("LogAuditModel::addLog Error: " . $e->getMessage());
            return false;
        }
    }

    public function getRecentLogs(int $limit = 10): array {
        $sql = "SELECT la.*, u.nama AS nama_pengguna, u.email AS email_pengguna
                FROM log_audit la
                LEFT JOIN users u ON la.user_id = u.id
                ORDER BY la.timestamp DESC
                LIMIT :limit";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("LogAuditModel::getRecentLogs Error: " . $e->getMessage());
            return [];
        }
    }
}