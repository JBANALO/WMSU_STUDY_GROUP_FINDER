<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

try {
    require_once dirname(__DIR__) . '/config/database.php';
    
    $announcement_id = isset($_POST['announcement_id']) ? intval($_POST['announcement_id']) : 0;
    $user_id = intval($_SESSION['user_id']);
    
    if (!$announcement_id) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing announcement ID']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT user_id, attachment FROM announcements WHERE id = ?");
    $stmt->execute([$announcement_id]);
    $announcement = $stmt->fetch();
    
    if (!$announcement) {
        ob_end_clean();
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Announcement not found']);
        exit;
    }
    
    if ($announcement['user_id'] != $user_id) {
        ob_end_clean();
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Not authorized']);
        exit;
    }
    
    if ($announcement['attachment']) {
        $filepath = dirname(__DIR__) . '/' . $announcement['attachment'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->execute([$announcement_id]);
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode(['success' => true]);
    
} catch(PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
} catch(Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
exit;