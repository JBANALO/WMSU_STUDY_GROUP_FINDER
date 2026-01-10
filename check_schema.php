<?php
require_once 'config/database.php';

// Check database schema
$stmt = $pdo->query("DESCRIBE users");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Users Table Schema:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
foreach ($columns as $col) {
    echo "<tr>";
    echo "<td>" . $col['Field'] . "</td>";
    echo "<td>" . $col['Type'] . "</td>";
    echo "<td>" . $col['Null'] . "</td>";
    echo "<td>" . $col['Key'] . "</td>";
    echo "<td>" . ($col['Default'] ?? '-') . "</td>";
    echo "<td>" . ($col['Extra'] ?? '-') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check for admin user
echo "<h2>Check if Admin User Exists:</h2>";
$stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name FROM users WHERE email LIKE '%admin%' OR username LIKE '%admin%'");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($admins)) {
    echo "<p>No admin users found</p>";
} else {
    foreach ($admins as $admin) {
        echo "<p>Admin: {$admin['username']} ({$admin['email']}) - {$admin['first_name']} {$admin['last_name']}</p>";
    }
}
?>
