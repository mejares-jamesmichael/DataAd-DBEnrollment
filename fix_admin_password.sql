-- Fix Admin Password
-- This updates the admin password to: admin123
-- Run this SQL in phpMyAdmin if login is failing

USE dbenrollment;

-- Update admin password with correct bcrypt hash for "admin123"
UPDATE `tbl_users` 
SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE `username` = 'admin';

-- Verify the update
SELECT user_id, username, role, email, full_name, is_active 
FROM `tbl_users` 
WHERE `username` = 'admin';
