-- ============================================================
-- Migration: Add missing application fields to farms & policies
-- Run this once against your database
-- ============================================================

-- Add extra fields to farms table
ALTER TABLE farms
    ADD COLUMN IF NOT EXISTS application_type  VARCHAR(50)  DEFAULT NULL AFTER farm_name,
    ADD COLUMN IF NOT EXISTS farmer_category   VARCHAR(100) DEFAULT NULL AFTER application_type,
    ADD COLUMN IF NOT EXISTS tenurial_status   VARCHAR(50)  DEFAULT NULL AFTER irrigation,
    ADD COLUMN IF NOT EXISTS planting_method   VARCHAR(50)  DEFAULT NULL AFTER tenurial_status,
    ADD COLUMN IF NOT EXISTS planting_date     DATE         DEFAULT NULL AFTER planting_method,
    ADD COLUMN IF NOT EXISTS harvest_date      DATE         DEFAULT NULL AFTER planting_date;

-- Add damage report fields to policies table
ALTER TABLE policies
    ADD COLUMN IF NOT EXISTS cause_of_damage     VARCHAR(100) DEFAULT NULL AFTER remarks,
    ADD COLUMN IF NOT EXISTS percent_damage      DECIMAL(5,2) DEFAULT NULL AFTER cause_of_damage,
    ADD COLUMN IF NOT EXISTS financial_damage    DECIMAL(15,2) DEFAULT NULL AFTER percent_damage,
    ADD COLUMN IF NOT EXISTS damage_description  TEXT         DEFAULT NULL AFTER financial_damage,
    ADD COLUMN IF NOT EXISTS date_of_loss        DATE         DEFAULT NULL AFTER damage_description;
