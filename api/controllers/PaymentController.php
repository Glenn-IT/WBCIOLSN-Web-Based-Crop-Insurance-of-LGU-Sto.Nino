<?php
// ============================================================
// Payment Controller
// POST   /api/payments/premium
// GET    /api/payments
// GET    /api/payments/{id}
// POST   /api/payments/payout   (admin)
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/PolicyModel.php';
require_once __DIR__ . '/../models/ClaimModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class PaymentController extends BaseController {
    private PaymentModel $payments;
    private PolicyModel  $policies;
    private ClaimModel   $claims;

    public function __construct() {
        $this->payments = new PaymentModel();
        $this->policies = new PolicyModel();
        $this->claims   = new ClaimModel();
    }

    // GET /api/payments
    public function index(array $params): void {
        $auth = requireAuth();
        ['page' => $page, 'perPage' => $perPage, 'offset' => $offset] = getPagination();
        $type = sanitize($_GET['type'] ?? '');

        if (in_array($auth['role'], ['admin', 'agent'])) {
            $items = $this->payments->getAllPaginated($offset, $perPage, $type);
            $total = $this->payments->countAll($type);
        } else {
            $items = $this->payments->getByUser($auth['id'], $offset, $perPage);
            $total = $this->payments->countByUser($auth['id']);
        }

        sendPaginated($items, $total, $page, $perPage);
    }

    // GET /api/payments/{id}
    public function show(array $params): void {
        $auth    = requireAuth();
        $id      = assertPositiveInt($params['id']);
        $payment = $this->payments->find($id);
        if (!$payment) sendNotFound('Payment not found.');
        requireOwnerOrAdmin($auth, (int)$payment['user_id']);
        sendSuccess($payment);
    }

    // POST /api/payments/premium
    public function premium(array $params): void {
        $auth = requireAuth();
        $data = sanitizeAll($this->body());
        guardSqlInjection($data);

        $this->validateOrFail($data, [
            'policy_id' => 'required|numeric',
            'method'    => 'required|in:cash,bank_transfer,gcash,maya,check',
        ]);

        $policy = $this->policies->find((int)$data['policy_id']);
        if (!$policy) sendNotFound('Policy not found.');
        requireOwnerOrAdmin($auth, (int)$policy['user_id']);

        $id = $this->payments->insert([
            'reference_number' => $this->payments->generateReference(),
            'user_id'          => $policy['user_id'],
            'policy_id'        => (int)$data['policy_id'],
            'type'             => 'premium',
            'amount'           => $policy['total_premium'],
            'method'           => $data['method'],
            'status'           => 'completed',
            'processed_by'     => $auth['id'],
            'notes'            => sanitize($data['notes'] ?? ''),
        ]);

        $this->audit($auth['id'], 'pay_premium', 'payments',
            "Premium paid for policy #{$data['policy_id']}");
        $payment = $this->payments->find($id);
        notifyPaymentReceived((int)$policy['user_id'], $payment['reference_number'], (float)$policy['total_premium']);
        sendSuccess($payment, 'Premium payment recorded successfully.', 201);
    }

    // POST /api/payments/payout   (admin)
    public function payout(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');

        $data = sanitizeAll($this->body());
        guardSqlInjection($data);
        $this->validateOrFail($data, [
            'claim_id' => 'required|numeric',
            'amount'   => 'required|numeric',
            'method'   => 'required|in:cash,bank_transfer,gcash,maya,check',
        ]);

        $claim = $this->claims->find((int)$data['claim_id']);
        if (!$claim) sendNotFound('Claim not found.');
        if ($claim['status'] !== 'approved') sendError('Only approved claims can receive a payout.', 400);

        $id = $this->payments->insert([
            'reference_number' => $this->payments->generateReference(),
            'user_id'          => $claim['user_id'],
            'policy_id'        => $claim['policy_id'],
            'claim_id'         => (int)$data['claim_id'],
            'type'             => 'payout',
            'amount'           => (float)$data['amount'],
            'method'           => $data['method'],
            'status'           => 'completed',
            'processed_by'     => $auth['id'],
            'notes'            => sanitize($data['notes'] ?? ''),
        ]);

        // Mark claim as paid
        $this->claims->update((int)$data['claim_id'], ['status' => 'paid']);

        $this->audit($auth['id'], 'process_payout', 'payments',
            "Payout processed for claim #{$data['claim_id']}");
        $payment = $this->payments->find($id);
        notifyPayoutProcessed((int)$claim['user_id'], $payment['reference_number'], (float)$data['amount']);
        sendSuccess($payment, 'Payout processed successfully.', 201);
    }
}
