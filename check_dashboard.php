<?php
session_start();

echo "<h2>Current Session Info</h2>";
echo "<p><strong>User ID:</strong> " . ($_SESSION['user_id'] ?? 'NOT LOGGED IN') . "</p>";
echo "<p><strong>Username:</strong> " . ($_SESSION['username'] ?? 'N/A') . "</p>";
echo "<p><strong>Full Name:</strong> " . ($_SESSION['full_name'] ?? 'N/A') . "</p>";

require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    echo "<h2>Your Study Groups</h2>";
    
    // Pending groups (created by you, waiting approval)
    echo "<h3>Pending Groups (Created by you):</h3>";
    $stmt = $pdo->prepare("SELECT id, group_name, subject, status, created_at FROM study_groups WHERE creator_id = ? AND status = 'pending'");
    $stmt->execute([$user_id]);
    $pending = $stmt->fetchAll();
    if ($pending) {
        echo "<ul>";
        foreach ($pending as $group) {
            echo "<li>" . $group['group_name'] . " (" . $group['subject'] . ") - Status: " . $group['status'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No pending groups</p>";
    }
    
    // Groups they joined
    echo "<h3>Groups You Joined (Member):</h3>";
    $stmt = $pdo->prepare("
        SELECT sg.id, sg.group_name, sg.subject, sg.status, gm.role 
        FROM study_groups sg
        JOIN group_members gm ON sg.id = gm.group_id
        WHERE gm.user_id = ? AND sg.status = 'approved'
    ");
    $stmt->execute([$user_id]);
    $joined = $stmt->fetchAll();
    if ($joined) {
        echo "<ul>";
        foreach ($joined as $group) {
            echo "<li>" . $group['group_name'] . " (" . $group['subject'] . ") - Role: " . $group['role'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No joined groups</p>";
    }
    
    // Available groups to join
    echo "<h3>Available Groups (You can join):</h3>";
    $stmt = $pdo->prepare("
        SELECT sg.id, sg.group_name, sg.subject, sg.status
        FROM study_groups sg
        WHERE sg.status = 'approved' 
        AND sg.id NOT IN (SELECT group_id FROM group_members WHERE user_id = ?)
    ");
    $stmt->execute([$user_id]);
    $available = $stmt->fetchAll();
    if ($available) {
        echo "<ul>";
        foreach ($available as $group) {
            echo "<li>" . $group['group_name'] . " (" . $group['subject'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No available groups to join</p>";
    }
} else {
    echo "<p><strong>You are not logged in!</strong></p>";
}

echo "<p><a href='index.php?page=dashboard'>&larr; Go to Dashboard</a></p>";
?>
