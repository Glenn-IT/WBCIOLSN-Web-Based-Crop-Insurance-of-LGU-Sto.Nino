<?php
// ============================================================
// Claim Model
// ============================================================
require_once __DIR__ . '/BaseModel.php';

class ClaimModel extends BaseModel {
    protected string $table = 'claims';

    public function generateClaimNumber(): string {
        $year = date('Y');
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM claims WHERE YEAR(created_at) = $year"
        );
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'CLM-' . $year . '-' . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    public function getWithDetails(int $id): ?array {
        return $this->rawOne(
            "SELECT c.*,
                    u.first_name, u.last_name, u.email, u.phone,
                    p.policy_number, p.coverage_amount,
                    f.farm_name, f.location,
                    CONCAT(r.first_name,' ',r.last_name) AS reviewer_name
             FROM claims c
             JOIN users u      ON u.id = c.user_id
             JOIN policies p   ON p.id = c.policy_id
             JOIN farms f      ON f.id = p.farm_id
             LEFT JOIN users r ON r.id = c.reviewed_by
             WHERE c.id = ? LIMIT 1",
            [$id]
        );
    }

    public function getByUser(int $userId, int $offset, int $limit): array {
        return $this->raw(
            "SELECT c.*, p.policy_number, f.farm_name
             FROM claims c
             JOIN policies p ON p.id = c.policy_id
             JOIN farms f    ON f.id = p.farm_id
             WHERE c.user_id = ?
             ORDER BY c.created_at DESC LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }

    public function countByUser(int $userId): int {
        return $this->count('user_id = ?', [$userId]);
    }

    public function getAllPaginated(int $offset, int $limit, string $status = ''): array {
        $where  = $status ? 'c.status = ?' : '';
        $params = $status ? [$status] : [];
        $sql = "SELECT c.*, u.first_name, u.last_name, p.policy_number
                FROM claims c
                JOIN users u    ON u.id = c.user_id
                JOIN policies p ON p.id = c.policy_id"
                . ($where ? " WHERE $where" : '')
                . " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
        return $this->raw($sql, [...$params, $limit, $offset]);
    }

    public function countAll(string $status = ''): int {
        if ($status) return $this->count('status = ?', [$status]);
        return $this->count();
    }

    public function getDocuments(int $claimId): array {
        return $this->raw(
            "SELECT * FROM claim_documents WHERE claim_id = ? ORDER BY uploaded_at DESC",
            [$claimId]
        );
    }

    public function addDocument(int $claimId, array $file): int {
        $stmt = $this->db->prepare(
            "INSERT INTO claim_documents (claim_id, file_name, file_path, file_type, file_size)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $claimId,
            $file['filename'],
            $file['path'],
            $file['mime'],
            $file['size'],
        ]);
        return (int)$this->db->lastInsertId();
    }
}
