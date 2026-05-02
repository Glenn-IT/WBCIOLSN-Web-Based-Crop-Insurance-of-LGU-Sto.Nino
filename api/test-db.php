<?php
// ============================================================
// Database Diagnostic Script
// Access: http://localhost/web-based-crop-insurance/api/diagnostic.php
// DELETE this file before deploying to production!
// ============================================================

require_once __DIR__ . '/bootstrap.php';

try {
    $pdo = Database::getInstance();

    // ---- Test 1: DB tables ----
    echo "=== DB CHECK ===\n";
    $ct = $pdo->query('SELECT COUNT(*) as cnt FROM crop_types')->fetch();
    echo "crop_types: " . $ct['cnt'] . "\n";
    $cp = $pdo->query('SELECT COUNT(*) as cnt FROM coverage_plans')->fetch();
    echo "coverage_plans: " . $cp['cnt'] . "\n";

    // ---- Test 2: Simulate farm insert ----
    echo "\n=== FARM INSERT SIMULATION ===\n";
    $farmData = [
        'farm_name'     => "Pedro Dela Cruz's Farm",
        'location'      => 'Brgy. Maligaya, Sto. Nino',
        'area_hectares' => 2.5,
        'crop_type_id'  => 1,
        'soil_type'     => 'Irrigated',
        'irrigation'    => 0,
    ];
    echo "Raw data:\n"; print_r($farmData);

    // Simulate sanitizeAll
    $sanitized = sanitizeAll($farmData);
    echo "\nAfter sanitizeAll:\n"; print_r($sanitized);

    // Simulate guardSqlInjection (manually check each value)
    $suspicious = false;
    array_walk_recursive($sanitized, function($value) use (&$suspicious) {
        if (is_string($value) && containsSqlInjection($value)) {
            $suspicious = true;
            echo "FLAGGED AS SQL INJECTION: '$value'\n";
        }
    });
    if (!$suspicious) echo "No SQL injection detected.\n";

    // Validate
    $errors = validate($sanitized, [
        'farm_name'     => 'required|max:150',
        'location'      => 'required|max:255',
        'area_hectares' => 'required|numeric',
        'crop_type_id'  => 'required|numeric',
    ]);
    if ($errors) {
        echo "VALIDATION ERRORS:\n"; print_r($errors);
    } else {
        echo "Validation passed!\n";
    }

    // ---- Test 3: Try actual DB insert with user_id=4 ----
    echo "\n=== ACTUAL INSERT TEST ===\n";
    $stmt = $pdo->prepare("INSERT INTO farms (user_id, farm_name, location, province, municipality, barangay, area_hectares, crop_type_id, soil_type, irrigation) VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([4, $sanitized['farm_name'], $sanitized['location'], '', '', '', 2.5, 1, 'Irrigated', 0]);
    $newId = $pdo->lastInsertId();
    echo "Inserted farm ID: $newId\n";
    // Clean up test row
    $pdo->exec("DELETE FROM farms WHERE id = $newId");
    echo "Cleaned up test row.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
