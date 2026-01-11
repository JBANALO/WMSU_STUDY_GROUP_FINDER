#!/usr/bin/env php
<?php
/**
 * Add related_id column to notifications table
 * Run this script to upgrade existing databases
 */

require_once 'config/database.php';

echo "=== Updating Notifications Table ===\n\n";

try {
    // Check if related_id column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM notifications LIKE 'related_id'");
    $column_exists = $stmt->fetch();
    
    if ($column_exists) {
        echo "✓ Column 'related_id' already exists in notifications table.\n";
    } else {
        echo "Adding 'related_id' column to notifications table...";
        $pdo->exec("ALTER TABLE notifications ADD COLUMN related_id INT AFTER message");
        echo " ✓ Done\n";
    }
    
    echo "\n=== Update Complete! ===\n";
    
} catch(PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
