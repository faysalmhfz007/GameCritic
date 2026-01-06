-- Add profile_picture column to users table
USE gamecritic;

ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER password;

-- Update existing users to have a default profile picture (optional)
-- UPDATE users SET profile_picture = NULL WHERE profile_picture IS NULL;
