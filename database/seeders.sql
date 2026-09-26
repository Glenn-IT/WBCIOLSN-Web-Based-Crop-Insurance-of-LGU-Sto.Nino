-- ============================================================
-- Web-Based Crop Insurance System - Seed Data
-- Based on: docs/CAPSTONE-PRESENTATION-WALKTHROUGH.md
-- Run AFTER schema.sql
-- ============================================================

USE crop_insurance_db;

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE claim_documents;
TRUNCATE TABLE claims;
TRUNCATE TABLE policy_documents;
TRUNCATE TABLE payments;
TRUNCATE TABLE notifications;
TRUNCATE TABLE policies;
TRUNCATE TABLE farms;
TRUNCATE TABLE sms_logs;

-- ============================================================
-- SEED: crop_types
-- ============================================================
INSERT INTO crop_types (id, name, description) VALUES
(1,  'Rice',        'Palay / rice farming'),
(2,  'Corn',        'White and yellow corn'),
(3,  'Sugarcane',   'Sugar cane production'),
(4,  'Banana',      'Banana plantation'),
(5,  'Coconut',     'Coconut/copra farming'),
(6,  'Vegetables',  'Various vegetable crops'),
(7,  'Cassava',     'Cassava/root crops'),
(8,  'Coffee',      'Coffee bean farming'),
(9,  'Mango',       'Mango orchards'),
(10, 'Tobacco',     'Tobacco leaf farming')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);

-- ============================================================
-- SEED: coverage_plans
-- ============================================================
INSERT INTO coverage_plans (id, plan_name, description, coverage_type, coverage_percent, premium_rate, max_coverage_amount, duration_months, is_active) VALUES
(1, 'Basic Flood Cover',       'Covers crop loss due to flooding',                         'flood',            70.00, 0.0500, 50000.00,  6, 1),
(2, 'Natural Disaster Plan',   'Covers typhoons, earthquakes, and other natural events',   'natural_disaster', 80.00, 0.0650, 100000.00, 6, 1),
(3, 'Pest & Disease Shield',   'Covers losses from pest infestations and crop diseases',   'pest_disease',     75.00, 0.0450, 75000.00,  6, 1),
(4, 'Drought Protection',      'Covers crop loss due to prolonged drought',                'drought',          70.00, 0.0550, 60000.00,  6, 1),
(5, 'Comprehensive Plan',      'Full coverage for all insurable risks',                    'comprehensive',    90.00, 0.0850, 200000.00, 12, 1)
ON DUPLICATE KEY UPDATE
    plan_name=VALUES(plan_name), description=VALUES(description), coverage_type=VALUES(coverage_type),
    coverage_percent=VALUES(coverage_percent), premium_rate=VALUES(premium_rate),
    max_coverage_amount=VALUES(max_coverage_amount), duration_months=VALUES(duration_months), is_active=VALUES(is_active);

