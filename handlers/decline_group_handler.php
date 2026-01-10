<?php
require_once '../config/database.php';
require_once '../includes/notification_helper.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?page=admin_dashboard");
    exit();
}

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php?page=login");
    exit();
}

$group_id = $_POST['group_id'] ?? null;

if (!$group_id) {
    $_SESSION['error'] = "Invalid group ID";
    header("Location: ../index.php?page=admin_dashboard");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT group_name, creator_id FROM study_groups WHERE id = ?");
    $stmt->execute([$group_id]);
    $group = $stmt->fetch();
    
    if (!$group) {
        $_SESSION['error'] = "Group not found";
        header("Location: ../index.php?page=admin_dashboard");
        exit();
    }
    
    $stmt = $pdo->prepare("UPDATE study_groups SET status = 'declined' WHERE id = ?");
    $stmt->execute([$group_id]);
    
    notifyUserGroupDeclined($pdo, $group['creator_id'], $group['group_name']);
    
    $_SESSION['success'] = "Study group declined.";
    header("Location: ../index.php?page=admin_dashboard");
    exit();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Failed to decline group: " . $e->getMessage();
    header("Location: ../index.php?page=admin_dashboard");
    exit();
}
?>