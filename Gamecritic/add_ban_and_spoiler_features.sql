-- Add ban and spoiler features
USE gamecritic;

-- Add banned columns to users table (run these one at a time if column exists error occurs)
ALTER TABLE users ADD COLUMN banned TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN banned_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE users ADD COLUMN banned_reason TEXT NULL DEFAULT NULL;

-- Create user_hidden_games table for spoiler/hide feature
CREATE TABLE IF NOT EXISTS user_hidden_games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_game (user_id, game_id),
    INDEX idx_user_id (user_id),
    INDEX idx_game_id (game_id)
);
