<?php

class UserModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Registrasi pengguna baru.
     * @return bool|string ID pengguna jika berhasil, false jika gagal.
     */
    public function registerUser(string $nama, string $email, string $password, ?string $no_telepon = null, ?string $alamat = null) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $isAdmin = false; // Default pengguna baru bukan admin

        $sql = "INSERT INTO users (nama, email, password, is_admin, no_telepon, alamat) 
                VALUES (:nama, :email, :password, :is_admin, :no_telepon, :alamat)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':is_admin', $isAdmin, PDO::PARAM_BOOL);
            $stmt->bindParam(':no_telepon', $no_telepon);
            $stmt->bindParam(':alamat', $alamat);
            
            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            // error_log("UserModel::registerUser Error: " . $e->getMessage()); // Log error
            return false;
        }
    }

    /**
     * Cari pengguna berdasarkan alamat email.
     * @return array|false Data pengguna jika ditemukan, false jika tidak.
     */
    public function getUserByEmail(string $email) {
        $sql = "SELECT id, nama, email, password, is_admin FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cari pengguna berdasarkan ID.
     * @return array|false Data pengguna jika ditemukan, false jika tidak.
     */
    public function getUserById(int $id) {
        $sql = "SELECT id, nama, email, is_admin, no_telepon, alamat FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Memperbarui profil pengguna (nama, no_telepon, alamat).
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateUserProfile(int $userId, string $nama, ?string $no_telepon, ?string $alamat): bool {
        $sql = "UPDATE users 
                SET nama = :nama, 
                    no_telepon = :no_telepon, 
                    alamat = :alamat, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :user_id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':no_telepon', $no_telepon); // PDO akan handle null dengan benar
            $stmt->bindParam(':alamat', $alamat);       // PDO akan handle null dengan benar
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Catat error ke log server di lingkungan produksi
            error_log("UserModel::updateUserProfile Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil semua data pengguna (untuk admin).
     * @return array Daftar semua pengguna.
     */
    public function getAllUsers(): array {
        $sql = "SELECT id, nama, email, no_telepon, alamat, is_admin, created_at 
                FROM users 
                ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Memperbarui data pengguna oleh Admin.
     * Admin bisa mengubah nama, no_telepon, alamat, dan status is_admin.
     * Email dan password tidak diubah di sini untuk kesederhanaan/keamanan awal.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateUserByAdmin(int $userId, string $nama, ?string $no_telepon, ?string $alamat, bool $isAdmin): bool {
        $sql = "UPDATE users 
                SET nama = :nama, 
                    no_telepon = :no_telepon, 
                    alamat = :alamat, 
                    is_admin = :is_admin,
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :user_id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':no_telepon', $no_telepon);
            $stmt->bindParam(':alamat', $alamat);
            $stmt->bindParam(':is_admin', $isAdmin, PDO::PARAM_BOOL);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("UserModel::updateUserByAdmin Error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllNonAdminUsers(): array {
        $sql = "SELECT id, nama, email 
                FROM users 
                WHERE is_admin = FALSE OR is_admin = 0 OR is_admin IS NULL 
                ORDER BY nama ASC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("UserModel::getAllNonAdminUsers PDOException: " . $e->getMessage());
            return [];
        }
    }
}
?>