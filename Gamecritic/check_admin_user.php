<?php
require_once 'app/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "<h3>Checking Users Table:</h3>";
    $result = $conn->query('SELECT id, username, email, is_admin FROM users');
    if ($result) {
        while ($user = $result->fetch_assoc()) {
            echo "ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, is_admin: " . ($user['is_admin'] ?? 'NULL') . "<br>";
        }
    } else {
        echo "Error querying users table: " . $conn->error . "<br>";
    }
    
    echo "<br><h3>Checking Admins Table:</h3>";
    $result = $conn->query('SELECT id, username, email FROM admins');
    if ($result) {
        while ($admin = $result->fetch_assoc()) {
            echo "ID: {$admin['id']}, Username: {$admin['username']}, Email: {$admin['email']}<br>";
        }
    } else {
        echo "Error querying admins table: " . $conn->error . "<br>";
    }
    
    echo "<br><h3>Table Structure:</h3>";
    $result = $conn->query('DESCRIBE users');
    if ($result) {
        echo "Users table columns:<br>";
        while ($col = $result->fetch_assoc()) {
            echo "- {$col['Field']} ({$col['Type']})<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>

