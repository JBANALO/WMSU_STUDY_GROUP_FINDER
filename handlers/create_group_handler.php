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

$group_name = trim($_POST['group_name'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$description = trim($_POST['description'] ?? '');
$user_id = $_SESSION['user_id'];

if (empty($group_name) || empty($subject)) {
    $_SESSION['error'] = "Group name and subject are required!";
    header("Location: ../index.php?page=dashboard");
    exit();
}

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        INSERT INTO study_groups (group_name, subject, description, creator_id, status)
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$group_name, $subject, $description, $user_id]);
    $group_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("
        INSERT INTO group_members (group_id, user_id, role)
        VALUES (?, ?, 'creator')
    ");
    $stmt->execute([$group_id, $user_id]);
    
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $creator = $stmt->fetch();
    $creator_name = $creator['first_name'] . ' ' . $creator['last_name'];
    
    notifyAdminsNewGroup($pdo, $group_id, $group_name, $creator_name);
    
    $pdo->commit();
    
    $_SESSION['success'] = "Study group created! Waiting for admin approval.";
    header("Location: ../index.php?page=dashboard");
    exit();
    
} catch(PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Failed to create study group: " . $e->getMessage();
    header("Location: ../index.php?page=dashboard");
    exit();
}
?>