<?php
echo "Starting script...\n";

// Include database config
require_once __DIR__ . '/app/config/database.php';

echo "Database config loaded...\n";

// Create database connection
$db = new Database();
$conn = $db->getConnection();

echo "Database connected...\n";

// Check current structure
$result = $conn->query("DESCRIBE users");
echo "Current table structure:\n";
while ($row = $result->fetch_assoc()) {
    echo "- {$row['Field']}\n";
}

// Add profile_picture column
echo "\nAdding profile_picture column...\n";
$sql = "ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL";
if ($conn->query($sql)) {
    echo "Column added successfully!\n";
} else {
    echo "Error: " . $conn->error . "\n";
}

// Show updated structure
$result = $conn->query("DESCRIBE users");
echo "\nUpdated table structure:\n";
while ($row = $result->fetch_assoc()) {
    echo "- {$row['Field']}\n";
}

echo "Script completed!\n";
?>
