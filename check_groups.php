<?php
require_once 'config/database.php';

echo "<h2>Study Groups Diagnostic</h2>";

try {
    // Get all study groups
    echo "<h3>All Study Groups in Database:</h3>";
    $stmt = $pdo->query("SELECT sg.id, sg.group_name, sg.subject, sg.status, u.first_name, u.last_name FROM study_groups sg JOIN users u ON sg.creator_id = u.id ORDER BY sg.created_at DESC");
    $groups = $stmt->fetchAll();
    
    if ($groups) {
        echo "<table border='1' cellpadding='10' style='width: 100%; margin-bottom: 20px;'>";
        echo "<tr><th>ID</th><th>Group Name</th><th>Subject</th><th>Creator</th><th>Status</th><th>Created At</th></tr>";
        foreach ($groups as $group) {
            echo "<tr>";
            echo "<td>{$group['id']}</td>";
            echo "<td>{$group['group_name']}</td>";
            echo "<td>{$group['subject']}</td>";
            echo "<td>{$group['first_name']} {$group['last_name']}</td>";
            echo "<td><strong style='color: " . ($group['status'] === 'pending' ? 'orange' : ($group['status'] === 'approved' ? 'green' : 'red')) . "'>{$group['status']}</strong></td>";
            echo "<td>" . $group['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No groups found</p>";
    }
    
    // Check pending groups query (used in admin dashboard)
    echo "<h3>Pending Groups (as seen in admin dashboard):</h3>";
    $stmt = $pdo->prepare("
        SELECT sg.*, u.first_name, u.last_name 
        FROM study_groups sg
        JOIN users u ON sg.creator_id = u.id
        WHERE sg.status = 'pending'
        ORDER BY sg.created_at DESC
    ");
    $stmt->execute();
    $pending = $stmt->fetchAll();
    
    if ($pending) {
        echo "<p style='color: green;'>Found " . count($pending) . " pending group(s):</p>";
        foreach ($pending as $group) {
            echo "<p>- " . $group['group_name'] . " (by " . $group['first_name'] . " " . $group['last_name'] . ")</p>";
        }
    } else {
        echo "<p style='color: orange;'>No pending groups found</p>";
    }
    
    echo "<p><a href='index.php?page=admin_dashboard' style='color: #8B0000; text-decoration: none;'>&larr; Go to Admin Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'><strong>Error: " . $e->getMessage() . "</strong></p>";
}
?>
