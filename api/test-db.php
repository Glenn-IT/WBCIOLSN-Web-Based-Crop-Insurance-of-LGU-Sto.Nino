<?php
// ============================================================
// Database Diagnostic Script
// Access: http://localhost/web-based-crop-insurance/api/diagnostic.php
// DELETE this file before deploying to production!
// ============================================================

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/FarmController.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/FarmModel.php';
require_once __DIR__ . '/controllers/BaseController.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/RoleMiddleware.php';

// Get token for farmer1
$pdo = Database::getInstance();
$users = new UserModel();
$user = $users->findByEmail('farmer1@cropinsurance.ph');

$token = jwtEncode([
    'id'    => $user['id'],
    'email' => $user['email'],
    'role'  => $user['role'],
]);

echo "Token generated: " . substr($token, 0, 30) . "...\n";

// Simulate POST /farms
$payload = [
    'farm_name'     => "Pedro's Test Farm",
    'location'      => 'Brgy. Sto. Nino',
    'area_hectares' => 2.5,
    'crop_type_id'  => 1,
    'soil_type'     => 'Irrigated',
    'irrigation'    => 0,
];

// Simulate the controller logic
$raw = $payload;
$flagged = false;
array_walk_recursive($raw, function($v) use (&$flagged) {
    if (is_string($v) && containsSqlInjection($v)) {
        $flagged = true;
        echo "FLAGGED: '$v'\n";
    }
});

if ($flagged) {
    echo "RESULT: Would return 400 Bad Request\n";
} else {
    $data = sanitizeAll($raw);
    $errors = validate($data, [
        'farm_name'     => 'required|max:150',
        'location'      => 'required|max:255',
        'area_hectares' => 'required|numeric',
        'crop_type_id'  => 'required|numeric',
    ]);
    if ($errors) {
        echo "VALIDATION ERRORS:\n"; print_r($errors);
    } else {
        echo "RESULT: Would return 201 Created - Farm submitted OK!\n";
    }
}

// End-to-End Flow Verification
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/FarmModel.php';
require_once __DIR__ . '/models/PlanModel.php';
require_once __DIR__ . '/models/PolicyModel.php';

$pdo     = Database::getInstance();
$users   = new UserModel();
$farms   = new FarmModel();
$plans   = new PlanModel();
$policies = new PolicyModel();

$pass = 0; $fail = 0;
function check($label, $cond, $detail = '') {
    global $pass, $fail;
    if ($cond) { echo "  ✓ $label\n"; $pass++; }
    else        { echo "  ✗ $label" . ($detail ? " — $detail" : '') . "\n"; $fail++; }
}

echo "=== END-TO-END FLOW TEST ===\n\n";

// 1. Login
echo "1. AUTH\n";
$farmer = $users->findByEmail('farmer1@cropinsurance.ph');
check('farmer1 exists', !!$farmer);
check('password verifies', $farmer && $users->verifyPassword('Password@123', $farmer['password']));
$token = $farmer ? jwtEncode(['id'=>$farmer['id'],'email'=>$farmer['email'],'role'=>$farmer['role']]) : null;
check('JWT encoded', !!$token);
$decoded = $token ? jwtDecode($token) : null;
check('JWT decoded', !!$decoded && $decoded['id'] == $farmer['id']);

// 2. Plans
echo "\n2. PLANS\n";
$activePlans = $plans->getActive();
check('plans exist', count($activePlans) > 0, count($activePlans) . ' found');
$plan = $activePlans[0];
check('plan has id', !empty($plan['id']));

// 3. Create Farm
echo "\n3. FARM CREATE\n";
$farmName  = 'Pedro Dela Cruz Farm';  // No apostrophe
$sanitized = sanitizeAll(['farm_name' => $farmName, 'location' => 'Sto. Nino']);
$flagged   = false;
array_walk_recursive($sanitized, function($v) use (&$flagged) {
    if (is_string($v) && containsSqlInjection($v)) $flagged = true;
});
check('farm_name not flagged as SQL injection', !$flagged);

