<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? null;
    $decline_reason = $_POST['decline_reason'] ?? '';
    
    if (!$user_id || !$action) {
        $_SESSION['error'] = "Invalid request";
        header("Location: ../index.php?page=admin_dashboard");
        exit();
    }
    
    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE users SET status = 'approved', decline_reason = NULL WHERE id = ?");
            $stmt->execute([$user_id]);
            
            // Create notification for approved user
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, related_id) VALUES (?, 'account', 'Account Approved', 'Your account has been approved! You can now login.', NULL)");
            $stmt->execute([$user_id]);
            
            $_SESSION['success'] = "User account approved!";
        } elseif ($action === 'decline') {
            $stmt = $pdo->prepare("UPDATE users SET status = 'declined', decline_reason = ? WHERE id = ?");
            $stmt->execute([$decline_reason ?: 'Account not approved', $user_id]);
            
            // Create notification for declined user
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, related_id) VALUES (?, 'account', 'Account Declined', ?, NULL)");
            $stmt->execute([$user_id, 'Your account was declined. Reason: ' . ($decline_reason ?: 'Account not approved')]);
            
            $_SESSION['success'] = "User account declined!";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: ../index.php?page=admin_dashboard");
    exit();
}
?>
