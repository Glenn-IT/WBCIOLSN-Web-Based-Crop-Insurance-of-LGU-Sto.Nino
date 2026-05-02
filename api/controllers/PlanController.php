<?php
// ============================================================
// Plan Controller  (Coverage Plans)
// GET    /api/plans           (public)
// GET    /api/plans/{id}      (public)
// POST   /api/plans           (admin)
// PUT    /api/plans/{id}      (admin)
// DELETE /api/plans/{id}      (admin)
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PlanModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class PlanController extends BaseController {
    private PlanModel $plans;

    public function __construct() {
        $this->plans = new PlanModel();
    }

    // GET /api/plans
    public function index(array $params): void {
        // Public: return only active plans; admin sees all
        $token   = getBearerToken();
        $payload = $token ? jwtDecode($token) : null;
        $isAdmin = $payload && $payload['role'] === 'admin';

        ['page' => $page, 'perPage' => $perPage, 'offset' => $offset] = getPagination();

        if ($isAdmin) {
            $items = $this->plans->getAllPaginated($offset, $perPage);
            $total = $this->plans->count();
        } else {
            $items = $this->plans->getActive();
            $total = count($items);
        }

        sendPaginated($items, $total, $page, $perPage);
    }

    // GET /api/plans/{id}
    public function show(array $params): void {
        $id   = assertPositiveInt($params['id']);
        $plan = $this->plans->find($id);
        if (!$plan) sendNotFound('Coverage plan not found.');
        sendSuccess($plan);
    }

    // POST /api/plans
    public function store(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);
        $this->validateOrFail($data, [
            'plan_name'           => 'required|max:150',
            'coverage_type'       => 'required|in:natural_disaster,pest_disease,drought,flood,comprehensive',
            'coverage_percent'    => 'required|numeric',
            'premium_rate'        => 'required|numeric',
            'max_coverage_amount' => 'required|numeric',
            'duration_months'     => 'required|numeric',
        ]);

        $id = $this->plans->insert([
            'plan_name'           => sanitize($data['plan_name']),
            'description'         => sanitize($data['description'] ?? ''),
            'coverage_type'       => $data['coverage_type'],
            'coverage_percent'    => (float)$data['coverage_percent'],
            'premium_rate'        => (float)$data['premium_rate'],
            'max_coverage_amount' => (float)$data['max_coverage_amount'],
            'duration_months'     => (int)$data['duration_months'],
            'is_active'           => 1,
        ]);

        $this->audit($auth['id'], 'create_plan', 'plans', "Created plan #$id");
        sendSuccess($this->plans->find($id), 'Coverage plan created.', 201);
    }

    // PUT /api/plans/{id}
    public function update(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');
        $id   = assertPositiveInt($params['id']);

        $plan = $this->plans->find($id);
        if (!$plan) sendNotFound('Coverage plan not found.');

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);
        $this->validateOrFail($data, [
            'plan_name'           => 'required|max:150',
            'coverage_type'       => 'required|in:natural_disaster,pest_disease,drought,flood,comprehensive',
            'coverage_percent'    => 'required|numeric',
            'premium_rate'        => 'required|numeric',
            'max_coverage_amount' => 'required|numeric',
            'duration_months'     => 'required|numeric',
        ]);

        $this->plans->update($id, [
            'plan_name'           => sanitize($data['plan_name']),
            'description'         => sanitize($data['description'] ?? $plan['description']),
            'coverage_type'       => $data['coverage_type'],
            'coverage_percent'    => (float)$data['coverage_percent'],
            'premium_rate'        => (float)$data['premium_rate'],
            'max_coverage_amount' => (float)$data['max_coverage_amount'],
            'duration_months'     => (int)$data['duration_months'],
            'is_active'           => isset($data['is_active']) ? (int)$data['is_active'] : $plan['is_active'],
        ]);

        $this->audit($auth['id'], 'update_plan', 'plans', "Updated plan #$id");
        sendSuccess($this->plans->find($id), 'Coverage plan updated.');
    }

    // DELETE /api/plans/{id}
    public function destroy(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');
        $id   = assertPositiveInt($params['id']);

        $plan = $this->plans->find($id);
        if (!$plan) sendNotFound('Coverage plan not found.');

        $this->plans->update($id, ['is_active' => 0]);
        $this->audit($auth['id'], 'delete_plan', 'plans', "Deactivated plan #$id");
        sendSuccess(null, 'Coverage plan deactivated.');
    }
}
