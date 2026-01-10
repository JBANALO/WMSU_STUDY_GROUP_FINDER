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
    
    $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $user_id = intval($_SESSION['user_id']);
    
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['attachment'];
        
        $allowed_types = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        if (!in_array($file['type'], $allowed_types)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
            exit;
        }
        
        if ($file['size'] > 10 * 1024 * 1024) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'File too large (max 10MB)']);
            exit;
        }
        
        $uploadDir = dirname(__DIR__) . '/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('msg_') . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $attachment = 'uploads/' . $filename;
        } else {
            ob_end_clean();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
            exit;
        }
    }
    
    if (!$group_id || (!$message && !$attachment)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    
    if (!$stmt->fetch()) {
        ob_end_clean();
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'You are not a member of this group']);
        exit;
    }
    
    $checkTable = $pdo->query("SHOW TABLES LIKE 'group_messages'");
    if ($checkTable->rowCount() == 0) {
        $pdo->exec("
            CREATE TABLE group_messages (
                id INT PRIMARY KEY AUTO_INCREMENT,
                group_id INT NOT NULL,
                user_id INT NOT NULL,
                message TEXT NULL,
                attachment VARCHAR(500) NULL,
                message_type VARCHAR(50) DEFAULT 'message',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_group (group_id),
                INDEX idx_user (user_id),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } else {
        try {
            $pdo->exec("ALTER TABLE group_messages MODIFY message TEXT NULL");
            $pdo->exec("ALTER TABLE group_messages MODIFY attachment VARCHAR(500) NULL");
        } catch(Exception $e) {
        }
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO group_messages (group_id, user_id, message, attachment, message_type) 
        VALUES (?, ?, ?, ?, 'message')
    ");
    
    $insertMessage = $message ?: null;
    $stmt->execute([$group_id, $user_id, $insertMessage, $attachment]);
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode(['success' => true, 'message_id' => $pdo->lastInsertId()]);
    
} catch(PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch(Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
exit;