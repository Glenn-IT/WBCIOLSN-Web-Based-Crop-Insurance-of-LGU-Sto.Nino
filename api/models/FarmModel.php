<?php
// ============================================================
// Farm Model
// ============================================================
require_once __DIR__ . '/BaseModel.php';

class FarmModel extends BaseModel {
    protected string $table = 'farms';

    public function getByUser(int $userId): array {
        return $this->raw(
            "SELECT f.*, c.name AS crop_name
             FROM farms f
             JOIN crop_types c ON c.id = f.crop_type_id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC",
            [$userId]
        );
    }

    public function getWithDetails(int $id): ?array {
        return $this->rawOne(
            "SELECT f.*, c.name AS crop_name, u.first_name, u.last_name, u.email
             FROM farms f
             JOIN crop_types c ON c.id = f.crop_type_id
             JOIN users u ON u.id = f.user_id
             WHERE f.id = ? LIMIT 1",
            [$id]
        );
    }

    public function getAllPaginated(int $offset, int $limit, string $search = ''): array {
        $where  = $search ? "f.farm_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?" : '';
        $params = $search ? ["%$search%", "%$search%", "%$search%"] : [];
        $sql    = "SELECT f.*, c.name AS crop_name, u.first_name, u.last_name
                   FROM farms f
                   JOIN crop_types c ON c.id = f.crop_type_id
                   JOIN users u ON u.id = f.user_id"
                   . ($where ? " WHERE $where" : '')
                   . " ORDER BY f.created_at DESC LIMIT ? OFFSET ?";
        return $this->raw($sql, [...$params, $limit, $offset]);
    }

    public function countAll(string $search = ''): int {
        $where  = $search ? "f.farm_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?" : '';
        $params = $search ? ["%$search%", "%$search%", "%$search%"] : [];
        $sql    = "SELECT COUNT(*) FROM farms f JOIN users u ON u.id = f.user_id"
                   . ($where ? " WHERE $where" : '');
        $stmt   = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
