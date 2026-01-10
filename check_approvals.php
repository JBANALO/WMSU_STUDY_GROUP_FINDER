<?php
require_once 'config/database.php';

echo "<h2>Admin Dashboard Diagnostic</h2>";

try {
    // Check users and their status
    echo "<h3>All Users:</h3>";
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, status FROM users");
    $users = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='10' style='width: 100%; margin-bottom: 20px;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['first_name']} {$user['last_name']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td><strong style='color: " . ($user['status'] === 'pending' ? 'orange' : ($user['status'] === 'approved' ? 'green' : 'red')) . "'>{$user['status']}</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count pending users
    echo "<h3>Pending Users Count:</h3>";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'pending'");
    $stmt->execute();
    $pending_count = $stmt->fetch()['count'];
    echo "<p><strong>" . $pending_count . "</strong> pending users</p>";
    
    // Count approved users
    echo "<h3>Approved Users Count:</h3>";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'approved'");
    $stmt->execute();
    $approved_count = $stmt->fetch()['count'];
    echo "<p><strong>" . $approved_count . "</strong> approved users</p>";
    
    echo "<p><a href='index.php?page=admin_dashboard' style='color: #8B0000; text-decoration: none;'>&larr; Go to Admin Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'><strong>Error: " . $e->getMessage() . "</strong></p>";
}
?>
