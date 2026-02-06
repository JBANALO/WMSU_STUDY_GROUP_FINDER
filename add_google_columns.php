<?php
/**
 * Add Google Sign-In columns to users table
 * Run this once: php add_google_columns.php
 */

require_once 'config/database.php';

try {
    echo "Adding Google Sign-In columns to users table...\n\n";
    
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
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
