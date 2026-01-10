<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

ob_start();

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    
    if (!$group_id || !$user_id) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['announcements' => [], 'error' => 'Invalid parameters']);
        exit;
    }
    
    require_once dirname(__DIR__) . '/config/database.php';
    
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    $is_member = (bool)$stmt->fetch();
    
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'announcements'");
    
    if ($tableCheck->rowCount() == 0) {
        ob_end_clean();
        http_response_code(200);
        echo json_encode(['announcements' => [], 'is_member' => $is_member]);
        exit;
    }
    
    $stmt = $pdo->prepare("
        SELECT 
            a.id, a.title, a.content, a.attachment, a.announcement_type, a.due_date,
            a.created_at, a.updated_at,
            u.first_name, u.last_name,
            CONCAT(u.first_name, ' ', u.last_name) as user_name,
            DATE_FORMAT(a.created_at, '%b %d, %Y') as date_format,
            DATE_FORMAT(a.updated_at, '%b %d, %Y') as edited_format,
            a.user_id
        FROM announcements a
        JOIN users u ON a.user_id = u.id
        WHERE a.group_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$group_id]);
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode(['announcements' => $announcements, 'is_member' => $is_member, 'success' => true]);
    
} catch(PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['announcements' => [], 'error' => 'Database error: ' . $e->getMessage()]);
} catch(Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['announcements' => [], 'error' => 'Server error']);
}
exit;