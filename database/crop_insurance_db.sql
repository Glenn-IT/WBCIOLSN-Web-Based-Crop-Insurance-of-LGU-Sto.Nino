-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2026 at 05:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crop_insurance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(63, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:46:40'),
(64, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:54:05'),
(65, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:54:19'),
(66, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:54:43'),
(67, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:54:48'),
(68, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:01:04'),
(69, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:01:14'),
(70, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:01:50'),
(71, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:02:00'),
(72, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:02:37'),
(73, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:02:42'),
(74, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:05:55'),
(75, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:05:57'),
(76, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:06:20'),
(77, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:06:23'),
(78, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:25:26'),
(79, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:26:15'),
(80, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:27:45'),
(81, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:28:53'),
(82, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:29:10'),
(83, 10, 'register', 'auth', 'New farmer registered.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:53:04'),
(84, 10, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:53:10'),
(85, 10, 'forgot_password', 'auth', 'Password reset requested.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:53:28'),
(86, 10, 'reset_password', 'auth', 'Password was reset.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:54:23'),
(87, 10, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:54:41'),
(88, 10, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:56:27'),
(89, 10, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:04:10'),
(90, 10, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:12:40'),
(91, 10, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:14:45'),
(92, 10, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:15:25'),
(93, 10, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:18:46'),
(94, 10, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:18:48'),
(95, 10, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:19:07'),
(96, 10, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:19:09'),
(97, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:20:03'),
(98, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:20:07');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` int(10) UNSIGNED NOT NULL,
  `claim_number` varchar(50) NOT NULL,
  `policy_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `incident_type` varchar(150) NOT NULL,
  `incident_date` date NOT NULL,
  `description` text NOT NULL,
  `estimated_loss` decimal(15,2) NOT NULL,
  `approved_amount` decimal(15,2) DEFAULT NULL,
  `status` enum('submitted','under_review','approved','rejected','paid') NOT NULL DEFAULT 'submitted',
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `claim_documents`
--

CREATE TABLE `claim_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `claim_id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(10) UNSIGNED DEFAULT NULL COMMENT 'Size in bytes',
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coverage_plans`
--

CREATE TABLE `coverage_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `coverage_type` enum('natural_disaster','pest_disease','drought','flood','comprehensive') NOT NULL,
  `coverage_percent` decimal(5,2) NOT NULL COMMENT 'Percentage of loss covered (e.g. 80.00)',
  `premium_rate` decimal(5,4) NOT NULL COMMENT 'Rate per hectare per season (e.g. 0.0500)',
  `max_coverage_amount` decimal(15,2) NOT NULL,
  `duration_months` int(11) NOT NULL DEFAULT 6,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coverage_plans`
--

INSERT INTO `coverage_plans` (`id`, `plan_name`, `description`, `coverage_type`, `coverage_percent`, `premium_rate`, `max_coverage_amount`, `duration_months`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Basic Flood Cover', 'Covers crop loss due to flooding', 'flood', 70.00, 0.0500, 50000.00, 6, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(2, 'Natural Disaster Plan', 'Covers typhoons, earthquakes, and other natural events', 'natural_disaster', 80.00, 0.0650, 100000.00, 6, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(3, 'Pest & Disease Shield', 'Covers losses from pest infestations and crop diseases', 'pest_disease', 75.00, 0.0450, 75000.00, 6, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(4, 'Drought Protection', 'Covers crop loss due to prolonged drought', 'drought', 70.00, 0.0550, 60000.00, 6, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(5, 'Comprehensive Plan', 'Full coverage for all insurable risks', 'comprehensive', 90.00, 0.0850, 200000.00, 12, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `crop_types`
--

CREATE TABLE `crop_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crop_types`
--

INSERT INTO `crop_types` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Rice', 'Palay / rice farming', '2026-06-08 14:25:12'),
(2, 'Corn', 'White and yellow corn', '2026-06-08 14:25:12'),
(3, 'Sugarcane', 'Sugar cane production', '2026-06-08 14:25:12'),
(4, 'Banana', 'Banana plantation', '2026-06-08 14:25:12'),
(5, 'Coconut', 'Coconut/copra farming', '2026-06-08 14:25:12'),
(6, 'Vegetables', 'Various vegetable crops', '2026-06-08 14:25:12'),
(7, 'Cassava', 'Cassava/root crops', '2026-06-08 14:25:12'),
(8, 'Coffee', 'Coffee bean farming', '2026-06-08 14:25:12'),
(9, 'Mango', 'Mango orchards', '2026-06-08 14:25:12'),
(10, 'Tobacco', 'Tobacco leaf farming', '2026-06-08 14:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `farms`
--

CREATE TABLE `farms` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `farm_name` varchar(150) NOT NULL,
  `application_type` varchar(50) DEFAULT NULL,
  `farmer_category` varchar(100) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `province` varchar(100) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `area_hectares` decimal(10,4) NOT NULL,
  `crop_type_id` int(10) UNSIGNED NOT NULL,
  `soil_type` varchar(100) DEFAULT NULL,
  `irrigation` tinyint(1) DEFAULT 0,
  `tenurial_status` varchar(50) DEFAULT NULL,
  `planting_method` varchar(50) DEFAULT NULL,
  `planting_date` date DEFAULT NULL,
  `harvest_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `policy_id` int(10) UNSIGNED DEFAULT NULL,
  `claim_id` int(10) UNSIGNED DEFAULT NULL,
  `type` enum('premium','payout') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method` enum('cash','bank_transfer','gcash','maya','check') NOT NULL DEFAULT 'cash',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_date` datetime DEFAULT current_timestamp(),
  `processed_by` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` int(10) UNSIGNED NOT NULL,
  `policy_number` varchar(50) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `farm_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `agent_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','under_review','active','expired','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_premium` decimal(15,2) NOT NULL,
  `coverage_amount` decimal(15,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `cause_of_damage` varchar(100) DEFAULT NULL,
  `percent_damage` decimal(5,2) DEFAULT NULL,
  `financial_damage` decimal(15,2) DEFAULT NULL,
  `damage_description` text DEFAULT NULL,
  `date_of_loss` date DEFAULT NULL,
  `farm_verification` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `damage_verification` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `coverage_verification` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `approved_at` datetime DEFAULT NULL,
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policy_documents`
--

CREATE TABLE `policy_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `policy_id` int(10) UNSIGNED NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'damage_photo',
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(10) UNSIGNED DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `farmer_type` varchar(100) DEFAULT NULL,
  `role` enum('admin','agent','farmer') NOT NULL DEFAULT 'farmer',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `profile_photo` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `address`, `farmer_type`, `role`, `status`, `profile_photo`, `email_verified`, `reset_token`, `reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'System', 'Admin', 'admin@cropinsurance.ph', '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', '09000000001', NULL, NULL, 'admin', 'active', NULL, 1, NULL, NULL, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(10, 'Glenard', 'Pagurayan', 'glenard0823@gmail.com', '$2y$12$XlYYLxHXRi6pu80o5v7NE./gFpskoe.vMLBU4e8gT/Eo0E5TYjN..', '09557997409', NULL, NULL, 'farmer', 'active', NULL, 0, NULL, NULL, '2026-06-27 23:52:59', '2026-06-27 23:54:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `claim_number` (`claim_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_claims_policy` (`policy_id`),
  ADD KEY `idx_claims_status` (`status`);

--
-- Indexes for table `claim_documents`
--
ALTER TABLE `claim_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claim_id` (`claim_id`);

--
-- Indexes for table `coverage_plans`
--
ALTER TABLE `coverage_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crop_types`
--
ALTER TABLE `crop_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `farms`
--
ALTER TABLE `farms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_type_id` (`crop_type_id`),
  ADD KEY `idx_farms_user` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_id`,`is_read`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`),
  ADD KEY `policy_id` (`policy_id`),
  ADD KEY `claim_id` (`claim_id`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_payments_user` (`user_id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `policy_number` (`policy_number`),
  ADD KEY `farm_id` (`farm_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_policies_user` (`user_id`),
  ADD KEY `idx_policies_status` (`status`);

--
-- Indexes for table `policy_documents`
--
ALTER TABLE `policy_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_policy_id` (`policy_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `claim_documents`
--
ALTER TABLE `claim_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coverage_plans`
--
ALTER TABLE `coverage_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `crop_types`
--
ALTER TABLE `crop_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `farms`
--
ALTER TABLE `farms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `policy_documents`
--
ALTER TABLE `policy_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claims_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claims_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `claim_documents`
--
ALTER TABLE `claim_documents`
  ADD CONSTRAINT `claim_documents_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farms`
--
ALTER TABLE `farms`
  ADD CONSTRAINT `farms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `farms_ibfk_2` FOREIGN KEY (`crop_type_id`) REFERENCES `crop_types` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `policies`
--
ALTER TABLE `policies`
  ADD CONSTRAINT `policies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `policies_ibfk_2` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `policies_ibfk_3` FOREIGN KEY (`plan_id`) REFERENCES `coverage_plans` (`id`),
  ADD CONSTRAINT `policies_ibfk_4` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_ibfk_5` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
