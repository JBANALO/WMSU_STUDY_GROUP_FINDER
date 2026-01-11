<?php
require_once 'config/database.php';

echo "<h2>Add Google ID Column to Users Table</h2>";

try {
    // Add google_id column to users table
    $pdo->exec("
        ALTER TABLE users 
        ADD COLUMN google_id VARCHAR(255) UNIQUE NULL AFTER email
    ");
    
    echo "<p style='color: green;'><strong>✓ google_id column added successfully!</strong></p>";
    echo "<p>Users can now sign in with Google accounts.</p>";
    echo "<br><p><a href='index.php?page=login' style='color: #8B0000; text-decoration: none;'>&larr; Go to Login</a></p>";
    
} catch(PDOException $e) {
    if ($e->getCode() == '42S21' || strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "<p style='color: orange;'><strong>⚠ Column 'google_id' already exists!</strong></p>";
        echo "<p>Database schema is up to date.</p>";
    } else {
        echo "<p style='color: red;'><strong>Error: " . $e->getMessage() . "</strong></p>";
    }
}
?>
