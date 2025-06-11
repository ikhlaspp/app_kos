<?php

class VoucherModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createVoucher(array $data) {
        $sql = "INSERT INTO vouchers (code, name, description, type, value, min_transaction_amount, max_discount_amount, usage_limit_per_user, total_usage_limit, expiration_date, is_active, is_claimable_by_new_users, created_by_admin_id)
                VALUES (:code, :name, :description, :type, :value, :min_transaction_amount, :max_discount_amount, :usage_limit_per_user, :total_usage_limit, :expiration_date, :is_active, :is_claimable_by_new_users, :created_by_admin_id)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $data['code']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':description', $data['description'] ?? null);
        $stmt->bindValue(':type', $data['type']);
        $stmt->bindValue(':value', $data['value']);
        $stmt->bindValue(':min_transaction_amount', $data['min_transaction_amount'] ?? null);
        $stmt->bindValue(':max_discount_amount', $data['max_discount_amount'] ?? null);
        $stmt->bindValue(':usage_limit_per_user', $data['usage_limit_per_user'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':total_usage_limit', $data['total_usage_limit'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':expiration_date', $data['expiration_date']);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':is_claimable_by_new_users', $data['is_claimable_by_new_users'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':created_by_admin_id', $data['created_by_admin_id'] ?? null, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    public function updateVoucher(int $id, array $data) {
        $sql = "UPDATE vouchers SET
                    code = :code, name = :name, description = :description, type = :type, value = :value,
                    min_transaction_amount = :min_transaction_amount, max_discount_amount = :max_discount_amount,
                    usage_limit_per_user = :usage_limit_per_user, total_usage_limit = :total_usage_limit,
                    expiration_date = :expiration_date, is_active = :is_active, is_claimable_by_new_users = :is_claimable_by_new_users
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $data['code']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':description', $data['description'] ?? null);
        $stmt->bindValue(':type', $data['type']);
        $stmt->bindValue(':value', $data['value']);
        $stmt->bindValue(':min_transaction_amount', $data['min_transaction_amount'] ?? null);
        $stmt->bindValue(':max_discount_amount', $data['max_discount_amount'] ?? null);
        $stmt->bindValue(':usage_limit_per_user', $data['usage_limit_per_user'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':total_usage_limit', $data['total_usage_limit'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':expiration_date', $data['expiration_date']);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':is_claimable_by_new_users', $data['is_claimable_by_new_users'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteVoucher(int $id) {
        $sql = "DELETE FROM vouchers WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getVoucherById(int $id) {
        $sql = "SELECT * FROM vouchers WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getVoucherByCode(string $code) {
        $sql = "SELECT * FROM vouchers WHERE code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllVouchers() {
        $sql = "SELECT * FROM vouchers ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableVouchersForClaiming(int $userId) {
        $sql = "SELECT v.*
                FROM vouchers v
                LEFT JOIN user_vouchers uv ON v.id = uv.voucher_id AND uv.user_id = :user_id
                WHERE v.is_active = 1
                AND v.expiration_date > NOW()
                AND (v.total_usage_limit IS NULL OR v.current_total_uses < v.total_usage_limit)
                AND uv.voucher_id IS NULL -- Only show if NOT claimed by this user yet (no entry in user_vouchers)
                ORDER BY v.expiration_date ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserClaimedVouchers(int $userId) {
        $sql = "SELECT uv.*, v.code, v.name, v.description, v.type, v.value, v.min_transaction_amount, 
                       v.max_discount_amount, v.usage_limit_per_user, v.expiration_date, v.is_active
                FROM user_vouchers uv
                JOIN vouchers v ON uv.voucher_id = v.id
                WHERE uv.user_id = :user_id
                ORDER BY uv.claimed_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function claimVoucher(int $userId, int $voucherId, string $initialStatus = 'claimed') {
        try {
            $sql = "INSERT INTO user_vouchers (user_id, voucher_id, status) VALUES (:user_id, :voucher_id, :status)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':voucher_id' => $voucherId,
                ':status' => $initialStatus
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                error_log("Voucher already claimed by user: " . $userId . " for voucher " . $voucherId);
                return false;
            }
            throw $e;
        }
    }

    public function markVoucherAsUsed(int $userId, int $voucherId) {
        $this->pdo->beginTransaction();
        try {
            $sqlUserVoucher = "UPDATE user_vouchers
                               SET used_at = NOW(), times_used = times_used + 1, status = 'used'
                               WHERE user_id = :user_id AND voucher_id = :voucher_id";
            $stmtUserVoucher = $this->pdo->prepare($sqlUserVoucher);
            $stmtUserVoucher->execute([':user_id' => $userId, ':voucher_id' => $voucherId]);

            $sqlVoucher = "UPDATE vouchers
                           SET current_total_uses = current_total_uses + 1
                           WHERE id = :voucher_id";
            $stmtVoucher = $this->pdo->prepare($sqlVoucher);
            $stmtVoucher->execute([':voucher_id' => $voucherId]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Failed to mark voucher as used: " . $e->getMessage());
            return false;
        }
    }

    public function getUserVoucher(int $userId, int $voucherId) {
        $sql = "SELECT * FROM user_vouchers WHERE user_id = :user_id AND voucher_id = :voucher_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':voucher_id' => $voucherId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
}