-- ============================================================
-- SEED: users (Password for all accounts: "Password@123")
-- ============================================================
INSERT INTO users (
    id, first_name, last_name, email, password, must_change_password,
    phone, address, farmer_type, role, status, email_verified,
    security_question, security_answer_hash, failed_attempts, locked_until
) VALUES
(1, 'System',  'Admin',     'admin@cropinsurance.ph',   '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', 0, '09000000001', 'MAO Sto. Niño HQ, Sto. Niño, Cagayan', NULL, 'admin',  'active', 1, 'What was your childhood nickname?', '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS', 0, NULL),
(2, 'Maria',   'Santos',    'agent1@cropinsurance.ph',  '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', 0, '09111111111', 'Field Inspection Unit, MAO Sto. Niño', NULL, 'agent',  'active', 1, 'What was your childhood nickname?', '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS', 0, NULL),
(3, 'Jose',    'Reyes',     'agent2@cropinsurance.ph',  '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', 0, '09222222222', 'Claims Verifier Unit, MAO Sto. Niño',  NULL, 'agent',  'active', 1, 'What was your childhood nickname?', '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS', 0, NULL),
(4, 'Pedro',   'Dela Cruz', 'farmer1@cropinsurance.ph', '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', 0, '09333333333', 'Brgy. Maligaya, Sto. Niño, Cagayan', 'Small Farmer', 'farmer', 'active', 1, 'What was your childhood nickname?', '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS', 0, NULL),
(5, 'Luisa',   'Garcia',    'farmer2@cropinsurance.ph', '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', 0, '09444444444', 'Brgy. San Isidro, Sto. Niño, Cagayan', 'Commercial Farmer', 'farmer', 'active', 1, 'What was your childhood nickname?', '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS', 0, NULL),
(6, 'Ramon',   'Flores',    'farmer3@cropinsurance.ph', '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', 0, '09555555555', 'Brgy. Rizal, Sto. Niño, Cagayan', 'Small Farmer', 'farmer', 'active', 1, 'What was your childhood nickname?', '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS', 0, NULL),
(7, 'Glenard', 'Pagurayan', 'glenard2308@gmail.com',    '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', 0, '09557997409', 'Sto. Niño, Cagayan', NULL, 'admin',  'active', 1, 'What was your childhood nickname?', '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS', 0, NULL)
ON DUPLICATE KEY UPDATE
    first_name=VALUES(first_name), last_name=VALUES(last_name), email=VALUES(email),
    password=VALUES(password), phone=VALUES(phone), address=VALUES(address),
    farmer_type=VALUES(farmer_type), role=VALUES(role), status=VALUES(status),
    email_verified=VALUES(email_verified), security_question=VALUES(security_question),
    security_answer_hash=VALUES(security_answer_hash), failed_attempts=0, locked_until=NULL;

-- ============================================================
-- SEED: farms (Sto. Niño, Cagayan with OpenStreetMap coordinates)
-- ============================================================
INSERT INTO farms (
    id, user_id, farm_name, application_type, farmer_category,
    location, province, municipality, barangay, area_hectares,
    crop_type_id, soil_type, irrigation, tenurial_status,
    planting_method, planting_date, harvest_date, latitude, longitude
) VALUES
(1, 4, 'Dela Cruz Farm',        'New',     'Small Farmer',      'Brgy. Maligaya, Sto. Niño, Cagayan',   'Cagayan', 'Sto. Niño', 'Maligaya',   2.5000, 1, 'Clay Loam',  1, 'Owner',  'Direct Seeding', '2026-01-10', '2026-05-15', 17.89230000, 121.57140000),
(2, 4, 'North Field',           'Renewal', 'Small Farmer',      'Brgy. Pinili, Sto. Niño, Cagayan',     'Cagayan', 'Sto. Niño', 'Pinili',     1.2500, 1, 'Sandy Loam', 0, 'Owner',  'Transplanting',  '2026-01-20', '2026-05-25', 17.89800000, 121.57600000),
(3, 5, 'Garcia Cornfield',      'New',     'Commercial Farmer', 'Brgy. San Isidro, Sto. Niño, Cagayan', 'Cagayan', 'Sto. Niño', 'San Isidro', 3.7500, 2, 'Clay',        1, 'Owner',  'Direct Seeding', '2026-02-05', '2026-06-30', 17.88450000, 121.56500000),
(4, 6, 'Flores Sugarcane Farm', 'New',     'Small Farmer',      'Brgy. Rizal, Sto. Niño, Cagayan',      'Cagayan', 'Sto. Niño', 'Rizal',      5.0000, 3, 'Loam',        1, 'Tenant', 'Ratoon',         '2026-03-01', '2026-11-30', 17.90500000, 121.58200000)
ON DUPLICATE KEY UPDATE
    user_id=VALUES(user_id), farm_name=VALUES(farm_name), application_type=VALUES(application_type),
    farmer_category=VALUES(farmer_category), location=VALUES(location), province=VALUES(province),
    municipality=VALUES(municipality), barangay=VALUES(barangay), area_hectares=VALUES(area_hectares),
    crop_type_id=VALUES(crop_type_id), soil_type=VALUES(soil_type), irrigation=VALUES(irrigation),
    tenurial_status=VALUES(tenurial_status), planting_method=VALUES(planting_method),
    planting_date=VALUES(planting_date), harvest_date=VALUES(harvest_date),
    latitude=VALUES(latitude), longitude=VALUES(longitude);

