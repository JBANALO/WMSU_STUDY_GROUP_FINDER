<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$group_id = $_POST['group_id'] ?? null;

if (!$group_id) {
    echo json_encode(['success' => false, 'error' => 'Group ID required']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT id 
        FROM group_messages 
        WHERE group_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$group_id]);
    $last_message = $stmt->fetch();
    
    if ($last_message) {
        $stmt = $pdo->prepare("
            INSERT INTO user_last_seen (user_id, group_id, last_seen_message_id)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                last_seen_message_id = VALUES(last_seen_message_id),
                last_seen_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$_SESSION['user_id'], $group_id, $last_message['id']]);
    }
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>