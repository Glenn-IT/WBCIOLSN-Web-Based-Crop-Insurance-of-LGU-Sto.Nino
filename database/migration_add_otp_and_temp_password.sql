-- ============================================================
-- Migration: Add OTP verification table + forced-password-change flag
-- Run this once against your database
-- ============================================================

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS must_change_password TINYINT(1) NOT NULL DEFAULT 0 AFTER password;

CREATE TABLE IF NOT EXISTS otp_verifications (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(191) NOT NULL,
    otp_hash    VARCHAR(255) NOT NULL,
    purpose     VARCHAR(50)  NOT NULL DEFAULT 'admin_create_user',
    attempts    TINYINT(1)   NOT NULL DEFAULT 0,
    used        TINYINT(1)   NOT NULL DEFAULT 0,
    expires_at  DATETIME     NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_purpose (email, purpose)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