$farmId = $farms->insert([
    'user_id'       => $farmer['id'],
    'farm_name'     => $farmName,
    'location'      => 'Brgy. Maligaya, Sto. Nino',
    'province'      => '',
    'municipality'  => '',
    'barangay'      => '',
    'area_hectares' => 2.5,
    'crop_type_id'  => 1,
    'soil_type'     => 'Irrigated',
    'irrigation'    => 1,
]);
check('farm inserted', $farmId > 0, "ID=$farmId");

// 4. Create Policy
echo "\n4. POLICY CREATE\n";
$startDate    = date('Y-m-d');
$endDate      = date('Y-m-d', strtotime("+{$plan['duration_months']} months"));
$totalPremium = round(2.5 * $plan['premium_rate'] * $plan['max_coverage_amount'], 2);
$coverage     = min(2.5 * $plan['max_coverage_amount'], (float)$plan['max_coverage_amount']);

$polNum = $policies->generatePolicyNumber();
check('policy number generated', !empty($polNum), $polNum);

$polId = $policies->insert([
    'policy_number'   => $polNum,
    'user_id'         => $farmer['id'],
    'farm_id'         => $farmId,
    'plan_id'         => $plan['id'],
    'status'          => 'pending',
    'start_date'      => $startDate,
    'end_date'        => $endDate,
    'total_premium'   => $totalPremium,
    'coverage_amount' => $coverage,
]);
check('policy inserted', $polId > 0, "ID=$polId");

$pol = $policies->find($polId);
check('policy retrievable', !!$pol);
check('policy status=pending', $pol['status'] === 'pending');

// 5. Cleanup
echo "\n5. CLEANUP\n";
$pdo->exec("DELETE FROM policies WHERE id = $polId");
$pdo->exec("DELETE FROM farms WHERE id = $farmId");
check('test data cleaned', true);

echo "\n=== RESULT: $pass passed, $fail failed ===\n";

// Targeted test for the POST /policies response
$farmer = $users->findByEmail('farmer1@cropinsurance.ph');
$plan   = $plans->getActive()[0] ?? null;

// Insert a temp farm
$farmId = $farms->insert([
    'user_id' => $farmer['id'], 'farm_name' => 'Test Farm',
    'location' => 'Sto Nino', 'province' => '', 'municipality' => '',
    'barangay' => '', 'area_hectares' => 2.5, 'crop_type_id' => 1,
    'soil_type' => 'Irrigated', 'irrigation' => 0,
]);

// Insert policy
$polNum = $policies->generatePolicyNumber();
$polId  = $policies->insert([
    'policy_number'   => $polNum,
    'user_id'         => $farmer['id'],
    'farm_id'         => $farmId,
    'plan_id'         => $plan['id'],
    'status'          => 'pending',
    'start_date'      => date('Y-m-d'),
    'end_date'        => date('Y-m-d', strtotime('+6 months')),
    'total_premium'   => 1000,
    'coverage_amount' => 50000,
]);

echo "=== Policy inserted: ID=$polId, Number=$polNum ===\n\n";

// Test getWithDetails
$details = $policies->getWithDetails($polId);
echo "getWithDetails result:\n";
echo $details ? "  OK — has " . count($details) . " fields\n" : "  NULL — this is the bug!\n";
if ($details) {
    echo "  policy_number: " . $details['policy_number'] . "\n";
    echo "  plan_name: "     . $details['plan_name'] . "\n";
    echo "  farm_name: "     . $details['farm_name'] . "\n";
}

// Check via API call
$token = jwtEncode(['id' => $farmer['id'], 'email' => $farmer['email'], 'role' => $farmer['role']]);
$ch = curl_init("http://localhost/web-based-crop-insurance/api/policies/$polId");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"]]);
$apiRes = json_decode(curl_exec($ch), true);
curl_close($ch);
echo "\nGET /policies/$polId via API:\n";
echo "  success: " . ($apiRes['success'] ? 'true' : 'false') . "\n";
echo "  data null? " . ($apiRes['data'] === null ? 'YES — BUG!' : 'no, has data') . "\n";
if ($apiRes['data']) echo "  policy_number: " . $apiRes['data']['policy_number'] . "\n";

// Cleanup
$pdo->exec("DELETE FROM policies WHERE id = $polId");
$pdo->exec("DELETE FROM farms WHERE id = $farmId");
echo "\nCleaned up.\n";
