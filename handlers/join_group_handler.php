<?php
require_once '../config/database.php';
require_once '../includes/notification_helper.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?page=dashboard");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?page=login");
    exit();
}

$group_id = $_POST['group_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$group_id) {
    $_SESSION['error'] = "Invalid group ID";
    header("Location: ../index.php?page=dashboard");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = "You are already a member of this group";
        header("Location: ../index.php?page=group_details&id=" . $group_id);
        exit();
    }
    
    $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')");
    $stmt->execute([$group_id, $user_id]);
    
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $member_name = $user['first_name'] . ' ' . $user['last_name'];
    
    notifyGroupNewMember($pdo, $group_id, $member_name);
    
    $stmt = $pdo->prepare("
        INSERT INTO group_messages (group_id, user_id, message, message_type)
        VALUES (?, ?, ?, 'system')
    ");
    $stmt->execute([
        $group_id,
        $user_id,
        "<strong>{$member_name}</strong> joined the group"
    ]);
    
    $_SESSION['success'] = "Successfully joined the group!";
    header("Location: ../index.php?page=group_details&id=" . $group_id);
    exit();
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Failed to join group: " . $e->getMessage();
    header("Location: ../index.php?page=dashboard");
    exit();
}
?>