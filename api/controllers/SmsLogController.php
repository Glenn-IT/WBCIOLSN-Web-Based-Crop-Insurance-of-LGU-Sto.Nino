<?php
// ============================================================
// SMS Log Controller
// GET    /api/sms-logs               (admin)
// GET    /api/sms-logs/{id}          (admin)
// POST   /api/sms-logs/{id}/resend   (admin)
// DELETE /api/sms-logs/clear         (admin)
// Web-Based Crop Insurance System
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SmsLogModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../helpers/sms.php';

class SmsLogController extends BaseController {
    private SmsLogModel $smsLogs;

    public function __construct() {
        $this->smsLogs = new SmsLogModel();
    }

    // GET /api/sms-logs
    public function index(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);

        ['page' => $page, 'perPage' => $perPage, 'offset' => $offset] = getPagination();
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');

        $items = $this->smsLogs->getAllPaginated($offset, $perPage, $search, $status);
        $total = $this->smsLogs->countAll($search, $status);
        $stats = $this->smsLogs->getStats();

        sendPaginated($items, $total, $page, $perPage, 'SMS logs retrieved successfully.', ['stats' => $stats]);
    }

    // GET /api/sms-logs/{id}
    public function show(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);
        $id   = assertPositiveInt($params['id']);

        $log = $this->smsLogs->find($id);
        if (!$log) sendNotFound('SMS log entry not found.');

        sendSuccess($log);
    }

    // POST /api/sms-logs/{id}/resend
    public function resend(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');
        $id   = assertPositiveInt($params['id']);

        $log = $this->smsLogs->find($id);
        if (!$log) sendNotFound('SMS log entry not found.');

        $result = sendPhilSMS($log['recipient'], $log['message']);
        $this->audit($auth['id'], 'resend_sms', 'sms_logs', "Resent SMS log #$id to {$log['recipient']}");

        sendSuccess($result, 'SMS resend attempt completed.');
    }

    // DELETE /api/sms-logs/clear
    public function clear(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');

        $this->smsLogs->rawExec("DELETE FROM sms_logs");
        $this->audit($auth['id'], 'clear_sms_logs', 'sms_logs', 'Cleared all SMS log entries');
        sendSuccess(null, 'SMS logs cleared successfully.');
    }
}
