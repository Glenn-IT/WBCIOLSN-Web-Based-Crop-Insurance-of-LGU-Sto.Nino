-- ============================================================
-- Migration: Add address and farmer_type to users table
-- Run this once against your database
-- ============================================================

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS address      VARCHAR(255) DEFAULT NULL AFTER phone,
    ADD COLUMN IF NOT EXISTS farmer_type  VARCHAR(100) DEFAULT NULL AFTER address;
