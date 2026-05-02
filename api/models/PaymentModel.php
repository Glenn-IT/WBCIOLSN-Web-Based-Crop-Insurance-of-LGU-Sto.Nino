<?php
// ============================================================
// Payment Model
// ============================================================
require_once __DIR__ . '/BaseModel.php';

class PaymentModel extends BaseModel {
    protected string $table = 'payments';

    public function generateReference(): string {
        $year = date('Y');
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM payments WHERE YEAR(created_at) = $year"
        );
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'PAY-' . $year . '-' . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    public function getByUser(int $userId, int $offset, int $limit): array {
        return $this->raw(
            "SELECT py.*, p.policy_number
             FROM payments py
             LEFT JOIN policies p ON p.id = py.policy_id
             WHERE py.user_id = ?
             ORDER BY py.created_at DESC LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }

    public function countByUser(int $userId): int {
        return $this->count('user_id = ?', [$userId]);
    }

    public function getAllPaginated(int $offset, int $limit, string $type = ''): array {
        $where  = $type ? 'py.type = ?' : '';
        $params = $type ? [$type] : [];
        $sql = "SELECT py.*, u.first_name, u.last_name, p.policy_number
                FROM payments py
                JOIN users u ON u.id = py.user_id
                LEFT JOIN policies p ON p.id = py.policy_id"
                . ($where ? " WHERE $where" : '')
                . " ORDER BY py.created_at DESC LIMIT ? OFFSET ?";
        return $this->raw($sql, [...$params, $limit, $offset]);
    }

    public function countAll(string $type = ''): int {
        if ($type) return $this->count('type = ?', [$type]);
        return $this->count();
    }

    public function totalRevenue(): float {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(amount),0) FROM payments WHERE type='premium' AND status='completed'"
        );
        return (float)$stmt->fetchColumn();
    }

    public function totalPayouts(): float {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(amount),0) FROM payments WHERE type='payout' AND status='completed'"
        );
        return (float)$stmt->fetchColumn();
    }
}
