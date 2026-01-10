<?php
require_once 'config/database.php';

echo "<h2>All Study Groups in Database:</h2>";
$stmt = $pdo->prepare("SELECT sg.*, u.first_name, u.last_name FROM study_groups sg JOIN users u ON sg.creator_id = u.id ORDER BY sg.id DESC");
$stmt->execute();
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Creator</th><th>Status</th><th>Created At</th></tr>";
foreach ($groups as $g) {
    echo "<tr>";
    echo "<td>{$g['id']}</td>";
    echo "<td>{$g['group_name']}</td>";
    echo "<td>{$g['first_name']} {$g['last_name']}</td>";
    echo "<td><strong>{$g['status']}</strong></td>";
    echo "<td>{$g['created_at']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Current User Session:</h2>";
session_start();
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Logged in as: <strong>{$user['first_name']} {$user['last_name']}</strong> (ID: {$user['id']})</p>";
    
    echo "<h3>Pending Groups (created by this user):</h3>";
    $stmt = $pdo->prepare("SELECT * FROM study_groups WHERE creator_id = ? AND status = 'pending'");
    $stmt->execute([$_SESSION['user_id']]);
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($pending)) {
        echo "<p>None</p>";
    } else {
        foreach ($pending as $p) {
            echo "<p>- {$p['group_name']} (Status: {$p['status']})</p>";
        }
    }
} else {
    echo "<p style='color: red;'>Not logged in</p>";
}
?>
