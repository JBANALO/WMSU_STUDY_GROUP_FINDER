<?php
require_once 'config/database.php';

try {
    // Check if location column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM meetings LIKE 'location'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE meetings ADD COLUMN location VARCHAR(255) NULL");
        echo "✓ Added location column to meetings table<br>";
    } else {
        echo "✓ location column already exists<br>";
    }
    
    // Check if is_online column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM meetings LIKE 'is_online'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE meetings ADD COLUMN is_online BOOLEAN DEFAULT 0");
        echo "✓ Added is_online column to meetings table<br>";
    } else {
        echo "✓ is_online column already exists<br>";
    }
    
    // Check if created_by column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM meetings LIKE 'created_by'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE meetings ADD COLUMN created_by INT NOT NULL, ADD FOREIGN KEY (created_by) REFERENCES users(id)");
        echo "✓ Added created_by column to meetings table<br>";
    } else {
        echo "✓ created_by column already exists<br>";
    }
    
    echo "<br>Database schema updated successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
