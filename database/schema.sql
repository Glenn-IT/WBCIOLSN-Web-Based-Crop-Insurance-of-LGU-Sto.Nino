-- ============================================================
-- Web-Based Crop Insurance System - Database Schema
-- Created: 2026-05-02
-- ============================================================

CREATE DATABASE IF NOT EXISTS crop_insurance_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE crop_insurance_db;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name      VARCHAR(100) NOT NULL,
    last_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(191) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    phone           VARCHAR(20),
    role            ENUM('admin', 'agent', 'farmer') NOT NULL DEFAULT 'farmer',
    status          ENUM('pending', 'active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    profile_photo   VARCHAR(255) DEFAULT NULL,
    email_verified  TINYINT(1) DEFAULT 0,
    reset_token     VARCHAR(255) DEFAULT NULL,
    reset_expires   DATETIME DEFAULT NULL,
    failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,
    locked_until    DATETIME DEFAULT NULL,
    security_question    VARCHAR(255) DEFAULT NULL,
    security_answer_hash VARCHAR(255) DEFAULT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: crop_types
-- ============================================================
CREATE TABLE IF NOT EXISTS crop_types (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: farms
-- ============================================================
CREATE TABLE IF NOT EXISTS farms (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    farm_name       VARCHAR(150) NOT NULL,
    location        VARCHAR(255) NOT NULL,
    province        VARCHAR(100),
    municipality    VARCHAR(100),
    barangay        VARCHAR(100),
    area_hectares   DECIMAL(10, 4) NOT NULL,
    crop_type_id    INT UNSIGNED NOT NULL,
    soil_type       VARCHAR(100),
    irrigation      TINYINT(1) DEFAULT 0,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (crop_type_id) REFERENCES crop_types(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: coverage_plans
-- ============================================================
CREATE TABLE IF NOT EXISTS coverage_plans (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plan_name           VARCHAR(150) NOT NULL,
    description         TEXT,
    coverage_type       ENUM('natural_disaster', 'pest_disease', 'drought', 'flood', 'comprehensive') NOT NULL,
    coverage_percent    DECIMAL(5, 2) NOT NULL COMMENT 'Percentage of loss covered (e.g. 80.00)',
    premium_rate        DECIMAL(5, 4) NOT NULL COMMENT 'Rate per hectare per season (e.g. 0.0500)',
    max_coverage_amount DECIMAL(15, 2) NOT NULL,
    duration_months     INT NOT NULL DEFAULT 6,
    is_active           TINYINT(1) DEFAULT 1,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: policies
-- ============================================================
CREATE TABLE IF NOT EXISTS policies (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    policy_number       VARCHAR(50) NOT NULL UNIQUE,
    user_id             INT UNSIGNED NOT NULL,
    farm_id             INT UNSIGNED NOT NULL,
    plan_id             INT UNSIGNED NOT NULL,
    agent_id            INT UNSIGNED DEFAULT NULL,
    status              ENUM('pending', 'active', 'expired', 'cancelled', 'rejected') NOT NULL DEFAULT 'pending',
    start_date          DATE NOT NULL,
    end_date            DATE NOT NULL,
    total_premium       DECIMAL(15, 2) NOT NULL,
    coverage_amount     DECIMAL(15, 2) NOT NULL,
    remarks             TEXT DEFAULT NULL,
    approved_at         DATETIME DEFAULT NULL,
    approved_by         INT UNSIGNED DEFAULT NULL,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (farm_id) REFERENCES farms(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES coverage_plans(id) ON DELETE RESTRICT,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: claims
-- ============================================================
CREATE TABLE IF NOT EXISTS claims (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    claim_number    VARCHAR(50) NOT NULL UNIQUE,
    policy_id       INT UNSIGNED NOT NULL,
    user_id         INT UNSIGNED NOT NULL,
    incident_type   VARCHAR(150) NOT NULL,
    incident_date   DATE NOT NULL,
    description     TEXT NOT NULL,
    estimated_loss  DECIMAL(15, 2) NOT NULL,
    approved_amount DECIMAL(15, 2) DEFAULT NULL,
    status          ENUM('submitted', 'under_review', 'approved', 'rejected', 'paid') NOT NULL DEFAULT 'submitted',
    reviewed_by     INT UNSIGNED DEFAULT NULL,
    reviewed_at     DATETIME DEFAULT NULL,
    remarks         TEXT DEFAULT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (policy_id) REFERENCES policies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: claim_documents
-- ============================================================
CREATE TABLE IF NOT EXISTS claim_documents (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    claim_id    INT UNSIGNED NOT NULL,
    file_name   VARCHAR(255) NOT NULL,
    file_path   VARCHAR(500) NOT NULL,
    file_type   VARCHAR(50),
    file_size   INT UNSIGNED COMMENT 'Size in bytes',
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (claim_id) REFERENCES claims(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: payments
-- ============================================================
CREATE TABLE IF NOT EXISTS payments (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_number    VARCHAR(100) NOT NULL UNIQUE,
    user_id             INT UNSIGNED NOT NULL,
    policy_id           INT UNSIGNED DEFAULT NULL,
    claim_id            INT UNSIGNED DEFAULT NULL,
    type                ENUM('premium', 'payout') NOT NULL,
    amount              DECIMAL(15, 2) NOT NULL,
    method              ENUM('cash', 'bank_transfer', 'gcash', 'maya', 'check') NOT NULL DEFAULT 'cash',
    status              ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    transaction_date    DATETIME DEFAULT CURRENT_TIMESTAMP,
    processed_by        INT UNSIGNED DEFAULT NULL,
    notes               TEXT DEFAULT NULL,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (policy_id) REFERENCES policies(id) ON DELETE SET NULL,
    FOREIGN KEY (claim_id) REFERENCES claims(id) ON DELETE SET NULL,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS notifications (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    title       VARCHAR(255) NOT NULL,
    message     TEXT NOT NULL,
    type        ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read     TINYINT(1) DEFAULT 0,
    link        VARCHAR(500) DEFAULT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: audit_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS audit_logs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED DEFAULT NULL,
    action      VARCHAR(100) NOT NULL,
    module      VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address  VARCHAR(45),
    user_agent  VARCHAR(255),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: sms_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS sms_logs (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipient     VARCHAR(20) NOT NULL,
    message       TEXT NOT NULL,
    status        ENUM('sent', 'failed', 'simulated') NOT NULL DEFAULT 'sent',
    http_code     INT UNSIGNED DEFAULT NULL,
    response_body TEXT DEFAULT NULL,
    error_message VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- INDEXES (performance optimization)
-- ============================================================
CREATE INDEX idx_policies_user     ON policies(user_id);
CREATE INDEX idx_policies_status   ON policies(status);
CREATE INDEX idx_claims_policy     ON claims(policy_id);
CREATE INDEX idx_claims_status     ON claims(status);
CREATE INDEX idx_payments_user     ON payments(user_id);
CREATE INDEX idx_notifications_user ON notifications(user_id, is_read);
CREATE INDEX idx_audit_user        ON audit_logs(user_id);
CREATE INDEX idx_farms_user        ON farms(user_id);
CREATE INDEX idx_sms_recipient     ON sms_logs(recipient);
CREATE INDEX idx_sms_status        ON sms_logs(status);
CREATE INDEX idx_sms_created_at    ON sms_logs(created_at);
