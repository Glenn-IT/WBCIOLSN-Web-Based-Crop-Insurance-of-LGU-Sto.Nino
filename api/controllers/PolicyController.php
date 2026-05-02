<?php
// ============================================================
// Policy Controller
// POST   /api/policies
// GET    /api/policies
// GET    /api/policies/{id}
// PUT    /api/policies/{id}/approve
// PUT    /api/policies/{id}/cancel
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PolicyModel.php';
require_once __DIR__ . '/../models/FarmModel.php';
require_once __DIR__ . '/../models/PlanModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class PolicyController extends BaseController {
    private PolicyModel $policies;
    private FarmModel   $farms;
    private PlanModel   $plans;

    public function __construct() {
        $this->policies = new PolicyModel();
        $this->farms    = new FarmModel();
        $this->plans    = new PlanModel();
    }

    // GET /api/policies
    public function index(array $params): void {
        $auth = requireAuth();
        ['page' => $page, 'perPage' => $perPage, 'offset' => $offset] = getPagination();
        $status = sanitize($_GET['status'] ?? '');

        if (in_array($auth['role'], ['admin', 'agent'])) {
            $items = $this->policies->getAllPaginated($offset, $perPage, $status);
            $total = $this->policies->countAll($status);
        } else {
            $items = $this->policies->getByUser($auth['id'], $offset, $perPage);
            $total = $this->policies->countByUser($auth['id']);
        }

        sendPaginated($items, $total, $page, $perPage);
    }

    // GET /api/policies/{id}
    public function show(array $params): void {
        $auth   = requireAuth();
        $id     = assertPositiveInt($params['id']);
        $policy = $this->policies->getWithDetails($id);
        if (!$policy) sendNotFound('Policy not found.');
        requireOwnerOrAdmin($auth, (int)$policy['user_id']);
        sendSuccess($policy);
    }

    // POST /api/policies
    public function store(array $params): void {
        $auth = requireAuth();
        $data = sanitizeAll($this->body());
        guardSqlInjection($data);

        $this->validateOrFail($data, [
            'farm_id'    => 'required|numeric',
            'plan_id'    => 'required|numeric',
            'start_date' => 'required|date',
        ]);

        $farm = $this->farms->find((int)$data['farm_id']);
        if (!$farm) sendNotFound('Farm not found.');
        if ((int)$farm['user_id'] !== (int)$auth['id']) sendForbidden('This farm does not belong to you.');

        $plan = $this->plans->find((int)$data['plan_id']);
        if (!$plan || !$plan['is_active']) sendNotFound('Coverage plan not found or inactive.');

        // Calculate end date and premium
        $startDate   = $data['start_date'];
        $endDate     = date('Y-m-d', strtotime("+{$plan['duration_months']} months", strtotime($startDate)));
        $totalPremium = round($farm['area_hectares'] * $plan['premium_rate'] * $plan['max_coverage_amount'], 2);
        $coverage    = min($farm['area_hectares'] * $plan['max_coverage_amount'], (float)$plan['max_coverage_amount']);

        $id = $this->policies->insert([
            'policy_number'  => $this->policies->generatePolicyNumber(),
            'user_id'        => $auth['id'],
            'farm_id'        => (int)$data['farm_id'],
            'plan_id'        => (int)$data['plan_id'],
            'status'         => 'pending',
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'total_premium'  => $totalPremium,
            'coverage_amount'=> $coverage,
        ]);

        $this->audit($auth['id'], 'create_policy', 'policies', "Submitted policy #$id");
        sendSuccess($this->policies->getWithDetails($id), 'Policy application submitted.', 201);
    }

    // PUT /api/policies/{id}/approve
    public function approve(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);
        $id   = assertPositiveInt($params['id']);

        $policy = $this->policies->find($id);
        if (!$policy) sendNotFound('Policy not found.');
        if ($policy['status'] !== 'pending') sendError('Only pending policies can be approved.', 400);

        $data = sanitizeAll($this->body());
        guardSqlInjection($data);

        $this->policies->update($id, [
            'status'      => 'active',
            'agent_id'    => $auth['id'],
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $auth['id'],
            'remarks'     => sanitize($data['remarks'] ?? ''),
        ]);

        $this->audit($auth['id'], 'approve_policy', 'policies', "Approved policy #$id");
        notifyPolicyApproved((int)$policy['user_id'], $policy['policy_number']);
        sendSuccess($this->policies->getWithDetails($id), 'Policy approved successfully.');
    }

    // PUT /api/policies/{id}/cancel
    public function cancel(array $params): void {
        $auth   = requireAuth();
        $id     = assertPositiveInt($params['id']);
        $policy = $this->policies->find($id);
        if (!$policy) sendNotFound('Policy not found.');
        requireOwnerOrAdmin($auth, (int)$policy['user_id']);

        if (in_array($policy['status'], ['cancelled', 'expired'])) {
            sendError('Policy is already cancelled or expired.', 400);
        }

        $data = sanitizeAll($this->body());
        guardSqlInjection($data);
        $this->policies->update($id, [
            'status'  => 'cancelled',
            'remarks' => sanitize($data['remarks'] ?? 'Cancelled by user.'),
        ]);

        $this->audit($auth['id'], 'cancel_policy', 'policies', "Cancelled policy #$id");
        sendSuccess(null, 'Policy cancelled successfully.');
    }

    // PUT /api/policies/{id}/reject  (admin/agent)
    public function reject(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);
        $id   = assertPositiveInt($params['id']);

        $policy = $this->policies->find($id);
        if (!$policy) sendNotFound('Policy not found.');
        if ($policy['status'] !== 'pending') sendError('Only pending policies can be rejected.', 400);

        $data = sanitizeAll($this->body());
        guardSqlInjection($data);
        $this->validateOrFail($data, ['remarks' => 'required']);

        $this->policies->update($id, [
            'status'  => 'rejected',
            'remarks' => sanitize($data['remarks']),
        ]);

        $this->audit($auth['id'], 'reject_policy', 'policies', "Rejected policy #$id");
        notifyPolicyRejected((int)$policy['user_id'], $policy['policy_number'], $data['remarks']);
        sendSuccess(null, 'Policy rejected.');
    }
}
