<?php
// Temporary migration runner - DELETE after running once
// Access via: yoursite.com/run_migration.php

// Simple password protection
$password = 'migrate123';
if (!isset($_GET['pw']) || $_GET['pw'] !== $password) {
    die('Access denied. Use: run_migration.php?pw=migrate123');
}

require_once 'config/database.php';

echo "<h2>Adding Google Sign-In columns to users table...</h2>";
echo "<pre>";

try {
    // Check if columns already exist
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_id'");
    if ($stmt->rowCount() > 0) {
        echo "✓ google_id column already exists\n";
    } else {
        $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL UNIQUE AFTER email");
        echo "✓ Added google_id column\n";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'auth_provider'");
    if ($stmt->rowCount() > 0) {
        echo "✓ auth_provider column already exists\n";
    } else {
        $pdo->exec("ALTER TABLE users ADD COLUMN auth_provider VARCHAR(50) DEFAULT 'local' AFTER google_id");
        echo "✓ Added auth_provider column\n";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    if ($stmt->rowCount() > 0) {
        echo "✓ profile_picture column already exists\n";
    } else {
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_picture TEXT NULL AFTER auth_provider");
        echo "✓ Added profile_picture column\n";
    }
    
    echo "\n✅ Database migration completed successfully!\n";
    echo "\nYou can now use Google Sign-In.\n";
    echo "\n<strong style='color: red;'>IMPORTANT: Delete this file (run_migration.php) after running!</strong>\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
