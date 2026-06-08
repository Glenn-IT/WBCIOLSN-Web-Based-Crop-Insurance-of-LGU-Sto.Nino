<?php
// ============================================================
// Report Model
// Aggregated queries for dashboard stats & reports
// Web-Based Crop Insurance System
// ============================================================
require_once __DIR__ . '/BaseModel.php';

class ReportModel extends BaseModel {
    protected string $table = 'audit_logs'; // placeholder — uses raw queries

    // ----------------------------------------------------------
    // Dashboard summary stats
    // ----------------------------------------------------------

    public function dashboardStats(): array {
        $db = $this->db;

        $scalar = fn(string $sql) => (float)$db->query($sql)->fetchColumn();

        return [
            'users' => [
                'total'   => (int)$scalar("SELECT COUNT(*) FROM users"),
                'farmers' => (int)$scalar("SELECT COUNT(*) FROM users WHERE role='farmer'"),
                'agents'  => (int)$scalar("SELECT COUNT(*) FROM users WHERE role='agent'"),
                'active'  => (int)$scalar("SELECT COUNT(*) FROM users WHERE status='active'"),
            ],
            'farms' => [
                'total'          => (int)$scalar("SELECT COUNT(*) FROM farms"),
                'total_hectares' => round($scalar("SELECT COALESCE(SUM(area_hectares),0) FROM farms"), 4),
            ],
            'policies' => [
                'total'    => (int)$scalar("SELECT COUNT(*) FROM policies"),
                'pending'  => (int)$scalar("SELECT COUNT(*) FROM policies WHERE status='pending'"),
                'active'   => (int)$scalar("SELECT COUNT(*) FROM policies WHERE status='active'"),
                'expired'  => (int)$scalar("SELECT COUNT(*) FROM policies WHERE status='expired'"),
                'cancelled'=> (int)$scalar("SELECT COUNT(*) FROM policies WHERE status='cancelled'"),
                'rejected' => (int)$scalar("SELECT COUNT(*) FROM policies WHERE status='rejected'"),
            ],
            'claims' => [
                'total'       => (int)$scalar("SELECT COUNT(*) FROM claims"),
                'submitted'   => (int)$scalar("SELECT COUNT(*) FROM claims WHERE status='submitted'"),
                'under_review'=> (int)$scalar("SELECT COUNT(*) FROM claims WHERE status='under_review'"),
                'approved'    => (int)$scalar("SELECT COUNT(*) FROM claims WHERE status='approved'"),
                'rejected'    => (int)$scalar("SELECT COUNT(*) FROM claims WHERE status='rejected'"),
                'paid'        => (int)$scalar("SELECT COUNT(*) FROM claims WHERE status='paid'"),
                'total_estimated_loss' => round($scalar("SELECT COALESCE(SUM(estimated_loss),0) FROM claims"), 2),
                'total_approved_amount'=> round($scalar("SELECT COALESCE(SUM(approved_amount),0) FROM claims WHERE status IN ('approved','paid')"), 2),
            ],
            'payments' => [
                'total_premiums_collected' => round($scalar("SELECT COALESCE(SUM(amount),0) FROM payments WHERE type='premium' AND status='completed'"), 2),
                'total_payouts_disbursed'  => round($scalar("SELECT COALESCE(SUM(amount),0) FROM payments WHERE type='payout'  AND status='completed'"), 2),
                'net_revenue'              => round(
                    $scalar("SELECT COALESCE(SUM(amount),0) FROM payments WHERE type='premium' AND status='completed'") -
                    $scalar("SELECT COALESCE(SUM(amount),0) FROM payments WHERE type='payout'  AND status='completed'"),
                    2
                ),
            ],
        ];
    }

    // ----------------------------------------------------------
    // Farmer-specific dashboard stats
    // ----------------------------------------------------------

    public function farmerStats(int $userId): array {
        $db = $this->db;

        // Use raw SQL with explicit table names to avoid the $this->table = 'audit_logs' bug
        $count = function(string $table, string $where, array $params) use ($db): int {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `$table` WHERE $where");
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        };

        return [
            'farms'    => $count('farms', 'user_id = ?', [$userId]),
            'policies' => [
                'total'   => $count('policies', 'user_id = ?', [$userId]),
                'active'  => $count('policies', 'user_id = ? AND status = ?', [$userId, 'active']),
                'pending' => $count('policies', 'user_id = ? AND status = ?', [$userId, 'pending']),
                'rejected'=> $count('policies', 'user_id = ? AND status = ?', [$userId, 'rejected']),
            ],
            'claims'   => [
                'total'    => $count('claims', 'user_id = ?', [$userId]),
                'pending'  => $count('claims', 'user_id = ? AND status IN (?,?)', [$userId, 'submitted', 'under_review']),
                'approved' => $count('claims', 'user_id = ? AND status = ?', [$userId, 'approved']),
            ],
            'payments' => [
                'total_premium_paid' => (float)$this->rawOne(
                    "SELECT COALESCE(SUM(amount),0) AS t FROM payments WHERE user_id=? AND type='premium' AND status='completed'",
                    [$userId]
                )['t'] ?? 0,
                'total_payout_received' => (float)$this->rawOne(
                    "SELECT COALESCE(SUM(amount),0) AS t FROM payments WHERE user_id=? AND type='payout' AND status='completed'",
                    [$userId]
                )['t'] ?? 0,
            ],
        ];
    }

