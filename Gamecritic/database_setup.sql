-- GameCritic Database Setup
USE gamecritic;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create games table
CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(100),
    platform VARCHAR(100),
    release_year INT,
    cover_image VARCHAR(255),
    description TEXT,
    review TEXT,
    pos_count INT DEFAULT 0,
    neg_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    overall_score DECIMAL(4,1) DEFAULT 0.0,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    user_id INT NOT NULL,
    rating DECIMAL(3,1) NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO users (username, email, phone, password) VALUES
('john_doe', 'john@example.com', '1234567890', 'password123'),
('jane_smith', 'jane@example.com', '0987654321', 'password456'),
('bob_wilson', 'bob@example.com', '5555555555', 'password789');

INSERT INTO games (title, genre, platform, release_year, cover_image, description, review) VALUES
('The Legend of Zelda: Breath of the Wild', 'Adventure', 'Nintendo Switch', 2017, '/images/zelda.jpg', 'An open-world adventure game featuring Link in a vast, beautiful world.', 'A masterpiece of open-world design with innovative gameplay mechanics.'),
('God of War', 'Action-Adventure', 'PlayStation 4', 2018, '/images/godofwar.jpg', 'Kratos returns in this epic Norse mythology adventure.', 'Stunning visuals and emotional storytelling make this a must-play action game.'),
('Elden Ring', 'Action RPG', 'Multi-platform', 2022, '/images/eldenring.jpg', 'FromSoftware\'s latest challenging action RPG in an open world.', 'A challenging yet rewarding experience that redefines the Souls-like genre.');

INSERT INTO admins (username, email, password) VALUES
('admin', 'admin@gamecritic.com', 'admin123');

