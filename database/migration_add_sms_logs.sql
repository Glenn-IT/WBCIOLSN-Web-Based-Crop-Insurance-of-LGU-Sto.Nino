-- ============================================================
-- Migration: Add SMS Logs Table
-- Web-Based Crop Insurance System
-- ============================================================

USE crop_insurance_db;

CREATE TABLE IF NOT EXISTS sms_logs (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipient     VARCHAR(20) NOT NULL,
    message       TEXT NOT NULL,
    status        ENUM('sent', 'failed', 'simulated') NOT NULL DEFAULT 'sent',
    http_code     INT UNSIGNED DEFAULT NULL,
    response_body TEXT DEFAULT NULL,
    error_message VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sms_recipient (recipient),
    INDEX idx_sms_status (status),
    INDEX idx_sms_created_at (created_at)
) ENGINE=InnoDB;
