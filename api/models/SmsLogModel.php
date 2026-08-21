<?php
// ============================================================
// SMS Log Model
// Web-Based Crop Insurance System
// ============================================================
require_once __DIR__ . '/BaseModel.php';

class SmsLogModel extends BaseModel {
    protected string $table = 'sms_logs';

    /**
     * Get paginated SMS logs with search and status filtering
     */
    public function getAllPaginated(int $offset, int $limit, string $search = '', string $status = ''): array {
        $whereClause = [];
        $params = [];

        if ($search !== '') {
            $whereClause[] = "(recipient LIKE ? OR message LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status !== '') {
            $whereClause[] = "status = ?";
            $params[] = $status;
        }

        $where = $whereClause ? implode(' AND ', $whereClause) : '';
        return $this->raw(
            "SELECT * FROM sms_logs " .
            ($where ? "WHERE $where " : '') .
            "ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [...$params, $limit, $offset]
        );
    }

    /**
     * Count matching SMS logs
     */
    public function countAll(string $search = '', string $status = ''): int {
        $whereClause = [];
        $params = [];

        if ($search !== '') {
            $whereClause[] = "(recipient LIKE ? OR message LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status !== '') {
            $whereClause[] = "status = ?";
            $params[] = $status;
        }

        $where = $whereClause ? implode(' AND ', $whereClause) : '';
        return $this->count($where, $params);
    }

    /**
     * Get aggregate statistics of SMS attempts
     */
    public function getStats(): array {
        $total     = $this->count();
        $sent      = $this->count("status = 'sent'");
        $failed    = $this->count("status = 'failed'");
        $simulated = $this->count("status = 'simulated'");

        return [
            'total'     => $total,
            'sent'      => $sent,
            'failed'    => $failed,
            'simulated' => $simulated,
        ];
    }
}
