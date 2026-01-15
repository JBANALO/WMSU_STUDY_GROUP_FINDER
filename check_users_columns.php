<?php
// Quick script to check users table structure
require_once 'config/database.php';

try {
    $stmt = $pdo->query('DESCRIBE users');
    echo "<h3>Users Table Structure:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
