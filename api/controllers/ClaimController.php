<?php
// ============================================================
// Claim Controller
// POST   /api/claims
// GET    /api/claims
// GET    /api/claims/{id}
// PUT    /api/claims/{id}/status
// POST   /api/claims/{id}/documents
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ClaimModel.php';
require_once __DIR__ . '/../models/PolicyModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class ClaimController extends BaseController {
    private ClaimModel  $claims;
    private PolicyModel $policies;

    public function __construct() {
        $this->claims   = new ClaimModel();
        $this->policies = new PolicyModel();
    }

    // GET /api/claims
    public function index(array $params): void {
        $auth = requireAuth();
        ['page' => $page, 'perPage' => $perPage, 'offset' => $offset] = getPagination();
        $status = sanitize($_GET['status'] ?? '');

        if (in_array($auth['role'], ['admin', 'agent'])) {
            $items = $this->claims->getAllPaginated($offset, $perPage, $status);
            $total = $this->claims->countAll($status);
        } else {
            $items = $this->claims->getByUser($auth['id'], $offset, $perPage);
            $total = $this->claims->countByUser($auth['id']);
        }

        sendPaginated($items, $total, $page, $perPage);
    }

    // GET /api/claims/{id}
    public function show(array $params): void {
        $auth  = requireAuth();
        $id    = assertPositiveInt($params['id']);
        $claim = $this->claims->getWithDetails($id);
        if (!$claim) sendNotFound('Claim not found.');
        requireOwnerOrAdmin($auth, (int)$claim['user_id']);

        $claim['documents'] = $this->claims->getDocuments($id);
        sendSuccess($claim);
    }

    // POST /api/claims
    public function store(array $params): void {
        $auth = requireAuth();
        $data = sanitizeAll($this->body());
        guardSqlInjection($data);

        $this->validateOrFail($data, [
            'policy_id'     => 'required|numeric',
            'incident_type' => 'required|max:150',
            'incident_date' => 'required|date',
            'description'   => 'required',
            'estimated_loss'=> 'required|numeric',
        ]);

        $policy = $this->policies->find((int)$data['policy_id']);
        if (!$policy) sendNotFound('Policy not found.');
        if ((int)$policy['user_id'] !== (int)$auth['id']) sendForbidden('This policy does not belong to you.');
        if ($policy['status'] !== 'active') sendError('Claims can only be filed on active policies.', 400);

        $id = $this->claims->insert([
            'claim_number'  => $this->claims->generateClaimNumber(),
            'policy_id'     => (int)$data['policy_id'],
            'user_id'       => $auth['id'],
            'incident_type' => sanitize($data['incident_type']),
            'incident_date' => $data['incident_date'],
            'description'   => sanitize($data['description']),
            'estimated_loss'=> (float)$data['estimated_loss'],
            'status'        => 'submitted',
        ]);

        $this->audit($auth['id'], 'submit_claim', 'claims', "Submitted claim #$id");
        sendSuccess($this->claims->getWithDetails($id), 'Claim submitted successfully.', 201);
    }

    // PUT /api/claims/{id}/status   (admin/agent)
    public function updateStatus(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);

        $id    = assertPositiveInt($params['id']);
        $claim = $this->claims->find($id);
        if (!$claim) sendNotFound('Claim not found.');

        $data = sanitizeAll($this->body());
        guardSqlInjection($data);
        $this->validateOrFail($data, [
            'status' => 'required|in:submitted,under_review,approved,rejected,paid',
        ]);

        $update = [
            'status'      => $data['status'],
            'reviewed_by' => $auth['id'],
            'reviewed_at' => date('Y-m-d H:i:s'),
            'remarks'     => sanitize($data['remarks'] ?? ''),
        ];

        if ($data['status'] === 'approved') {
            $this->validateOrFail($data, ['approved_amount' => 'required|numeric']);
            $update['approved_amount'] = (float)$data['approved_amount'];
        }

        $this->claims->update($id, $update);
        $this->audit($auth['id'], 'update_claim_status', 'claims',
            "Set claim #$id to {$data['status']}");
        notifyClaimStatusUpdated((int)$claim['user_id'], $claim['claim_number'], $data['status']);
        sendSuccess($this->claims->getWithDetails($id), 'Claim status updated.');
    }

    // POST /api/claims/{id}/documents
    public function uploadDocument(array $params): void {
        $auth  = requireAuth();
        $id    = assertPositiveInt($params['id']);
        $claim = $this->claims->find($id);
        if (!$claim) sendNotFound('Claim not found.');
        requireOwnerOrAdmin($auth, (int)$claim['user_id']);

        if (empty($_FILES['document'])) {
            sendError('No file uploaded. Use field name "document".', 400);
        }

        $result = uploadFile($_FILES['document'], 'claims');
        if (!$result['success']) sendError($result['error'], 422);

        $docId = $this->claims->addDocument($id, $result);
        $this->audit($auth['id'], 'upload_claim_doc', 'claims',
            "Uploaded document for claim #$id");

        sendSuccess(['document_id' => $docId, 'path' => $result['path']],
            'Document uploaded successfully.', 201);
    }
}
