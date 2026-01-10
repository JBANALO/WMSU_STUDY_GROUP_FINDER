<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?page=login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?page=change_password");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: ../index.php?page=change_password");
    exit();
}

if (strlen($new_password) < 6) {
    $_SESSION['error'] = "New password must be at least 6 characters long.";
    header("Location: ../index.php?page=change_password");
    exit();
}

if ($new_password !== $confirm_password) {
    $_SESSION['error'] = "New passwords do not match.";
    header("Location: ../index.php?page=change_password");
    exit();
}

try {
    // Get user's current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = "Current password is incorrect.";
        header("Location: ../index.php?page=change_password");
        exit();
    }

    // Check if new password is same as old password
    if (password_verify($new_password, $user['password'])) {
        $_SESSION['error'] = "New password must be different from current password.";
        header("Location: ../index.php?page=change_password");
        exit();
    }

    // Update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed_password, $user_id]);

    $_SESSION['success'] = "Password changed successfully!";
    header("Location: ../index.php?page=profile");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Error changing password: " . $e->getMessage();
    header("Location: ../index.php?page=change_password");
    exit();
}
