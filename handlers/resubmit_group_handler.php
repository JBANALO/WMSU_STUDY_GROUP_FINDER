<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?page=login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?page=dashboard");
    exit();
}

$group_id = $_POST['group_id'] ?? null;
$group_name = trim($_POST['group_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$subject = trim($_POST['subject'] ?? '');

if (!$group_id || !$group_name || !$description || !$subject) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../index.php?page=dashboard");
    exit();
}

try {
    // Check if the group belongs to the current user and is declined
    $stmt = $pdo->prepare("SELECT * FROM study_groups WHERE id = ? AND creator_id = ? AND status = 'declined'");
    $stmt->execute([$group_id, $_SESSION['user_id']]);
    $group = $stmt->fetch();

    if (!$group) {
        $_SESSION['error'] = "Group not found or you don't have permission to edit it.";
        header("Location: ../index.php?page=dashboard");
        exit();
    }

    // Update the group and set status back to pending
    $stmt = $pdo->prepare("UPDATE study_groups SET group_name = ?, description = ?, subject = ?, status = 'pending', decline_reason = NULL WHERE id = ?");
    $stmt->execute([$group_name, $description, $subject, $group_id]);

    $_SESSION['success'] = "Group has been updated and resubmitted for approval!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error updating group: " . $e->getMessage();
}

header("Location: ../index.php?page=dashboard");
exit();
