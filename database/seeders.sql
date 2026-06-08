-- ============================================================
-- Web-Based Crop Insurance System - Seed Data
-- Created: 2026-05-02
-- Run AFTER schema.sql
-- ============================================================

USE crop_insurance_db;

-- ============================================================
-- SEED: crop_types
-- ============================================================
INSERT INTO crop_types (name, description) VALUES
('Rice',        'Palay / rice farming'),
('Corn',        'White and yellow corn'),
('Sugarcane',   'Sugar cane production'),
('Banana',      'Banana plantation'),
('Coconut',     'Coconut/copra farming'),
('Vegetables',  'Various vegetable crops'),
('Cassava',     'Cassava/root crops'),
('Coffee',      'Coffee bean farming'),
('Mango',       'Mango orchards'),
('Tobacco',     'Tobacco leaf farming');

-- ============================================================
-- SEED: coverage_plans
-- ============================================================
INSERT INTO coverage_plans (plan_name, description, coverage_type, coverage_percent, premium_rate, max_coverage_amount, duration_months) VALUES
('Basic Flood Cover',       'Covers crop loss due to flooding',                         'flood',            70.00, 0.0500, 50000.00,  6),
('Natural Disaster Plan',   'Covers typhoons, earthquakes, and other natural events',   'natural_disaster', 80.00, 0.0650, 100000.00, 6),
('Pest & Disease Shield',   'Covers losses from pest infestations and crop diseases',   'pest_disease',     75.00, 0.0450, 75000.00,  6),
('Drought Protection',      'Covers crop loss due to prolonged drought',                'drought',          70.00, 0.0550, 60000.00,  6),
('Comprehensive Plan',      'Full coverage for all insurable risks',                    'comprehensive',    90.00, 0.0850, 200000.00, 12);

-- ============================================================
-- SEED: users (password = "Password@123" hashed with bcrypt)
-- ============================================================
-- Password for all seed users: Password@123
INSERT INTO users (first_name, last_name, email, password, phone, role, status, email_verified) VALUES
('System',  'Admin',    'admin@cropinsurance.ph',   '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', '09000000001', 'admin',  'active', 1),
('Maria',   'Santos',   'agent1@cropinsurance.ph',  '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', '09111111111', 'agent',  'active', 1),
('Jose',    'Reyes',    'agent2@cropinsurance.ph',  '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', '09222222222', 'agent',  'active', 1),
('Pedro',   'Dela Cruz','farmer1@cropinsurance.ph', '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', '09333333333', 'farmer', 'active', 1),
('Luisa',   'Garcia',   'farmer2@cropinsurance.ph', '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', '09444444444', 'farmer', 'active', 1),
('Ramon',   'Flores',   'farmer3@cropinsurance.ph', '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', '09555555555', 'farmer', 'active', 1);

-- ============================================================
-- SEED: farms
-- ============================================================
INSERT INTO farms (user_id, farm_name, location, province, municipality, barangay, area_hectares, crop_type_id, soil_type, irrigation) VALUES
(4, 'Dela Cruz Farm',   'Brgy. Maligaya, Cabanatuan, Nueva Ecija', 'Nueva Ecija',   'Cabanatuan',  'Maligaya',   2.5000, 1, 'Clay Loam', 1),
(4, 'North Field',      'Brgy. Pinili, Cabanatuan, Nueva Ecija',   'Nueva Ecija',   'Cabanatuan',  'Pinili',     1.2500, 1, 'Sandy Loam', 0),
(5, 'Garcia Cornfield', 'Brgy. San Isidro, Sto. Tomas, Batangas',  'Batangas',      'Sto. Tomas',  'San Isidro', 3.7500, 2, 'Clay',      1),
(6, 'Flores Sugarcane', 'Brgy. Rizal, San Carlos, Pangasinan',     'Pangasinan',    'San Carlos',  'Rizal',      5.0000, 3, 'Loam',      1);

-- ============================================================
-- SEED: policies (sample approved policies)
-- ============================================================
INSERT INTO policies (policy_number, user_id, farm_id, plan_id, agent_id, status, start_date, end_date, total_premium, coverage_amount, approved_at, approved_by) VALUES
('POL-2026-000001', 4, 1, 2, 2, 'active',   '2026-01-01', '2026-06-30', 3250.00,  50000.00, '2026-01-03 10:00:00', 1),
('POL-2026-000002', 4, 2, 1, 2, 'active',   '2026-01-15', '2026-07-14', 1562.50,  25000.00, '2026-01-16 14:00:00', 1),
('POL-2026-000003', 5, 3, 5, 3, 'active',   '2026-02-01', '2027-01-31', 9562.50, 150000.00, '2026-02-03 09:30:00', 1),
('POL-2026-000004', 6, 4, 3, 3, 'pending',  '2026-05-01', '2026-10-31', 8437.50, 100000.00, NULL, NULL);

-- ============================================================
-- SEED: claims (sample claims)
-- ============================================================
INSERT INTO claims (claim_number, policy_id, user_id, incident_type, incident_date, description, estimated_loss, approved_amount, status, reviewed_by, reviewed_at) VALUES
('CLM-2026-000001', 1, 4, 'Typhoon Damage',    '2026-03-15', 'Typhoon caused severe flooding and flattened standing rice crops in the entire farm area.', 35000.00, 28000.00, 'approved', 2, '2026-03-20 10:00:00'),
('CLM-2026-000002', 3, 5, 'Pest Infestation',  '2026-04-10', 'Brown planthopper infestation affecting approximately 60% of corn field.',                   45000.00, NULL,      'under_review', NULL, NULL);

-- ============================================================
-- SEED: payments
-- ============================================================
INSERT INTO payments (reference_number, user_id, policy_id, type, amount, method, status, processed_by) VALUES
('PAY-2026-000001', 4, 1, 'premium', 3250.00,  'bank_transfer', 'completed', 2),
('PAY-2026-000002', 4, 2, 'premium', 1562.50,  'gcash',         'completed', 2),
('PAY-2026-000003', 5, 3, 'premium', 9562.50,  'bank_transfer', 'completed', 3),
('PAY-2026-000004', 4, 1, 'payout',  28000.00, 'bank_transfer', 'completed', 1);

-- ============================================================
-- SEED: notifications (sample)
-- ============================================================
INSERT INTO notifications (user_id, title, message, type, is_read) VALUES
(4, 'Policy Approved',        'Your policy POL-2026-000001 has been approved and is now active.',     'success', 1),
(4, 'Claim Approved',         'Your claim CLM-2026-000001 has been approved. Payout of ₱28,000.00.', 'success', 0),
(5, 'Policy Approved',        'Your policy POL-2026-000003 has been approved and is now active.',     'success', 1),
(5, 'Claim Under Review',     'Your claim CLM-2026-000002 is currently under review by our agents.',  'info',    0),
(6, 'Application Received',   'Your policy application POL-2026-000004 has been received.',           'info',    0);
