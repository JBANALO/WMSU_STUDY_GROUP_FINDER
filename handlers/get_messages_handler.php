<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    
    if (!$group_id || !$user_id) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['messages' => [], 'is_member' => false, 'error' => 'Invalid params']);
        exit;
    }
    
    require_once dirname(__DIR__) . '/config/database.php';
    
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    $is_member = (bool)$stmt->fetch();
    
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'group_messages'");
    
    if ($tableCheck->rowCount() == 0) {
        ob_end_clean();
        http_response_code(200);
        echo json_encode(['messages' => [], 'is_member' => $is_member]);
        exit;
    }
    
    try {
        $pdo->exec("ALTER TABLE group_messages ADD COLUMN attachment VARCHAR(255)");
    } catch(Exception $e) {
    }
    
    $stmt = $pdo->prepare("
        SELECT 
            gm.id, gm.user_id, gm.message, gm.attachment, gm.message_type,
            u.first_name, u.last_name,
            CONCAT(u.first_name, ' ', u.last_name) as user_name,
            DATE_FORMAT(gm.created_at, '%H:%i') as time_format
        FROM group_messages gm
        JOIN users u ON gm.user_id = u.id
        WHERE gm.group_id = ?
        ORDER BY gm.id ASC
    ");
    $stmt->execute([$group_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode(['messages' => $messages, 'is_member' => $is_member, 'success' => true]);
    
} catch(PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['messages' => [], 'is_member' => false, 'error' => 'Database error']);
} catch(Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['messages' => [], 'is_member' => false, 'error' => 'Server error']);
}
exit;