<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once '../config/database.php';

$meeting_id = $_GET['id'] ?? null;
$group_id = $_GET['group_id'] ?? null;

if (!$meeting_id || !$group_id) {
    $_SESSION['error'] = "Invalid meeting ID";
    header("Location: ../index.php?page=group_details&id=" . $group_id);
    exit();
}

try {
    // Check if user created this meeting
    $stmt = $pdo->prepare("SELECT created_by FROM meetings WHERE id = ? AND group_id = ?");
    $stmt->execute([$meeting_id, $group_id]);
    $meeting = $stmt->fetch();
    
    if (!$meeting) {
        $_SESSION['error'] = "Meeting not found";
        header("Location: ../index.php?page=group_details&id=" . $group_id);
        exit();
    }
    
    if ($meeting['created_by'] != $_SESSION['user_id']) {
        $_SESSION['error'] = "You can only delete meetings you created";
        header("Location: ../index.php?page=group_details&id=" . $group_id);
        exit();
    }
    
    // Delete the meeting
    $stmt = $pdo->prepare("DELETE FROM meetings WHERE id = ?");
    $stmt->execute([$meeting_id]);
    
    $_SESSION['success'] = "Meeting deleted successfully!";
} catch(PDOException $e) {
    $_SESSION['error'] = "Error deleting meeting: " . $e->getMessage();
}

header("Location: ../index.php?page=group_details&id=" . $group_id);
exit();
?>
