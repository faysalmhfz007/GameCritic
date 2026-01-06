<?php
// Script to add profile_picture column to users table
require_once __DIR__ . '/app/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "🔍 Checking current table structure...\n";
    
    // Show current structure
    $result = $conn->query("DESCRIBE users");
    echo "\n📋 Current users table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']} {$row['Null']} {$row['Key']} {$row['Default']}\n";
    }
    
    // Check if profile_picture column exists
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    if ($result->num_rows > 0) {
        echo "\n✅ profile_picture column already exists!\n";
    } else {
        echo "\n❌ profile_picture column does NOT exist. Adding it now...\n";
        
        // Add the column
        $sql = "ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL";
        if ($conn->query($sql)) {
            echo "✅ profile_picture column added successfully!\n";
            
            // Show updated structure
            echo "\n📋 Updated users table structure:\n";
            $result = $conn->query("DESCRIBE users");
            while ($row = $result->fetch_assoc()) {
                echo "- {$row['Field']}: {$row['Type']} {$row['Null']} {$row['Key']} {$row['Default']}\n";
            }
        } else {
            echo "❌ Failed to add profile_picture column: " . $conn->error . "\n";
        }
    }
    
    // Test the upload directory
    $uploadDir = __DIR__ . '/public/uploads/profiles/';
    echo "\n📁 Upload directory check:\n";
    if (is_dir($uploadDir)) {
        echo "✅ Directory exists: {$uploadDir}\n";
        if (is_writable($uploadDir)) {
            echo "✅ Directory is writable\n";
        } else {
            echo "❌ Directory is NOT writable\n";
        }
    } else {
        echo "❌ Directory does not exist\n";
        // Try to create it
        if (mkdir($uploadDir, 0755, true)) {
            echo "✅ Created upload directory\n";
        } else {
            echo "❌ Failed to create upload directory\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