-- ============================================================
-- SEED: policies (Active Policies & Ready-for-Triage Application)
-- ============================================================
INSERT INTO policies (
    id, policy_number, user_id, farm_id, plan_id, agent_id,
    status, start_date, end_date, total_premium, coverage_amount,
    remarks, cause_of_damage, percent_damage, financial_damage,
    damage_description, date_of_loss, farm_verification,
    damage_verification, coverage_verification, approved_at, approved_by
) VALUES
(1, 'POL-2026-000001', 4, 1, 2, 2, 'active',  '2026-01-01', '2026-12-31', 3250.00,  50000.00, 'Verified and approved by MAO Sto. Niño.', 'Typhoon', 70.00, 35000.00, 'Severe storm winds and flooding flattened standing palay crop across the parcel.', '2026-03-15', 'Verified', 'Verified', 'Verified', '2026-01-03 10:00:00', 1),
(2, 'POL-2026-000002', 4, 2, 1, 2, 'active',  '2026-01-15', '2026-12-31', 1562.50,  25000.00, 'Approved for seasonal flood protection near river basin.', 'Flood', 40.00, 10000.00, 'Cagayan river backflow caused seasonal submergence.', '2026-02-10', 'Verified', 'Verified', 'Verified', '2026-01-16 14:00:00', 1),
(3, 'POL-2026-000003', 5, 3, 5, 3, 'active',  '2026-02-01', '2027-01-31', 9562.50, 150000.00, 'Comprehensive coverage approved for commercial corn farm.', 'Pest/Disease', 60.00, 45000.00, 'Fall armyworm and corn borer outbreak documented across field.', '2026-04-10', 'Verified', 'Verified', 'Verified', '2026-02-03 09:30:00', 1),
(4, 'POL-2026-000004', 6, 4, 3, 3, 'pending', '2026-05-01', '2026-11-30', 8437.50, 100000.00, 'Newly submitted application awaiting MAO field inspection & 3-tier validation.', 'Flood', 50.00, 42187.50, 'Heavy river overflow inundated sugarcane rows for 48 hours.', '2026-05-01', 'Pending', 'Pending', 'Pending', NULL, NULL)
ON DUPLICATE KEY UPDATE
    policy_number=VALUES(policy_number), user_id=VALUES(user_id), farm_id=VALUES(farm_id),
    plan_id=VALUES(plan_id), agent_id=VALUES(agent_id), status=VALUES(status),
    start_date=VALUES(start_date), end_date=VALUES(end_date), total_premium=VALUES(total_premium),
    coverage_amount=VALUES(coverage_amount), remarks=VALUES(remarks), cause_of_damage=VALUES(cause_of_damage),
    percent_damage=VALUES(percent_damage), financial_damage=VALUES(financial_damage),
    damage_description=VALUES(damage_description), date_of_loss=VALUES(date_of_loss),
    farm_verification=VALUES(farm_verification), damage_verification=VALUES(damage_verification),
    coverage_verification=VALUES(coverage_verification), approved_at=VALUES(approved_at), approved_by=VALUES(approved_by);

