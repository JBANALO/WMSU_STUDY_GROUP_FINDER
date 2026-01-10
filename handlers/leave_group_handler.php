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
    // Check if user is member
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $_SESSION['user_id']]);
    
    if ($stmt->fetch()) {
        // Get user name
        $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        $user_name = $user['first_name'] . ' ' . $user['last_name'];
        
        // Add system message FIRST
        $stmt = $pdo->prepare("
            INSERT INTO group_messages (group_id, user_id, message, message_type) 
            VALUES (?, ?, ?, 'system')
        ");
        $stmt->execute([$group_id, $_SESSION['user_id'], $user_name . ' left the group']);
        
        // Then delete from group_members
        $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->execute([$group_id, $_SESSION['user_id']]);
        
        $_SESSION['success'] = "You have left the group";
    } else {
        $_SESSION['error'] = "You are not a member of this group";
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Error leaving group: " . $e->getMessage();
}

// Redirect to dashboard after a short delay to allow the message to be seen
header("Location: ../index.php?page=dashboard");
exit();
?>
