-- ============================================================
-- Migration: add login-lockout and security-question columns
-- Date: 2026-07-13
-- ============================================================

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS failed_attempts INT UNSIGNED NOT NULL DEFAULT 0 AFTER reset_expires,
    ADD COLUMN IF NOT EXISTS locked_until DATETIME DEFAULT NULL AFTER failed_attempts,
    ADD COLUMN IF NOT EXISTS security_question VARCHAR(255) DEFAULT NULL AFTER locked_until,
    ADD COLUMN IF NOT EXISTS security_answer_hash VARCHAR(255) DEFAULT NULL AFTER security_question;
