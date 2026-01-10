<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once '../config/database.php';

$group_id = $_POST['group_id'] ?? null;

if (!$group_id || !$_SESSION['user_id']) {
    $_SESSION['error'] = "Invalid request";
    header("Location: ../index.php?page=group_details&id=" . $group_id);
    exit();
}

try {
    // Check if preference exists
    $stmt = $pdo->prepare("SELECT id FROM notification_preferences WHERE user_id = ? AND group_id = ?");
    $stmt->execute([$_SESSION['user_id'], $group_id]);
    
    if ($stmt->fetch()) {
        // Update: toggle enabled
        $stmt = $pdo->prepare("UPDATE notification_preferences SET enabled = NOT enabled WHERE user_id = ? AND group_id = ?");
        $stmt->execute([$_SESSION['user_id'], $group_id]);
    } else {
        // Create new preference
        $stmt = $pdo->prepare("INSERT INTO notification_preferences (user_id, group_id, enabled) VALUES (?, ?, 1)");
        $stmt->execute([$_SESSION['user_id'], $group_id]);
    }
    
    $_SESSION['success'] = "Notification preference updated!";
} catch(PDOException $e) {
    $_SESSION['error'] = "Error updating notification: " . $e->getMessage();
}

header("Location: ../index.php?page=group_details&id=" . $group_id);
exit();
?>