    // ----------------------------------------------------------
    // Claims report
    // ----------------------------------------------------------

    public function claimsReport(array $filters): array {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'c.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['date_from'])) {
            $where[]  = 'c.incident_date >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[]  = 'c.incident_date <= ?';
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT c.claim_number, c.incident_type, c.incident_date,
                       c.estimated_loss, c.approved_amount, c.status,
                       c.created_at,
                       CONCAT(u.first_name,' ',u.last_name) AS farmer_name,
                       p.policy_number, f.farm_name, f.location
                FROM claims c
                JOIN users u    ON u.id = c.user_id
                JOIN policies p ON p.id = c.policy_id
                JOIN farms f    ON f.id = p.farm_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY c.created_at DESC";

        return $this->raw($sql, $params);
    }

    // ----------------------------------------------------------
    // Policies report
    // ----------------------------------------------------------

    public function policiesReport(array $filters): array {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'p.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['date_from'])) {
            $where[]  = 'p.start_date >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[]  = 'p.start_date <= ?';
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT p.policy_number, p.status, p.start_date, p.end_date,
                       p.total_premium, p.coverage_amount, p.created_at,
                       CONCAT(u.first_name,' ',u.last_name) AS farmer_name,
                       pl.plan_name, f.farm_name, f.area_hectares
                FROM policies p
                JOIN users u          ON u.id  = p.user_id
                JOIN coverage_plans pl ON pl.id = p.plan_id
                JOIN farms f           ON f.id  = p.farm_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.created_at DESC";

        return $this->raw($sql, $params);
    }

    // ----------------------------------------------------------
    // Payments report
    // ----------------------------------------------------------

    public function paymentsReport(array $filters): array {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['type'])) {
            $where[]  = 'py.type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['date_from'])) {
            $where[]  = 'DATE(py.transaction_date) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[]  = 'DATE(py.transaction_date) <= ?';
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT py.reference_number, py.type, py.amount, py.method,
                       py.status, py.transaction_date,
                       CONCAT(u.first_name,' ',u.last_name) AS farmer_name,
                       p.policy_number
                FROM payments py
                JOIN users u         ON u.id  = py.user_id
                LEFT JOIN policies p ON p.id  = py.policy_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY py.transaction_date DESC";

        return $this->raw($sql, $params);
    }

    // ----------------------------------------------------------
    // Monthly premium trend (last 12 months)
    // ----------------------------------------------------------

    public function monthlyPremiumTrend(): array {
        return $this->raw(
            "SELECT DATE_FORMAT(transaction_date, '%Y-%m') AS month,
                    COALESCE(SUM(amount), 0) AS total
             FROM payments
             WHERE type = 'premium' AND status = 'completed'
               AND transaction_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month
             ORDER BY month ASC"
        );
    }

    // ----------------------------------------------------------
    // Claims by crop type
    // ----------------------------------------------------------

    public function claimsByCropType(): array {
        return $this->raw(
            "SELECT ct.name AS crop_type,
                    COUNT(c.id) AS total_claims,
                    COALESCE(SUM(c.estimated_loss), 0) AS total_estimated_loss
             FROM claims c
             JOIN policies p  ON p.id  = c.policy_id
             JOIN farms f     ON f.id  = p.farm_id
             JOIN crop_types ct ON ct.id = f.crop_type_id
             GROUP BY ct.id, ct.name
             ORDER BY total_claims DESC"
        );
    }

    // ----------------------------------------------------------
    // Top 10 provinces with most claims
    // ----------------------------------------------------------

    public function claimsByProvince(): array {
        return $this->raw(
            "SELECT f.province,
                    COUNT(c.id) AS total_claims,
                    COALESCE(SUM(c.approved_amount), 0) AS total_approved
             FROM claims c
             JOIN policies p ON p.id = c.policy_id
             JOIN farms f    ON f.id = p.farm_id
             WHERE f.province IS NOT NULL AND f.province != ''
             GROUP BY f.province
             ORDER BY total_claims DESC
             LIMIT 10"
        );
    }
}
