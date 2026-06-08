-- ============================================================
-- Migration: Add under_review status + verification columns
-- Run this once against your database
-- ============================================================

-- 1. Add 'under_review' to policies status ENUM
ALTER TABLE policies
    MODIFY COLUMN status ENUM('pending','under_review','active','expired','cancelled','rejected')
    NOT NULL DEFAULT 'pending';

-- 2. Add verification tracking columns to policies
ALTER TABLE policies
    ADD COLUMN IF NOT EXISTS farm_verification     ENUM('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending' AFTER date_of_loss,
    ADD COLUMN IF NOT EXISTS damage_verification   ENUM('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending' AFTER farm_verification,
    ADD COLUMN IF NOT EXISTS coverage_verification ENUM('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending' AFTER damage_verification;
