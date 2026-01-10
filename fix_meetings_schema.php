<?php
require_once 'config/database.php';

try {
    // Add description column if it doesn't exist
    $pdo->exec("ALTER TABLE meetings ADD COLUMN description TEXT AFTER title");
    echo "<p style='color: green; font-weight: bold;'>✓ Description column added successfully!</p>";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "<p style='color: orange;'>Column already exists</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>
