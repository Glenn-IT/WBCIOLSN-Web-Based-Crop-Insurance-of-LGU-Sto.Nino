<?php
// ============================================================
// Report Controller
// GET /api/dashboard/stats        — admin summary OR farmer summary
// GET /api/reports/claims         — claims report (admin)
// GET /api/reports/policies       — policies report (admin)
// GET /api/reports/payments       — payments report (admin)
// GET /api/reports/trends/premium — monthly premium trend
// GET /api/reports/claims/by-crop — claims grouped by crop
// GET /api/reports/claims/by-province — claims by province
// GET /api/reports/export/{type}  — CSV export
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ReportModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class ReportController extends BaseController {
    private ReportModel $reports;

    public function __construct() {
        $this->reports = new ReportModel();
    }

    // ----------------------------------------------------------
    // GET /api/dashboard/stats
    // ----------------------------------------------------------
    public function dashboardStats(array $params): void {
        $auth = requireAuth();

        if (in_array($auth['role'], ['admin', 'agent'])) {
            sendSuccess($this->reports->dashboardStats(), 'Dashboard stats retrieved.');
        } else {
            sendSuccess($this->reports->farmerStats($auth['id']), 'Farmer stats retrieved.');
        }
    }

    // ----------------------------------------------------------
    // GET /api/reports/claims
    // ----------------------------------------------------------
    public function claimsReport(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);

        $filters = [
            'status'    => sanitize($_GET['status']    ?? ''),
            'date_from' => sanitize($_GET['date_from'] ?? ''),
            'date_to'   => sanitize($_GET['date_to']   ?? ''),
        ];

        $data = $this->reports->claimsReport($filters);
        sendSuccess([
            'count'   => count($data),
            'filters' => $filters,
            'records' => $data,
        ], 'Claims report retrieved.');
    }

    // ----------------------------------------------------------
    // GET /api/reports/policies
    // ----------------------------------------------------------
    public function policiesReport(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);

        $filters = [
            'status'    => sanitize($_GET['status']    ?? ''),
            'date_from' => sanitize($_GET['date_from'] ?? ''),
            'date_to'   => sanitize($_GET['date_to']   ?? ''),
        ];

        $data = $this->reports->policiesReport($filters);
        sendSuccess([
            'count'   => count($data),
            'filters' => $filters,
            'records' => $data,
        ], 'Policies report retrieved.');
    }

    // ----------------------------------------------------------
    // GET /api/reports/payments
    // ----------------------------------------------------------
    public function paymentsReport(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);

        $filters = [
            'type'      => sanitize($_GET['type']      ?? ''),
            'date_from' => sanitize($_GET['date_from'] ?? ''),
            'date_to'   => sanitize($_GET['date_to']   ?? ''),
        ];

        $data = $this->reports->paymentsReport($filters);
        sendSuccess([
            'count'   => count($data),
            'filters' => $filters,
            'records' => $data,
        ], 'Payments report retrieved.');
    }

    // ----------------------------------------------------------
    // GET /api/reports/trends/premium
    // ----------------------------------------------------------
    public function premiumTrend(array $params): void {
        requireAuth();
        sendSuccess($this->reports->monthlyPremiumTrend(), 'Monthly premium trend retrieved.');
    }

    // ----------------------------------------------------------
    // GET /api/reports/claims/by-crop
    // ----------------------------------------------------------
    public function claimsByCrop(array $params): void {
        requireAuth();
        sendSuccess($this->reports->claimsByCropType(), 'Claims by crop type retrieved.');
    }

    // ----------------------------------------------------------
    // GET /api/reports/claims/by-province
    // ----------------------------------------------------------
    public function claimsByProvince(array $params): void {
        requireAuth();
        sendSuccess($this->reports->claimsByProvince(), 'Claims by province retrieved.');
    }

    // ----------------------------------------------------------
    // GET /api/reports/export/{type}   — CSV download
    // type: claims | policies | payments
    // ----------------------------------------------------------
    public function export(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);

        $type = strtolower($params['type'] ?? '');
        $filters = [
            'status'    => sanitize($_GET['status']    ?? ''),
            'date_from' => sanitize($_GET['date_from'] ?? ''),
            'date_to'   => sanitize($_GET['date_to']   ?? ''),
            'type'      => sanitize($_GET['type']      ?? ''),
        ];

        $data     = match ($type) {
            'claims'   => $this->reports->claimsReport($filters),
            'policies' => $this->reports->policiesReport($filters),
            'payments' => $this->reports->paymentsReport($filters),
            default    => null,
        };

        if ($data === null) sendError("Invalid export type. Use: claims, policies, payments.", 400);
        if (empty($data))   sendError("No data found for the selected filters.", 404);

        $filename = "crop_insurance_{$type}_" . date('Ymd_His') . ".csv";

        // Strip JSON headers and send CSV
        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        fputs($out, "\xEF\xBB\xBF");
        // Header row
        fputcsv($out, array_keys($data[0]));
        // Data rows
        foreach ($data as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }
}
