-- ============================================================
-- Migration: Add 'pending' status for farmer self-registration
-- New farmers must be approved by an admin before they can log in.
-- Run this once against your database
-- ============================================================

ALTER TABLE users
    MODIFY COLUMN status ENUM('pending', 'active', 'inactive', 'suspended')
    NOT NULL DEFAULT 'active';
