<?php
// Quick fix: Add middle_name column if it doesn't exist
require_once 'config/database.php';

echo "=== Adding middle_name column to users table ===\n";

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'middle_name'");
    $column_exists = $stmt->fetch();
    
    if (!$column_exists) {
        echo "Column 'middle_name' not found. Adding it now...\n";
        // Use ALTER TABLE without IF NOT EXISTS for MySQL compatibility
        $pdo->exec("ALTER TABLE users ADD COLUMN middle_name VARCHAR(255) NULL AFTER first_name");
        echo "✓ Column 'middle_name' added successfully!\n";
    } else {
        echo "✓ Column 'middle_name' already exists. Skipping.\n";
    }
    
    // Verify - show all columns
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\n✓ Users table structure:\n";
    foreach ($columns as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
    
} catch (PDOException $e) {
    echo "⚠ Error: " . $e->getMessage() . "\n";
    // If column already exists, that's fine
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✓ Column already exists (duplicate column error is OK)\n";
    } else {
        die("✗ Fatal error occurred\n");
    }
}

echo "\n=== Fix Complete! ===\n";
?>
