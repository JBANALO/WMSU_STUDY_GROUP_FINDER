<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?page=login");
    exit();
}

$group_id = $_GET['id'] ?? null;

if (!$group_id) {
    $_SESSION['error'] = "Invalid group ID.";
    header("Location: ../index.php?page=dashboard");
    exit();
}

try {
    // Check if the group belongs to the current user
    $stmt = $pdo->prepare("SELECT * FROM study_groups WHERE id = ? AND creator_id = ?");
    $stmt->execute([$group_id, $_SESSION['user_id']]);
    $group = $stmt->fetch();

    if (!$group) {
        $_SESSION['error'] = "Group not found or you don't have permission to delete it.";
        header("Location: ../index.php?page=dashboard");
        exit();
    }

    // Delete group members first (foreign key constraint)
    $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id = ?");
    $stmt->execute([$group_id]);

    // Delete messages if they exist
    try {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE group_id = ?");
        $stmt->execute([$group_id]);
    } catch (PDOException $e) {
        // Messages table might not exist
    }

    // Delete meetings if they exist
    try {
        $stmt = $pdo->prepare("DELETE FROM meetings WHERE group_id = ?");
        $stmt->execute([$group_id]);
    } catch (PDOException $e) {
        // Meetings table might not exist
    }

    // Delete the group
    $stmt = $pdo->prepare("DELETE FROM study_groups WHERE id = ?");
    $stmt->execute([$group_id]);

    $_SESSION['success'] = "Group has been deleted successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting group: " . $e->getMessage();
}

header("Location: ../index.php?page=dashboard");
exit();
