<?php
// ============================================================
// Seed Walkthrough Data Script
// Based on docs/CAPSTONE-PRESENTATION-WALKTHROUGH.md
// ============================================================

require_once __DIR__ . '/../api/config/database.php';

try {
    $db = Database::getInstance();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully.\n";

    // Disable foreign key checks for clean seeding
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Clean tables to prevent duplicates
    $tablesToTruncate = [
        'claim_documents',
        'claims',
        'policy_documents',
        'payments',
        'notifications',
        'policies',
        'farms',
        'sms_logs',
    ];

    foreach ($tablesToTruncate as $tbl) {
        $db->exec("TRUNCATE TABLE `$tbl`;");
        echo "Truncated table: $tbl\n";
    }

    // Standard bcrypt hash for "Password@123"
    $passwordHash = '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO';
    // Security question answer hash for "admin" / "secret"
    $securityAnswerHash = '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS';

    // 1. Seed or Upsert Users
    $users = [
        [
            'id' => 1,
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'admin@cropinsurance.ph',
            'password' => $passwordHash,
            'must_change_password' => 0,
            'phone' => '09000000001',
            'address' => 'MAO Sto. Niño HQ, Sto. Niño, Cagayan',
            'farmer_type' => null,
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => 1,
            'security_question' => 'What was your childhood nickname?',
            'security_answer_hash' => $securityAnswerHash,
        ],
        [
            'id' => 2,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'agent1@cropinsurance.ph',
            'password' => $passwordHash,
            'must_change_password' => 0,
            'phone' => '09111111111',
            'address' => 'Field Inspection Unit, MAO Sto. Niño',
            'farmer_type' => null,
            'role' => 'agent',
            'status' => 'active',
            'email_verified' => 1,
            'security_question' => 'What was your childhood nickname?',
            'security_answer_hash' => $securityAnswerHash,
        ],
        [
            'id' => 3,
            'first_name' => 'Jose',
            'last_name' => 'Reyes',
            'email' => 'agent2@cropinsurance.ph',
            'password' => $passwordHash,
            'must_change_password' => 0,
            'phone' => '09222222222',
            'address' => 'Claims Verifier Unit, MAO Sto. Niño',
            'farmer_type' => null,
            'role' => 'agent',
            'status' => 'active',
            'email_verified' => 1,
            'security_question' => 'What was your childhood nickname?',
            'security_answer_hash' => $securityAnswerHash,
        ],
        [
            'id' => 4,
            'first_name' => 'Pedro',
            'last_name' => 'Dela Cruz',
            'email' => 'farmer1@cropinsurance.ph',
            'password' => $passwordHash,
            'must_change_password' => 0,
            'phone' => '09333333333',
            'address' => 'Brgy. Maligaya, Sto. Niño, Cagayan',
            'farmer_type' => 'Small Farmer',
            'role' => 'farmer',
            'status' => 'active',
            'email_verified' => 1,
            'security_question' => 'What was your childhood nickname?',
            'security_answer_hash' => $securityAnswerHash,
        ],
        [
            'id' => 5,
            'first_name' => 'Luisa',
            'last_name' => 'Garcia',
            'email' => 'farmer2@cropinsurance.ph',
            'password' => $passwordHash,
            'must_change_password' => 0,
            'phone' => '09444444444',
            'address' => 'Brgy. San Isidro, Sto. Niño, Cagayan',
            'farmer_type' => 'Commercial Farmer',
            'role' => 'farmer',
            'status' => 'active',
            'email_verified' => 1,
            'security_question' => 'What was your childhood nickname?',
            'security_answer_hash' => $securityAnswerHash,
        ],
        [
            'id' => 6,
            'first_name' => 'Ramon',
            'last_name' => 'Flores',
            'email' => 'farmer3@cropinsurance.ph',
            'password' => $passwordHash,
            'must_change_password' => 0,
            'phone' => '09555555555',
            'address' => 'Brgy. Rizal, Sto. Niño, Cagayan',
            'farmer_type' => 'Small Farmer',
            'role' => 'farmer',
            'status' => 'active',
            'email_verified' => 1,
            'security_question' => 'What was your childhood nickname?',
            'security_answer_hash' => $securityAnswerHash,
        ],
        [
            'id' => 7,
            'first_name' => 'Glenard',
            'last_name' => 'Pagurayan',
            'email' => 'glenard2308@gmail.com',
            'password' => $passwordHash,
            'must_change_password' => 0,
            'phone' => '09557997409',
            'address' => 'Sto. Niño, Cagayan',
            'farmer_type' => null,
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => 1,
            'security_question' => 'What was your childhood nickname?',
            'security_answer_hash' => $securityAnswerHash,
        ],
    ];

    $userStmt = $db->prepare("
        INSERT INTO users (
            id, first_name, last_name, email, password, must_change_password,
            phone, address, farmer_type, role, status, email_verified,
            security_question, security_answer_hash, failed_attempts, locked_until
        ) VALUES (
            :id, :first_name, :last_name, :email, :password, :must_change_password,
            :phone, :address, :farmer_type, :role, :status, :email_verified,
            :security_question, :security_answer_hash, 0, NULL
        )
        ON DUPLICATE KEY UPDATE
            first_name = VALUES(first_name),
            last_name = VALUES(last_name),
            email = VALUES(email),
            password = VALUES(password),
            must_change_password = VALUES(must_change_password),
            phone = VALUES(phone),
            address = VALUES(address),
            farmer_type = VALUES(farmer_type),
            role = VALUES(role),
            status = VALUES(status),
            email_verified = VALUES(email_verified),
            security_question = VALUES(security_question),
            security_answer_hash = VALUES(security_answer_hash),
            failed_attempts = 0,
            locked_until = NULL
    ");

    foreach ($users as $u) {
        $userStmt->execute($u);
        echo "Seeded User #{$u['id']}: {$u['first_name']} {$u['last_name']} ({$u['email']}) [{$u['role']}]\n";
    }

    // 2. Seed Farms
    $farms = [
        [
            'id' => 1,
            'user_id' => 4,
            'farm_name' => 'Dela Cruz Farm',
            'application_type' => 'New',
            'farmer_category' => 'Small Farmer',
            'location' => 'Brgy. Maligaya, Sto. Niño, Cagayan',
            'province' => 'Cagayan',
            'municipality' => 'Sto. Niño',
            'barangay' => 'Maligaya',
            'area_hectares' => 2.5000,
            'crop_type_id' => 1, // Rice
            'soil_type' => 'Clay Loam',
            'irrigation' => 1,
            'tenurial_status' => 'Owner',
            'planting_method' => 'Direct Seeding',
            'planting_date' => '2026-01-10',
            'harvest_date' => '2026-05-15',
            'latitude' => 17.89230000,
            'longitude' => 121.57140000,
        ],
        [
            'id' => 2,
            'user_id' => 4,
            'farm_name' => 'North Field',
            'application_type' => 'Renewal',
            'farmer_category' => 'Small Farmer',
            'location' => 'Brgy. Pinili, Sto. Niño, Cagayan',
            'province' => 'Cagayan',
            'municipality' => 'Sto. Niño',
            'barangay' => 'Pinili',
            'area_hectares' => 1.2500,
            'crop_type_id' => 1, // Rice
            'soil_type' => 'Sandy Loam',
            'irrigation' => 0,
            'tenurial_status' => 'Owner',
            'planting_method' => 'Transplanting',
            'planting_date' => '2026-01-20',
            'harvest_date' => '2026-05-25',
            'latitude' => 17.89800000,
            'longitude' => 121.57600000,
        ],
        [
            'id' => 3,
            'user_id' => 5,
            'farm_name' => 'Garcia Cornfield',
            'application_type' => 'New',
            'farmer_category' => 'Commercial Farmer',
            'location' => 'Brgy. San Isidro, Sto. Niño, Cagayan',
            'province' => 'Cagayan',
            'municipality' => 'Sto. Niño',
            'barangay' => 'San Isidro',
            'area_hectares' => 3.7500,
            'crop_type_id' => 2, // Corn
            'soil_type' => 'Clay',
            'irrigation' => 1,
            'tenurial_status' => 'Owner',
            'planting_method' => 'Direct Seeding',
            'planting_date' => '2026-02-05',
            'harvest_date' => '2026-06-30',
            'latitude' => 17.88450000,
            'longitude' => 121.56500000,
        ],
        [
            'id' => 4,
            'user_id' => 6,
            'farm_name' => 'Flores Sugarcane Farm',
            'application_type' => 'New',
            'farmer_category' => 'Small Farmer',
            'location' => 'Brgy. Rizal, Sto. Niño, Cagayan',
            'province' => 'Cagayan',
            'municipality' => 'Sto. Niño',
            'barangay' => 'Rizal',
            'area_hectares' => 5.0000,
            'crop_type_id' => 3, // Sugarcane
            'soil_type' => 'Loam',
            'irrigation' => 1,
            'tenurial_status' => 'Tenant',
            'planting_method' => 'Ratoon',
            'planting_date' => '2026-03-01',
            'harvest_date' => '2026-11-30',
            'latitude' => 17.90500000,
            'longitude' => 121.58200000,
        ],
    ];

    $farmStmt = $db->prepare("
        INSERT INTO farms (
            id, user_id, farm_name, application_type, farmer_category,
            location, province, municipality, barangay, area_hectares,
            crop_type_id, soil_type, irrigation, tenurial_status,
            planting_method, planting_date, harvest_date, latitude, longitude
        ) VALUES (
            :id, :user_id, :farm_name, :application_type, :farmer_category,
            :location, :province, :municipality, :barangay, :area_hectares,
            :crop_type_id, :soil_type, :irrigation, :tenurial_status,
            :planting_method, :planting_date, :harvest_date, :latitude, :longitude
        )
    ");

    foreach ($farms as $f) {
        $farmStmt->execute($f);
        echo "Seeded Farm #{$f['id']}: {$f['farm_name']} ({$f['location']})\n";
    }

    // 3. Seed Policies
    $policies = [
        [
            'id' => 1,
            'policy_number' => 'POL-2026-000001',
            'user_id' => 4, // Pedro Dela Cruz
            'farm_id' => 1,
            'plan_id' => 2, // Natural Disaster Plan
            'agent_id' => 2, // Maria Santos
            'status' => 'active',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'total_premium' => 3250.00,
            'coverage_amount' => 50000.00,
            'remarks' => 'Verified and approved by MAO Sto. Niño.',
            'cause_of_damage' => 'Typhoon',
            'percent_damage' => 70.00,
            'financial_damage' => 35000.00,
            'damage_description' => 'Severe storm winds and flooding flattened standing palay crop across the parcel.',
            'date_of_loss' => '2026-03-15',
            'farm_verification' => 'Verified',
            'damage_verification' => 'Verified',
            'coverage_verification' => 'Verified',
            'approved_at' => '2026-01-03 10:00:00',
            'approved_by' => 1,
        ],
        [
            'id' => 2,
            'policy_number' => 'POL-2026-000002',
            'user_id' => 4, // Pedro Dela Cruz
            'farm_id' => 2,
            'plan_id' => 1, // Basic Flood Cover
            'agent_id' => 2, // Maria Santos
            'status' => 'active',
            'start_date' => '2026-01-15',
            'end_date' => '2026-12-31',
            'total_premium' => 1562.50,
            'coverage_amount' => 25000.00,
            'remarks' => 'Approved for seasonal flood protection near river basin.',
            'cause_of_damage' => 'Flood',
            'percent_damage' => 40.00,
            'financial_damage' => 10000.00,
            'damage_description' => 'Cagayan river backflow caused seasonal submergence.',
            'date_of_loss' => '2026-02-10',
            'farm_verification' => 'Verified',
            'damage_verification' => 'Verified',
            'coverage_verification' => 'Verified',
            'approved_at' => '2026-01-16 14:00:00',
            'approved_by' => 1,
        ],
        [
            'id' => 3,
            'policy_number' => 'POL-2026-000003',
            'user_id' => 5, // Luisa Garcia
            'farm_id' => 3,
            'plan_id' => 5, // Comprehensive Plan
            'agent_id' => 3, // Jose Reyes
            'status' => 'active',
            'start_date' => '2026-02-01',
            'end_date' => '2027-01-31',
            'total_premium' => 9562.50,
            'coverage_amount' => 150000.00,
            'remarks' => 'Comprehensive coverage approved for commercial corn farm.',
            'cause_of_damage' => 'Pest/Disease',
            'percent_damage' => 60.00,
            'financial_damage' => 45000.00,
            'damage_description' => 'Fall armyworm and corn borer outbreak documented across field.',
            'date_of_loss' => '2026-04-10',
            'farm_verification' => 'Verified',
            'damage_verification' => 'Verified',
            'coverage_verification' => 'Verified',
            'approved_at' => '2026-02-03 09:30:00',
            'approved_by' => 1,
        ],
        [
            'id' => 4,
            'policy_number' => 'POL-2026-000004',
            'user_id' => 6, // Ramon Flores
            'farm_id' => 4,
            'plan_id' => 3, // Pest & Disease Shield
            'agent_id' => 3, // Jose Reyes
            'status' => 'pending', // Ready for Step 12 live triage!
            'start_date' => '2026-05-01',
            'end_date' => '2026-11-30',
            'total_premium' => 8437.50,
            'coverage_amount' => 100000.00,
            'remarks' => 'Newly submitted application awaiting MAO field inspection & 3-tier validation.',
            'cause_of_damage' => 'Flood',
            'percent_damage' => 50.00,
            'financial_damage' => 42187.50,
            'damage_description' => 'Heavy river overflow inundated sugarcane rows for 48 hours.',
            'date_of_loss' => '2026-05-01',
            'farm_verification' => 'Pending',
            'damage_verification' => 'Pending',
            'coverage_verification' => 'Pending',
            'approved_at' => null,
            'approved_by' => null,
        ],
    ];

    $policyStmt = $db->prepare("
        INSERT INTO policies (
            id, policy_number, user_id, farm_id, plan_id, agent_id,
            status, start_date, end_date, total_premium, coverage_amount,
            remarks, cause_of_damage, percent_damage, financial_damage,
            damage_description, date_of_loss, farm_verification,
            damage_verification, coverage_verification, approved_at, approved_by
        ) VALUES (
            :id, :policy_number, :user_id, :farm_id, :plan_id, :agent_id,
            :status, :start_date, :end_date, :total_premium, :coverage_amount,
            :remarks, :cause_of_damage, :percent_damage, :financial_damage,
            :damage_description, :date_of_loss, :farm_verification,
            :damage_verification, :coverage_verification, :approved_at, :approved_by
        )
    ");

    foreach ($policies as $p) {
        $policyStmt->execute($p);
        echo "Seeded Policy #{$p['id']}: {$p['policy_number']} [{$p['status']}]\n";
    }

    // 4. Seed Policy Documents (Damage evidence photos & valid ID)
    // Linking actual files present in uploads/policies
    $policyDocs = [
        // For Policy 4 (Ramon Flores - 5 damage photos for Step 7 / Step 12 demonstration + 1 Valid ID)
        ['policy_id' => 4, 'document_type' => 'damage_photo', 'file_name' => 'flood_damage_plot_1.png', 'file_path' => 'policies/file_6a26eed3cdb066.66263919.png', 'file_type' => 'image/png', 'file_size' => 413709],
        ['policy_id' => 4, 'document_type' => 'damage_photo', 'file_name' => 'flood_damage_plot_2.png', 'file_path' => 'policies/file_6a26eed528c066.30623825.png', 'file_type' => 'image/png', 'file_size' => 413709],
        ['policy_id' => 4, 'document_type' => 'damage_photo', 'file_name' => 'flood_damage_plot_3.png', 'file_path' => 'policies/file_6a26eed5d69fb0.48116279.png', 'file_type' => 'image/png', 'file_size' => 413709],
        ['policy_id' => 4, 'document_type' => 'damage_photo', 'file_name' => 'flood_damage_plot_4.png', 'file_path' => 'policies/file_6a26eed68a2413.29363042.png', 'file_type' => 'image/png', 'file_size' => 413709],
        ['policy_id' => 4, 'document_type' => 'damage_photo', 'file_name' => 'flood_damage_plot_5.png', 'file_path' => 'policies/file_6a26eed7d6ac86.91061740.png', 'file_type' => 'image/png', 'file_size' => 413709],
        ['policy_id' => 4, 'document_type' => 'valid_id',     'file_name' => 'government_id.jpg',        'file_path' => 'policies/file_6a26eebb3b01b2.81014618.jpg', 'file_type' => 'image/jpeg', 'file_size' => 54977],

        // For Policy 1 (Pedro Dela Cruz)
        ['policy_id' => 1, 'document_type' => 'damage_photo', 'file_name' => 'typhoon_damage_proof.png', 'file_path' => 'policies/file_6a26eed3cdb066.66263919.png', 'file_type' => 'image/png', 'file_size' => 413709],
        ['policy_id' => 1, 'document_type' => 'valid_id',     'file_name' => 'farmer_id.jpg',             'file_path' => 'policies/file_6a26eebb3b01b2.81014618.jpg', 'file_type' => 'image/jpeg', 'file_size' => 54977],

        // For Policy 3 (Luisa Garcia)
        ['policy_id' => 3, 'document_type' => 'damage_photo', 'file_name' => 'corn_damage_proof.png',    'file_path' => 'policies/file_6a26eed528c066.30623825.png', 'file_type' => 'image/png', 'file_size' => 413709],
        ['policy_id' => 3, 'document_type' => 'valid_id',     'file_name' => 'valid_id.jpg',             'file_path' => 'policies/file_6a26eebb3b01b2.81014618.jpg', 'file_type' => 'image/jpeg', 'file_size' => 54977],
    ];

    $docStmt = $db->prepare("
        INSERT INTO policy_documents (policy_id, document_type, file_name, file_path, file_type, file_size)
        VALUES (:policy_id, :document_type, :file_name, :file_path, :file_type, :file_size)
    ");

    foreach ($policyDocs as $doc) {
        $docStmt->execute($doc);
    }
    echo "Seeded " . count($policyDocs) . " policy documents.\n";

    // 5. Seed Claims
    $claims = [
        [
            'id' => 1,
            'claim_number' => 'CLM-2026-000001',
            'policy_id' => 1,
            'user_id' => 4, // Pedro Dela Cruz
            'incident_type' => 'Typhoon Damage',
            'incident_date' => '2026-03-15',
            'description' => 'Typhoon caused severe flooding and flattened standing rice crops across the entire 2.5-hectare farm parcel.',
            'estimated_loss' => 35000.00,
            'approved_amount' => 28000.00,
            'status' => 'approved',
            'reviewed_by' => 2, // Maria Santos
            'reviewed_at' => '2026-03-20 10:00:00',
            'remarks' => 'Verified 70% lodging damage. Indemnity cleared for ₱28,000.00 pursuant to PCIC / LGU schedule.',
        ],
        [
            'id' => 2,
            'claim_number' => 'CLM-2026-000002',
            'policy_id' => 3,
            'user_id' => 5, // Luisa Garcia
            'incident_type' => 'Pest Infestation',
            'incident_date' => '2026-04-10',
            'description' => 'Brown planthopper and corn borer infestation affecting approximately 60% of corn field.',
            'estimated_loss' => 45000.00,
            'approved_amount' => null,
            'status' => 'under_review', // Ready for Step 13 live demonstration!
            'reviewed_by' => null,
            'reviewed_at' => null,
            'remarks' => 'Field inspection assigned to Agent Jose Reyes for visual damage validation.',
        ],
    ];

    $claimStmt = $db->prepare("
        INSERT INTO claims (
            id, claim_number, policy_id, user_id, incident_type,
            incident_date, description, estimated_loss, approved_amount,
            status, reviewed_by, reviewed_at, remarks
        ) VALUES (
            :id, :claim_number, :policy_id, :user_id, :incident_type,
            :incident_date, :description, :estimated_loss, :approved_amount,
            :status, :reviewed_by, :reviewed_at, :remarks
        )
    ");

    foreach ($claims as $c) {
        $claimStmt->execute($c);
        echo "Seeded Claim #{$c['id']}: {$c['claim_number']} [{$c['status']}]\n";
    }

    // 6. Seed Claim Documents
    $claimDocs = [
        ['claim_id' => 1, 'file_name' => 'typhoon_damage_rice.jpg', 'file_path' => 'claims/file_6a26f27d2156e9.20885323.jpg', 'file_type' => 'image/jpeg', 'file_size' => 54977],
        ['claim_id' => 2, 'file_name' => 'corn_pest_damage.jpg',    'file_path' => 'claims/file_6a84e2327edfc1.81765159.jpg', 'file_type' => 'image/jpeg', 'file_size' => 54977],
    ];

    $claimDocStmt = $db->prepare("
        INSERT INTO claim_documents (claim_id, file_name, file_path, file_type, file_size)
        VALUES (:claim_id, :file_name, :file_path, :file_type, :file_size)
    ");

    foreach ($claimDocs as $cd) {
        $claimDocStmt->execute($cd);
    }
    echo "Seeded " . count($claimDocs) . " claim documents.\n";

    // 7. Seed Payments
    $payments = [
        [
            'reference_number' => 'PAY-2026-000001',
            'user_id' => 4,
            'policy_id' => 1,
            'claim_id' => null,
            'type' => 'premium',
            'amount' => 3250.00,
            'method' => 'bank_transfer',
            'status' => 'completed',
            'processed_by' => 2,
            'notes' => 'Premium payment received and verified.',
        ],
        [
            'reference_number' => 'PAY-2026-000002',
            'user_id' => 4,
            'policy_id' => 2,
            'claim_id' => null,
            'type' => 'premium',
            'amount' => 1562.50,
            'method' => 'gcash',
            'status' => 'completed',
            'processed_by' => 2,
            'notes' => 'GCash payment confirmed.',
        ],
        [
            'reference_number' => 'PAY-2026-000003',
            'user_id' => 5,
            'policy_id' => 3,
            'claim_id' => null,
            'type' => 'premium',
            'amount' => 9562.50,
            'method' => 'bank_transfer',
            'status' => 'completed',
            'processed_by' => 3,
            'notes' => 'Premium payment processed.',
        ],
        [
            'reference_number' => 'PAY-2026-000004',
            'user_id' => 4,
            'policy_id' => 1,
            'claim_id' => 1,
            'type' => 'payout',
            'amount' => 28000.00,
            'method' => 'bank_transfer',
            'status' => 'completed',
            'processed_by' => 1,
            'notes' => 'Indemnity payout for CLM-2026-000001 released via LandBank LGU disbursement.',
        ],
    ];

    $payStmt = $db->prepare("
        INSERT INTO payments (
            reference_number, user_id, policy_id, claim_id, type,
            amount, method, status, processed_by, notes
        ) VALUES (
            :reference_number, :user_id, :policy_id, :claim_id, :type,
            :amount, :method, :status, :processed_by, :notes
        )
    ");

    foreach ($payments as $py) {
        $payStmt->execute($py);
        echo "Seeded Payment: {$py['reference_number']} (₱" . number_format($py['amount'], 2) . ") [{$py['type']}]\n";
    }

    // 8. Seed In-App Notifications
    $notifications = [
        [
            'user_id' => 4,
            'title' => 'Policy Approved',
            'message' => 'Your policy POL-2026-000001 has been approved and is now active.',
            'type' => 'success',
            'is_read' => 1,
            'link' => '/web-based-crop-insurance/views/user/application-status.php',
        ],
        [
            'user_id' => 4,
            'title' => 'Claim Approved',
            'message' => 'Your claim CLM-2026-000001 has been approved. Indemnity payout of ₱28,000.00 released.',
            'type' => 'success',
            'is_read' => 0,
            'link' => '/web-based-crop-insurance/views/user/file-claim.php',
        ],
        [
            'user_id' => 4,
            'title' => 'Policy Approved',
            'message' => 'Your policy POL-2026-000002 has been approved and is now active.',
            'type' => 'success',
            'is_read' => 1,
            'link' => '/web-based-crop-insurance/views/user/application-status.php',
        ],
        [
            'user_id' => 5,
            'title' => 'Policy Approved',
            'message' => 'Your policy POL-2026-000003 has been approved and is now active.',
            'type' => 'success',
            'is_read' => 1,
            'link' => '/web-based-crop-insurance/views/user/application-status.php',
        ],
        [
            'user_id' => 5,
            'title' => 'Claim Under Review',
            'message' => 'Your claim CLM-2026-000002 is currently under review by our agents.',
            'type' => 'info',
            'is_read' => 0,
            'link' => '/web-based-crop-insurance/views/user/file-claim.php',
        ],
        [
            'user_id' => 6,
            'title' => 'Application Received',
            'message' => 'Your policy application POL-2026-000004 has been received and queued for review.',
            'type' => 'info',
            'is_read' => 0,
            'link' => '/web-based-crop-insurance/views/user/application-status.php',
        ],
    ];

    $notifStmt = $db->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read, link)
        VALUES (:user_id, :title, :message, :type, :is_read, :link)
    ");

    foreach ($notifications as $n) {
        $notifStmt->execute($n);
    }
    echo "Seeded " . count($notifications) . " in-app notifications.\n";

    // 9. Seed SMS Logs (for PhilSMS audit demonstration in Step 14)
    $smsLogs = [
        [
            'recipient' => '09333333333',
            'message' => 'Dear Pedro Dela Cruz, your Crop Insurance application (POL-2026-000001) has been APPROVED. Your policy is now active. Thank you! - Sto. Nino Crop Insurance',
            'status' => 'sent',
            'http_code' => 200,
            'response_body' => '{"status":"success","message":"Your message was successfully delivered","data":{"uid":"6a885da19eb31","to":"09333333333","from":"PhilSMS","message":"Policy approved","status":"Delivered","cost":"1","sms_count":1}}',
            'error_message' => null,
            'created_at' => '2026-01-03 10:00:15',
        ],
        [
            'recipient' => '09333333333',
            'message' => 'Dear Pedro Dela Cruz, your Insurance Claim (CLM-2026-000001) has been APPROVED for indemnity of ₱28,000.00. Please check your account. - Sto. Nino Crop Insurance',
            'status' => 'sent',
            'http_code' => 200,
            'response_body' => '{"status":"success","message":"Your message was successfully delivered","data":{"uid":"6a885fa22cb90","to":"09333333333","from":"PhilSMS","message":"Claim approved","status":"Delivered","cost":"1","sms_count":1}}',
            'error_message' => null,
            'created_at' => '2026-03-20 10:00:20',
        ],
        [
            'recipient' => '09444444444',
            'message' => 'Dear Luisa Garcia, your Crop Insurance application (POL-2026-000003) has been APPROVED. Your policy is now active. Thank you! - Sto. Nino Crop Insurance',
            'status' => 'sent',
            'http_code' => 200,
            'response_body' => '{"status":"success","message":"Your message was successfully delivered","data":{"uid":"6a8860b33ec81","to":"09444444444","from":"PhilSMS","message":"Policy approved","status":"Delivered","cost":"1","sms_count":1}}',
            'error_message' => null,
            'created_at' => '2026-02-03 09:30:12',
        ],
        [
            'recipient' => '09444444444',
            'message' => 'Dear Luisa Garcia, your Insurance Claim (CLM-2026-000002) is now UNDER REVIEW by the Municipal Agriculture Office. - Sto. Nino Crop Insurance',
            'status' => 'sent',
            'http_code' => 200,
            'response_body' => '{"status":"success","message":"Your message was successfully delivered","data":{"uid":"6a8861d44fd72","to":"09444444444","from":"PhilSMS","message":"Claim under review","status":"Delivered","cost":"1","sms_count":1}}',
            'error_message' => null,
            'created_at' => '2026-04-10 14:15:30',
        ],
        [
            'recipient' => '09555555555',
            'message' => 'Dear Ramon Flores, your Crop Insurance application (POL-2026-000004) status has been set to PENDING. Please monitor your account for updates. - Sto. Nino Crop Insurance',
            'status' => 'sent',
            'http_code' => 200,
            'response_body' => '{"status":"success","message":"Your message was successfully delivered","data":{"uid":"6a8862e55ae63","to":"09555555555","from":"PhilSMS","message":"Application pending","status":"Delivered","cost":"1","sms_count":1}}',
            'error_message' => null,
            'created_at' => '2026-05-01 11:20:05',
        ],
        [
            'recipient' => '09333333333',
            'message' => 'LGU Sto. Nino Crop Insurance: Hello Pedro Dela Cruz, your policy POL-2026-000001 has been APPROVED with coverage amount ₱50,000.00.',
            'status' => 'sent',
            'http_code' => 200,
            'response_body' => '{"status":"success","message":"Your message was successfully delivered","data":{"uid":"6a8863f66bf54","to":"09333333333","from":"PhilSMS","status":"Delivered"}}',
            'error_message' => null,
            'created_at' => '2026-01-03 10:00:25',
        ],
    ];

    $smsStmt = $db->prepare("
        INSERT INTO sms_logs (recipient, message, status, http_code, response_body, error_message, created_at)
        VALUES (:recipient, :message, :status, :http_code, :response_body, :error_message, :created_at)
    ");

    foreach ($smsLogs as $s) {
        $smsStmt->execute($s);
    }
    echo "Seeded " . count($smsLogs) . " PhilSMS Gateway audit logs.\n";

    // Re-enable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "\n=== ALL DEMONSTRATION ACCOUNTS & WALKTHROUGH DATA SEEDED SUCCESSFULLY! ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
