<?php
// ============================================================
// Coverage Plan Model
// ============================================================
require_once __DIR__ . '/BaseModel.php';

class PlanModel extends BaseModel {
    protected string $table = 'coverage_plans';

    public function getActive(): array {
        return $this->all('is_active = 1', [], 'plan_name ASC');
    }

    public function getAllPaginated(int $offset, int $limit): array {
        return $this->paginate($offset, $limit, '', [], 'created_at DESC');
    }
}
