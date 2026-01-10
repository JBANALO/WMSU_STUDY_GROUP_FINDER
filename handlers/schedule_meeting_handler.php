<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'] ?? null;
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $meeting_date = $_POST['meeting_date'] ?? null;
    $meeting_time = $_POST['meeting_time'] ?? null;
    $location = $_POST['location'] ?? null;
    $is_online = isset($_POST['is_online']) ? 1 : 0;
    
    if (!$group_id || !$title || !$meeting_date || !$meeting_time) {
        $_SESSION['error'] = "Please fill all required fields";
        header("Location: ../index.php?page=group_details&id=" . $group_id);
        exit();
    }
    
    // Combine date and time
    $full_datetime = $meeting_date . ' ' . $meeting_time;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO meetings (group_id, title, description, meeting_date, location, is_online, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $group_id,
            $title,
            $description,
            $full_datetime,
            $location,
            $is_online,
            $_SESSION['user_id']
        ]);
        
        $_SESSION['success'] = "Meeting scheduled successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error scheduling meeting: " . $e->getMessage();
    }
    
    header("Location: ../index.php?page=group_details&id=" . $group_id);
    exit();
}
?>
