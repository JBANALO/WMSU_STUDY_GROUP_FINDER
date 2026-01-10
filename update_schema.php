<?php
require_once 'config/database.php';

echo "<h2>Database Schema Update</h2>";

try {
    // Check if decline_reason columns exist, if not add them
    
    // Check users table
    $stmt = $pdo->query("DESCRIBE users");
    $columns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    
    if (!in_array('decline_reason', $columns)) {
        echo "Adding decline_reason column to users table...<br>";
        $pdo->exec("ALTER TABLE users ADD COLUMN decline_reason TEXT NULL");
        echo "✓ Column added to users table<br>";
    } else {
        echo "decline_reason column already exists in users table<br>";
    }
    
    // Check study_groups table
    $stmt = $pdo->query("DESCRIBE study_groups");
    $columns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    
    if (!in_array('decline_reason', $columns)) {
        echo "Adding decline_reason column to study_groups table...<br>";
        $pdo->exec("ALTER TABLE study_groups ADD COLUMN decline_reason TEXT NULL");
        echo "✓ Column added to study_groups table<br>";
    } else {
        echo "decline_reason column already exists in study_groups table<br>";
    }
    
    echo "<br><p style='color: green;'><strong>✓ Database schema updated successfully!</strong></p>";
    echo "<p><a href='index.php?page=admin_dashboard' style='color: #8B0000; text-decoration: none;'>&larr; Go to Admin Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
