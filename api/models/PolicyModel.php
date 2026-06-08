<?php
// ============================================================
// Policy Model
// ============================================================
require_once __DIR__ . '/BaseModel.php';

class PolicyModel extends BaseModel {
    protected string $table = 'policies';

    public function generatePolicyNumber(): string {
        $year = date('Y');
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM policies WHERE YEAR(created_at) = $year"
        );
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'POL-' . $year . '-' . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    public function getWithDetails(int $id): ?array {
        return $this->rawOne(
            "SELECT p.*,
                    u.first_name, u.last_name, u.email, u.phone,
                    f.farm_name, f.location AS farm_location, f.area_hectares,
                    ct.name AS crop_type,
                    pl.plan_name, pl.coverage_type,
                    CONCAT(a.first_name,' ',a.last_name) AS agent_name
             FROM policies p
             JOIN users u   ON u.id  = p.user_id
             JOIN farms f   ON f.id  = p.farm_id
             JOIN coverage_plans pl ON pl.id = p.plan_id
             LEFT JOIN crop_types ct ON ct.id = f.crop_type_id
             LEFT JOIN users a ON a.id = p.agent_id
             WHERE p.id = ? LIMIT 1",
            [$id]
        );
    }

    public function getByUser(int $userId, int $offset, int $limit): array {
        return $this->raw(
            "SELECT p.*, pl.plan_name, f.farm_name,
                    f.location AS farm_location, f.area_hectares,
                    ct.name AS crop_type
             FROM policies p
             JOIN coverage_plans pl ON pl.id = p.plan_id
             JOIN farms f ON f.id = p.farm_id
             LEFT JOIN crop_types ct ON ct.id = f.crop_type_id
             WHERE p.user_id = ?
             ORDER BY p.created_at DESC LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }

    public function countByUser(int $userId): int {
        return $this->count('user_id = ?', [$userId]);
    }

    public function getAllPaginated(int $offset, int $limit, string $status = ''): array {
        $where  = $status ? 'p.status = ?' : '';
        $params = $status ? [$status] : [];
        $sql = "SELECT p.*, u.first_name, u.last_name, pl.plan_name, f.farm_name,
                       f.location AS farm_location, f.area_hectares,
                       ct.name AS crop_type
                FROM policies p
                JOIN users u ON u.id = p.user_id
                JOIN coverage_plans pl ON pl.id = p.plan_id
                JOIN farms f ON f.id = p.farm_id
                LEFT JOIN crop_types ct ON ct.id = f.crop_type_id"
                . ($where ? " WHERE $where" : '')
                . " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        return $this->raw($sql, [...$params, $limit, $offset]);
    }

    public function countAll(string $status = ''): int {
        if ($status) return $this->count('status = ?', [$status]);
        return $this->count();
    }
}
