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
        $pdo->exec("ALTER TABLE users ADD COLUMN middle_name VARCHAR(255) AFTER first_name");
        echo "✓ Column 'middle_name' added successfully!\n";
    } else {
        echo "✓ Column 'middle_name' already exists. Skipping.\n";
    }
    
    // Verify
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "\nUsers table columns:\n";
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}

echo "\n=== Fix Complete! ===\n";
?>
