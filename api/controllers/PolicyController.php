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
        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);

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

        // ── Duplicate guard: block if an active/pending policy already exists for this farm ──
        $existing = $this->policies->rawOne(
            "SELECT id, policy_number, status FROM policies
             WHERE farm_id = ? AND status IN ('pending','active')
             LIMIT 1",
            [(int)$data['farm_id']]
        );
        if ($existing) {
            sendError(
                "A {$existing['status']} policy ({$existing['policy_number']}) already exists for this farm. " .
                "Please wait for it to be processed or cancel it first.",
                409
            );
        }

        // Calculate end date and premium
        $startDate   = $data['start_date'];
        $endDate     = date('Y-m-d', strtotime("+{$plan['duration_months']} months", strtotime($startDate)));
        $planMax     = (float)$plan['max_coverage_amount'];

        // Use user-supplied desired coverage, capped at the plan maximum
        $desiredCoverage = isset($data['coverage_amount']) && (float)$data['coverage_amount'] > 0
            ? (float)$data['coverage_amount']
            : $planMax;
        $coverage    = min($desiredCoverage, $planMax);

        $totalPremium = round($farm['area_hectares'] * $plan['premium_rate'] * $coverage, 2);

        $id = $this->policies->insert([
            'policy_number'      => $this->policies->generatePolicyNumber(),
            'user_id'            => $auth['id'],
            'farm_id'            => (int)$data['farm_id'],
            'plan_id'            => (int)$data['plan_id'],
            'status'             => 'pending',
            'start_date'         => $startDate,
            'end_date'           => $endDate,
            'total_premium'      => $totalPremium,
            'coverage_amount'    => $coverage,
            'cause_of_damage'    => sanitize($data['cause_of_damage']    ?? ''),
            'percent_damage'     => isset($data['percent_damage'])    ? (float)$data['percent_damage']    : null,
            'financial_damage'   => isset($data['financial_damage'])  ? (float)$data['financial_damage']  : null,
            'damage_description' => sanitize($data['damage_description'] ?? ''),
            'date_of_loss'       => sanitize($data['date_of_loss']       ?? ''),
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

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);

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

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);
        $this->policies->update($id, [
            'status'  => 'cancelled',
            'remarks' => sanitize($data['remarks'] ?? 'Cancelled by user.'),
        ]);

        $this->audit($auth['id'], 'cancel_policy', 'policies', "Cancelled policy #$id");
        sendSuccess(null, 'Policy cancelled successfully.');
    }

    // PUT /api/policies/{id}  (admin: any fields; user: damage fields of pending only)
    public function update(array $params): void {
        $auth   = requireAuth();
        $id     = assertPositiveInt($params['id']);
        $policy = $this->policies->find($id);
        if (!$policy) sendNotFound('Policy not found.');
        requireOwnerOrAdmin($auth, (int)$policy['user_id']);

        // Regular users can only edit pending policies
        if (!in_array($auth['role'], ['admin', 'agent']) && $policy['status'] !== 'pending') {
            sendError('Only pending policies can be updated.', 400);
        }

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);

        $updateFields = [
            'cause_of_damage'    => sanitize($data['cause_of_damage']    ?? $policy['cause_of_damage']),
            'percent_damage'     => isset($data['percent_damage'])    ? (float)$data['percent_damage']    : $policy['percent_damage'],
            'financial_damage'   => isset($data['financial_damage'])  ? (float)$data['financial_damage']  : $policy['financial_damage'],
            'damage_description' => sanitize($data['damage_description'] ?? $policy['damage_description']),
            'date_of_loss'       => sanitize($data['date_of_loss']       ?? $policy['date_of_loss']),
            'coverage_amount'    => isset($data['coverage_amount'])   ? (float)$data['coverage_amount']   : $policy['coverage_amount'],
            'remarks'            => sanitize($data['remarks']            ?? $policy['remarks'] ?? ''),
        ];

        // Verification status fields (admin only)
        $allowedVer = ['Pending', 'Verified', 'Rejected'];
        if (in_array($auth['role'], ['admin', 'agent'])) {
            if (isset($data['farm_verification']) && in_array($data['farm_verification'], $allowedVer)) {
                $updateFields['farm_verification'] = $data['farm_verification'];
            }
            if (isset($data['damage_verification']) && in_array($data['damage_verification'], $allowedVer)) {
                $updateFields['damage_verification'] = $data['damage_verification'];
            }
            if (isset($data['coverage_verification']) && in_array($data['coverage_verification'], $allowedVer)) {
                $updateFields['coverage_verification'] = $data['coverage_verification'];
            }
        }

        // Admin-only: allow status change to pending
        if (in_array($auth['role'], ['admin', 'agent']) && isset($data['status'])) {
            $updateFields['status'] = sanitize($data['status']);
        }

        $this->policies->update($id, $updateFields);

        // If farm_location provided by admin, update the farm record too
        if (in_array($auth['role'], ['admin', 'agent']) && !empty($data['farm_location'])) {
            $this->farms->update((int)$policy['farm_id'], ['location' => sanitize($data['farm_location'])]);
        }

        $this->audit($auth['id'], 'update_policy', 'policies', "Updated details for policy #$id");
        sendSuccess($this->policies->getWithDetails($id), 'Policy updated successfully.');
    }

    // PUT /api/policies/{id}/review  (admin — set to under_review)
    public function review(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);
        $id   = assertPositiveInt($params['id']);

        $policy = $this->policies->find($id);
        if (!$policy) sendNotFound('Policy not found.');

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);

        $this->policies->update($id, [
            'status'   => 'under_review',
            'agent_id' => $auth['id'],
            'remarks'  => sanitize($data['remarks'] ?? ''),
        ]);

        $this->audit($auth['id'], 'review_policy', 'policies', "Marked policy #$id as under review");
        sendSuccess($this->policies->getWithDetails($id), 'Policy set to Under Review.');
    }

    // PUT /api/policies/{id}/reject  (admin/agent)
    public function reject(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);
        $id   = assertPositiveInt($params['id']);

        $policy = $this->policies->find($id);
        if (!$policy) sendNotFound('Policy not found.');

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);
        $this->validateOrFail($data, ['remarks' => 'required']);

        $this->policies->update($id, [
            'status'  => 'rejected',
            'remarks' => sanitize($data['remarks']),
        ]);

        $this->audit($auth['id'], 'reject_policy', 'policies', "Rejected policy #$id");
        notifyPolicyRejected((int)$policy['user_id'], $policy['policy_number'], $data['remarks']);
        sendSuccess(null, 'Policy rejected.');
    }

    // DELETE /api/policies/{id}
    public function destroy(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);
        $id = assertPositiveInt($params['id']);

        $policy = $this->policies->find($id);
        if (!$policy) sendNotFound('Policy not found.');

        $this->policies->delete($id);
        $this->audit($auth['id'], 'delete_policy', 'policies', "Deleted policy #$id ({$policy['policy_number']})");
        sendSuccess(null, 'Policy deleted successfully.');
    }
}
