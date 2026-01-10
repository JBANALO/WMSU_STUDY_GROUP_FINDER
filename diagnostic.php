<?php
require_once 'config/database.php';

echo "<h2>Database Diagnostic</h2>";

try {
    // Check users table
    echo "<h3>Users Table:</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='10'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td></tr>";
    }
    echo "</table>";
    
    // Count users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total users: " . $result['count'] . "</p>";
    
    // List users
    echo "<h4>User List:</h4><table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Email</th><th>Username</th><th>Status</th></tr>";
    $stmt = $pdo->query("SELECT id, email, username, status FROM users");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $user) {
        echo "<tr><td>{$user['id']}</td><td>{$user['email']}</td><td>{$user['username']}</td><td>{$user['status']}</td></tr>";
    }
    echo "</table>";
    
    // Check study_groups table
    echo "<h3>Study Groups Table:</h3>";
    $stmt = $pdo->query("DESCRIBE study_groups");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='10'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td></tr>";
    }
    echo "</table>";
    
    // Count study groups
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM study_groups");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total study groups: " . $result['count'] . "</p>";
    
    // List study groups
    echo "<h4>Study Groups List:</h4><table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Creator</th><th>Status</th></tr>";
    $stmt = $pdo->query("SELECT id, group_name, creator_id, status FROM study_groups");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $group) {
        echo "<tr><td>{$group['id']}</td><td>{$group['group_name']}</td><td>{$group['creator_id']}</td><td>{$group['status']}</td></tr>";
    }
    echo "</table>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
