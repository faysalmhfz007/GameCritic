<?php
/**
 * Comprehensive Database Setup Script
 * This script creates all required tables and columns for GameCritic features
 */

echo "========================================\n";
echo "GameCritic Database Setup Script\n";
echo "========================================\n\n";

// Include database config
require_once __DIR__ . '/app/config/database.php';

// Create database connection
$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Failed to connect to database!\n");
}

echo "✓ Database connected successfully\n\n";

$errors = [];
$success = [];

// Helper function to check if column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $result && $result->num_rows > 0;
}

// Helper function to check if table exists
function tableExists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '{$table}'");
    return $result && $result->num_rows > 0;
}

// 1. Add banned columns to users table
echo "1. Checking users table columns...\n";
$columnsToAdd = [
    'banned' => "ALTER TABLE users ADD COLUMN banned TINYINT(1) DEFAULT 0",
    'banned_at' => "ALTER TABLE users ADD COLUMN banned_at TIMESTAMP NULL DEFAULT NULL",
    'banned_reason' => "ALTER TABLE users ADD COLUMN banned_reason TEXT NULL DEFAULT NULL"
];

foreach ($columnsToAdd as $column => $sql) {
    if (!columnExists($conn, 'users', $column)) {
        if ($conn->query($sql)) {
            $success[] = "Added column '{$column}' to users table";
            echo "  ✓ Added column '{$column}'\n";
        } else {
            $errors[] = "Failed to add column '{$column}': " . $conn->error;
            echo "  ✗ Failed to add column '{$column}': " . $conn->error . "\n";
        }
    } else {
        echo "  ✓ Column '{$column}' already exists\n";
    }
}

echo "\n";

// 2. Create social features tables
echo "2. Checking social features tables...\n";

// Friend requests table
if (!tableExists($conn, 'friend_requests')) {
    $sql = "CREATE TABLE friend_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_request (sender_id, receiver_id),
        INDEX idx_receiver_status (receiver_id, status),
        INDEX idx_sender_status (sender_id, status)
    )";
    if ($conn->query($sql)) {
        $success[] = "Created table 'friend_requests'";
        echo "  ✓ Created table 'friend_requests'\n";
    } else {
        $errors[] = "Failed to create table 'friend_requests': " . $conn->error;
        echo "  ✗ Failed to create table 'friend_requests': " . $conn->error . "\n";
    }
} else {
    echo "  ✓ Table 'friend_requests' already exists\n";
}

// Friendships table
if (!tableExists($conn, 'friendships')) {
    $sql = "CREATE TABLE friendships (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user1_id INT NOT NULL,
        user2_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_friendship (user1_id, user2_id),
        INDEX idx_user1 (user1_id),
        INDEX idx_user2 (user2_id)
    )";
    if ($conn->query($sql)) {
        $success[] = "Created table 'friendships'";
        echo "  ✓ Created table 'friendships'\n";
    } else {
        $errors[] = "Failed to create table 'friendships': " . $conn->error;
        echo "  ✗ Failed to create table 'friendships': " . $conn->error . "\n";
    }
} else {
    echo "  ✓ Table 'friendships' already exists\n";
}

// Messages table
if (!tableExists($conn, 'messages')) {
    $sql = "CREATE TABLE messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_receiver_read (receiver_id, is_read),
        INDEX idx_sender_receiver (sender_id, receiver_id),
        INDEX idx_created_at (created_at)
    )";
    if ($conn->query($sql)) {
        $success[] = "Created table 'messages'";
        echo "  ✓ Created table 'messages'\n";
    } else {
        $errors[] = "Failed to create table 'messages': " . $conn->error;
        echo "  ✗ Failed to create table 'messages': " . $conn->error . "\n";
    }
} else {
    echo "  ✓ Table 'messages' already exists\n";
}

// Notifications table
if (!tableExists($conn, 'notifications')) {
    $sql = "CREATE TABLE notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type ENUM('friend_request', 'friend_accepted', 'message', 'system') DEFAULT 'system',
        message TEXT NOT NULL,
        related_id INT DEFAULT NULL,
        related_type VARCHAR(50) DEFAULT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_read (user_id, is_read),
        INDEX idx_user_created (user_id, created_at)
    )";
    if ($conn->query($sql)) {
        $success[] = "Created table 'notifications'";
        echo "  ✓ Created table 'notifications'\n";
    } else {
        $errors[] = "Failed to create table 'notifications': " . $conn->error;
        echo "  ✗ Failed to create table 'notifications': " . $conn->error . "\n";
    }
} else {
    echo "  ✓ Table 'notifications' already exists\n";
}

echo "\n";

// 3. Create user_hidden_games table
echo "3. Checking user_hidden_games table...\n";
if (!tableExists($conn, 'user_hidden_games')) {
    $sql = "CREATE TABLE user_hidden_games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        game_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_game (user_id, game_id),
        INDEX idx_user_id (user_id),
        INDEX idx_game_id (game_id)
    )";
    if ($conn->query($sql)) {
        $success[] = "Created table 'user_hidden_games'";
        echo "  ✓ Created table 'user_hidden_games'\n";
    } else {
        $errors[] = "Failed to create table 'user_hidden_games': " . $conn->error;
        echo "  ✗ Failed to create table 'user_hidden_games': " . $conn->error . "\n";
    }
} else {
    echo "  ✓ Table 'user_hidden_games' already exists\n";
}

echo "\n";

// Summary
echo "========================================\n";
echo "Setup Summary\n";
echo "========================================\n";
echo "Successful operations: " . count($success) . "\n";
echo "Errors: " . count($errors) . "\n\n";

if (count($errors) > 0) {
    echo "Errors encountered:\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
    echo "\n";
}

if (count($success) > 0) {
    echo "Successful operations:\n";
    foreach ($success as $msg) {
        echo "  ✓ {$msg}\n";
    }
    echo "\n";
}

echo "Setup completed!\n";
?>
