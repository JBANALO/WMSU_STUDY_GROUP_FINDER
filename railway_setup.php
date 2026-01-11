#!/usr/bin/env php
<?php
/**
 * Railway Database Setup Script
 * This script will create all necessary tables for the Study Group Finder
 */

echo "=== Railway Database Setup ===\n";

// Get database connection from environment variables
$host = getenv('DB_HOST') ?: 'mysql.railway.internal';
$dbname = getenv('DB_NAME') ?: 'railway';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$port = getenv('DB_PORT') ?: '3306';

echo "Connecting to database...\n";
echo "Host: $host\n";
echo "Database: $dbname\n";
echo "User: $username\n";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to database successfully!\n\n";
} catch(PDOException $e) {
    die("✗ Connection failed: " . $e->getMessage() . "\n");
}

// SQL statements for table creation
$tables = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(255) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        first_name VARCHAR(255),
        middle_name VARCHAR(255),
        last_name VARCHAR(255),
        password VARCHAR(255) NOT NULL,
        status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    'study_groups' => "CREATE TABLE IF NOT EXISTS study_groups (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_name VARCHAR(255) NOT NULL,
        description TEXT,
        subject VARCHAR(255),
        creator_id INT NOT NULL,
        status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
        decline_reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (creator_id) REFERENCES users(id)
    )",
    
    'group_members' => "CREATE TABLE IF NOT EXISTS group_members (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        role VARCHAR(50) DEFAULT 'member',
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES study_groups(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )",
    
    'group_messages' => "CREATE TABLE IF NOT EXISTS group_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        message TEXT,
        attachment VARCHAR(255),
        message_type VARCHAR(50) DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES study_groups(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )",
    
    'meetings' => "CREATE TABLE IF NOT EXISTS meetings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        meeting_date DATETIME NOT NULL,
        location VARCHAR(255),
        is_online BOOLEAN DEFAULT 1,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES study_groups(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    )",
    
    'notifications' => "CREATE TABLE IF NOT EXISTS notifications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        type VARCHAR(50),
        title VARCHAR(255),
        message TEXT,
        related_id INT,
        is_read BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )",
    
    'user_last_seen' => "CREATE TABLE IF NOT EXISTS user_last_seen (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        group_id INT NOT NULL,
        last_seen_message_id INT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (group_id) REFERENCES study_groups(id)
    )"
];

// Create tables
foreach ($tables as $tableName => $sql) {
    echo "Creating table: $tableName...";
    try {
        $pdo->exec($sql);
        echo " ✓ Done\n";
    } catch(PDOException $e) {
        echo " ✗ Failed: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Checking for missing columns ===\n";

// Ensure middle_name column exists in users table (for existing deployments)
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'middle_name'");
    if (!$stmt->fetch()) {
        echo "Adding middle_name column to users table...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN middle_name VARCHAR(255) NULL AFTER first_name");
        echo "✓ middle_name column added\n";
    } else {
        echo "✓ middle_name column already exists\n";
    }
} catch(PDOException $e) {
    echo "⚠ Could not check/add middle_name column: " . $e->getMessage() . "\n";
}

echo "\n=== Setup Complete! ===\n";
echo "All tables have been created successfully.\n";

// Exit with success code
exit(0);
