<?php
require_once '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate WMSU email domain
    if (!str_ends_with($email, '@wmsu.edu.ph')) {
        $_SESSION['error'] = "Only WMSU emails (@wmsu.edu.ph) are allowed";
        header("Location: ../index.php?page=register");
        exit();
    }

    // Validate passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: ../index.php?page=register");
        exit();
    }

    // Validate password length
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters";
        header("Location: ../index.php?page=register");
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Email already registered";
        header("Location: ../index.php?page=register");
        exit();
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Username already taken";
        header("Location: ../index.php?page=register");
        exit();
    }

    // Hash password and create account
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, middle_name, last_name, username, email, password, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$first_name, $middle_name, $last_name, $username, $email, $hashed_password]);
        $new_user_id = $pdo->lastInsertId();
        
        // Notify all admins about new user registration
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username LIKE 'admin%' OR email LIKE 'admin%'");
            $stmt->execute();
            $admins = $stmt->fetchAll();
            
            foreach ($admins as $admin) {
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, type, title, message, related_id)
                    VALUES (?, 'user', 'New User Registration', ?, ?)
                ");
                $stmt->execute([
                    $admin['id'],
                    "{$first_name} {$last_name} ({$email}) has registered and is waiting for approval.",
                    $new_user_id
                ]);
            }
        } catch(PDOException $notif_error) {
            // Log notification error but don't fail registration
            error_log("Notification creation failed: " . $notif_error->getMessage());
        }
        
        $pdo->commit();
        
        $_SESSION['success'] = "Account created! Waiting for admin approval.";
        header("Location: ../index.php?page=login");
        exit();
    } catch(PDOException $e) {
        $pdo->rollBack();
        error_log("Registration failed: " . $e->getMessage());
        $_SESSION['error'] = "Registration failed. Please try again. Error: " . $e->getMessage();
        header("Location: ../index.php?page=register");
        exit();
    }
}
?>
