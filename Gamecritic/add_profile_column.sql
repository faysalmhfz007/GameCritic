-- Add profile_picture column to users table
USE gamecritic;

-- First, let's see the current table structure
DESCRIBE users;

-- Add the profile_picture column
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;

-- Verify the column was added
DESCRIBE users;
