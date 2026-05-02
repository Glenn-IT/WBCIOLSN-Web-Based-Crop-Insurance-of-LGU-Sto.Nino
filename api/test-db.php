<?php
// ============================================================
// Database Connection Test
// Access: http://localhost/web-based-crop-insurance/api/test-db.php
// DELETE this file before deploying to production!
// ============================================================

require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

try {
    $pdo = Database::getInstance();

    // Run a quick check on all core tables
    $tables = [
        'users', 'crop_types', 'farms', 'coverage_plans',
        'policies', 'claims', 'claim_documents',
        'payments', 'notifications', 'audit_logs'
    ];

    $results = [];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) AS count FROM `$table`");
        $row  = $stmt->fetch();
        $results[$table] = (int) $row['count'];
    }

    echo json_encode([
        'success'  => true,
        'message'  => 'Database connection successful!',
        'database' => DB_NAME,
        'tables'   => $results
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
