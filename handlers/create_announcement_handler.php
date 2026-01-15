<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

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
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $announcement_type = isset($_POST['announcement_type']) ? $_POST['announcement_type'] : 'announcement';
    $due_date = (isset($_POST['due_date']) && trim($_POST['due_date']) !== '') ? $_POST['due_date'] : null;
    $user_id = intval($_SESSION['user_id']);
    
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['attachment'];
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 
                         'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                         'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        
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
        
        $cloudinary_success = false;
        
        // Try Cloudinary upload with full error protection
        try {
            // Load Cloudinary config
            if (file_exists(dirname(__DIR__) . '/config/cloudinary.php')) {
                require_once dirname(__DIR__) . '/config/cloudinary.php';
                
                if (function_exists('isCloudinaryConfigured') && isCloudinaryConfigured()) {
                    if (function_exists('initCloudinary') && initCloudinary()) {
                        // Check if Cloudinary class exists
                        if (class_exists('\\Cloudinary\\Uploader')) {
                            $upload_result = \Cloudinary\Uploader::upload($file['tmp_name'], [
                                'folder' => 'studyfinder/announcements',
                                'resource_type' => 'auto',
                                'use_filename' => true
                            ]);
                            
                            if (isset($upload_result['secure_url'])) {
                                $attachment = $upload_result['secure_url'];
                                $cloudinary_success = true;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Cloudinary upload error: ' . $e->getMessage());
            $cloudinary_success = false;
        } catch (Error $e) {
            error_log('Cloudinary fatal error: ' . $e->getMessage());
            $cloudinary_success = false;
        }
        
        // Fallback to local storage if Cloudinary failed or not available
        if (!$cloudinary_success) {
            try {
                $uploadDir = dirname(__DIR__) . '/uploads/announcements';
                
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0755, true);
                }
                
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('announcement_') . '_' . time() . '.' . $ext;
                $filepath = $uploadDir . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $attachment = 'uploads/announcements/' . $filename;
                } else {
                    ob_end_clean();
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
                    exit;
                }
            } catch (Exception $e) {
                ob_end_clean();
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Upload error: ' . $e->getMessage()]);
                exit;
            }
        }
    }
    
    if (!$group_id || !$title) {
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
        echo json_encode(['success' => false, 'error' => 'Not a member']);
        exit;
    }
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS announcements (
            id INT PRIMARY KEY AUTO_INCREMENT,
            group_id INT NOT NULL,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            attachment VARCHAR(500),
            announcement_type ENUM('announcement', 'assignment', 'material') DEFAULT 'announcement',
            due_date DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_group_id (group_id),
            INDEX idx_created_at (created_at)
        )
    ");
    
    $stmt = $pdo->prepare("
        INSERT INTO announcements (group_id, user_id, title, content, attachment, announcement_type, due_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$group_id, $user_id, $title, $content, $attachment, $announcement_type, $due_date]);
    
    ob_end_clean();
    http_response_code(200);
    echo json_encode(['success' => true, 'announcement_id' => $pdo->lastInsertId()]);
    
} catch(PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    error_log('Announcement creation error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch(Exception $e) {
    ob_end_clean();
    http_response_code(500);
    error_log('Announcement creation error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
exit;