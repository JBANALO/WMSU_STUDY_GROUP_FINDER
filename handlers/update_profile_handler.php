<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login first.";
    header("Location: ../index.php?page=login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?page=profile");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($first_name) || empty($last_name) || empty($email)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../index.php?page=profile", true, 302);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: ../index.php?page=profile", true, 302);
    exit();
}

try {
    // Check if email is already used by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Email is already in use.";
        header("Location: ../index.php?page=profile", true, 302);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $email, $user_id]);

    // Update session
    $_SESSION['full_name'] = $first_name . ' ' . $last_name;

    $_SESSION['success'] = "Profile updated successfully!";
    
    // Redirect back to profile page
    $redirect_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/../index.php?page=profile";
    header("Location: " . $redirect_url);
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
    $redirect_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/../index.php?page=profile";
    header("Location: " . $redirect_url);
    exit();
}
