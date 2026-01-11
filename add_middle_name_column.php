#!/usr/bin/env php
<?php
/**
 * Add middle_name column to users table
 */

require_once 'config/database.php';

echo "=== Adding middle_name column to users table ===\n\n";

try {
    // Check if middle_name column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'middle_name'");
    $column_exists = $stmt->fetch();
    
    if ($column_exists) {
        echo "✓ Column 'middle_name' already exists in users table.\n";
    } else {
        echo "Adding 'middle_name' column to users table...";
        $pdo->exec("ALTER TABLE users ADD COLUMN middle_name VARCHAR(255) AFTER first_name");
        echo " ✓ Done\n";
    }
    
    echo "\n=== Update Complete! ===\n";
    
} catch(PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
