<?php
// Check what codes are in the database
require_once 'config/database.php';

echo "<h2>Password Reset Codes in Database</h2>";

try {
    $stmt = $pdo->query("
        SELECT id, user_id, email, code, expires_at, created_at, used 
        FROM password_reset_codes 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Email</th>
            <th>Code</th>
            <th>Expires At</th>
            <th>Created At</th>
            <th>Used</th>
            <th>Status</th>
          </tr>";
    
    while ($row = $stmt->fetch()) {
        $isExpired = strtotime($row['expires_at']) < time() ? 'EXPIRED' : 'VALID';
        $isUsed = $row['used'] == 1 ? 'USED' : 'UNUSED';
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['user_id']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td><strong>{$row['code']}</strong></td>";
        echo "<td>{$row['expires_at']}</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "<td>{$isUsed}</td>";
        echo "<td style='color: " . ($isExpired == 'EXPIRED' || $isUsed == 'USED' ? 'red' : 'green') . "'>{$isExpired} / {$isUsed}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<br><br><h3>Current time: " . date('Y-m-d H:i:s') . "</h3>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