-- ============================================================
-- SEED: policy_documents (5 geo-tagged damage photos + valid ID)
-- ============================================================
INSERT INTO policy_documents (policy_id, document_type, file_name, file_path, file_type, file_size) VALUES
(4, 'damage_photo', 'flood_damage_plot_1.png', 'policies/file_6a26eed3cdb066.66263919.png', 'image/png',  413709),
(4, 'damage_photo', 'flood_damage_plot_2.png', 'policies/file_6a26eed528c066.30623825.png', 'image/png',  413709),
(4, 'damage_photo', 'flood_damage_plot_3.png', 'policies/file_6a26eed5d69fb0.48116279.png', 'image/png',  413709),
(4, 'damage_photo', 'flood_damage_plot_4.png', 'policies/file_6a26eed68a2413.29363042.png', 'image/png',  413709),
(4, 'damage_photo', 'flood_damage_plot_5.png', 'policies/file_6a26eed7d6ac86.91061740.png', 'image/png',  413709),
(4, 'valid_id',     'government_id.jpg',        'policies/file_6a26eebb3b01b2.81014618.jpg', 'image/jpeg', 54977),
(1, 'damage_photo', 'typhoon_damage_proof.png', 'policies/file_6a26eed3cdb066.66263919.png', 'image/png',  413709),
(1, 'valid_id',     'farmer_id.jpg',             'policies/file_6a26eebb3b01b2.81014618.jpg', 'image/jpeg', 54977),
(3, 'damage_photo', 'corn_damage_proof.png',    'policies/file_6a26eed528c066.30623825.png', 'image/png',  413709),
(3, 'valid_id',     'valid_id.jpg',             'policies/file_6a26eebb3b01b2.81014618.jpg', 'image/jpeg', 54977);

-- ============================================================
-- SEED: claims (Approved Claim & Under Review Claim)
-- ============================================================
INSERT INTO claims (
    id, claim_number, policy_id, user_id, incident_type,
    incident_date, description, estimated_loss, approved_amount,
    status, reviewed_by, reviewed_at, remarks
) VALUES
(1, 'CLM-2026-000001', 1, 4, 'Typhoon Damage',   '2026-03-15', 'Typhoon caused severe flooding and flattened standing rice crops across the entire 2.5-hectare farm parcel.', 35000.00, 28000.00, 'approved',     2, '2026-03-20 10:00:00', 'Verified 70% lodging damage. Indemnity cleared for ₱28,000.00 pursuant to PCIC / LGU schedule.'),
(2, 'CLM-2026-000002', 3, 5, 'Pest Infestation', '2026-04-10', 'Brown planthopper and corn borer infestation affecting approximately 60% of corn field.',                           45000.00, NULL,     'under_review', NULL, NULL,                'Field inspection assigned to Agent Jose Reyes for visual damage validation.')
ON DUPLICATE KEY UPDATE
    claim_number=VALUES(claim_number), policy_id=VALUES(policy_id), user_id=VALUES(user_id),
    incident_type=VALUES(incident_type), incident_date=VALUES(incident_date), description=VALUES(description),
    estimated_loss=VALUES(estimated_loss), approved_amount=VALUES(approved_amount), status=VALUES(status),
    reviewed_by=VALUES(reviewed_by), reviewed_at=VALUES(reviewed_at), remarks=VALUES(remarks);

-- ============================================================
-- SEED: claim_documents
-- ============================================================
INSERT INTO claim_documents (claim_id, file_name, file_path, file_type, file_size) VALUES
(1, 'typhoon_damage_rice.jpg', 'claims/file_6a26f27d2156e9.20885323.jpg', 'image/jpeg', 54977),
(2, 'corn_pest_damage.jpg',    'claims/file_6a84e2327edfc1.81765159.jpg', 'image/jpeg', 54977);

-- ============================================================
-- SEED: payments
-- ============================================================
INSERT INTO payments (
    reference_number, user_id, policy_id, claim_id, type,
    amount, method, status, processed_by, notes
) VALUES
('PAY-2026-000001', 4, 1, NULL, 'premium',  3250.00, 'bank_transfer', 'completed', 2, 'Premium payment received and verified.'),
('PAY-2026-000002', 4, 2, NULL, 'premium',  1562.50, 'gcash',         'completed', 2, 'GCash payment confirmed.'),
('PAY-2026-000003', 5, 3, NULL, 'premium',  9562.50, 'bank_transfer', 'completed', 3, 'Premium payment processed.'),
('PAY-2026-000004', 4, 1, 1,    'payout',  28000.00, 'bank_transfer', 'completed', 1, 'Indemnity payout for CLM-2026-000001 released via LandBank LGU disbursement.')
ON DUPLICATE KEY UPDATE
    user_id=VALUES(user_id), policy_id=VALUES(policy_id), claim_id=VALUES(claim_id),
    type=VALUES(type), amount=VALUES(amount), method=VALUES(method), status=VALUES(status),
    processed_by=VALUES(processed_by), notes=VALUES(notes);

-- ============================================================
-- SEED: notifications
-- ============================================================
INSERT INTO notifications (user_id, title, message, type, is_read, link) VALUES
(4, 'Policy Approved',      'Your policy POL-2026-000001 has been approved and is now active.',                   'success', 1, '/web-based-crop-insurance/views/user/application-status.php'),
(4, 'Claim Approved',       'Your claim CLM-2026-000001 has been approved. Indemnity payout of ₱28,000.00 released.', 'success', 0, '/web-based-crop-insurance/views/user/file-claim.php'),
(4, 'Policy Approved',      'Your policy POL-2026-000002 has been approved and is now active.',                   'success', 1, '/web-based-crop-insurance/views/user/application-status.php'),
(5, 'Policy Approved',      'Your policy POL-2026-000003 has been approved and is now active.',                   'success', 1, '/web-based-crop-insurance/views/user/application-status.php'),
(5, 'Claim Under Review',   'Your claim CLM-2026-000002 is currently under review by our agents.',                'info',    0, '/web-based-crop-insurance/views/user/file-claim.php'),
(6, 'Application Received', 'Your policy application POL-2026-000004 has been received and queued for review.',  'info',    0, '/web-based-crop-insurance/views/user/application-status.php');

-- ============================================================
-- SEED: sms_logs (PhilSMS Gateway Audit Log for Step 14)
-- ============================================================
INSERT INTO sms_logs (recipient, message, status, http_code, response_body, error_message, created_at) VALUES
('09333333333', 'Dear Pedro Dela Cruz, your Crop Insurance application (POL-2026-000001) has been APPROVED. Your policy is now active. Thank you! - Sto. Nino Crop Insurance', 'sent', 200, '{"status":"success","message":"Your message was successfully delivered","data":{"to":"09333333333","from":"PhilSMS","status":"Delivered"}}', NULL, '2026-01-03 10:00:15'),
('09333333333', 'Dear Pedro Dela Cruz, your Insurance Claim (CLM-2026-000001) has been APPROVED for indemnity of ₱28,000.00. Please check your account. - Sto. Nino Crop Insurance', 'sent', 200, '{"status":"success","message":"Your message was successfully delivered","data":{"to":"09333333333","from":"PhilSMS","status":"Delivered"}}', NULL, '2026-03-20 10:00:20'),
('09444444444', 'Dear Luisa Garcia, your Crop Insurance application (POL-2026-000003) has been APPROVED. Your policy is now active. Thank you! - Sto. Nino Crop Insurance', 'sent', 200, '{"status":"success","message":"Your message was successfully delivered","data":{"to":"09444444444","from":"PhilSMS","status":"Delivered"}}', NULL, '2026-02-03 09:30:12'),
('09444444444', 'Dear Luisa Garcia, your Insurance Claim (CLM-2026-000002) is now UNDER REVIEW by the Municipal Agriculture Office. - Sto. Nino Crop Insurance', 'sent', 200, '{"status":"success","message":"Your message was successfully delivered","data":{"to":"09444444444","from":"PhilSMS","status":"Delivered"}}', NULL, '2026-04-10 14:15:30'),
('09555555555', 'Dear Ramon Flores, your Crop Insurance application (POL-2026-000004) status has been set to PENDING. Please monitor your account for updates. - Sto. Nino Crop Insurance', 'sent', 200, '{"status":"success","message":"Your message was successfully delivered","data":{"to":"09555555555","from":"PhilSMS","status":"Delivered"}}', NULL, '2026-05-01 11:20:05'),
('09333333333', 'LGU Sto. Nino Crop Insurance: Hello Pedro Dela Cruz, your policy POL-2026-000001 has been APPROVED with coverage amount ₱50,000.00.', 'sent', 200, '{"status":"success","message":"Your message was successfully delivered","data":{"to":"09333333333","from":"PhilSMS","status":"Delivered"}}', NULL, '2026-01-03 10:00:25');

SET FOREIGN_KEY_CHECKS = 1;
