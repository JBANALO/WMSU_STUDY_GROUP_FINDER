<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password required";
        header("Location: ../index.php?page=login");
        exit();
    }
    
    // Validate WMSU email domain
    if (substr($email, -13) !== '@wmsu.edu.ph') {
        $_SESSION['error'] = "Only WMSU emails (@wmsu.edu.ph) are allowed";
        header("Location: ../index.php?page=login");
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Check if account is approved
            if ($user['status'] !== 'approved') {
                $_SESSION['error'] = "Your account is pending admin approval. Please wait.";
                header("Location: ../index.php?page=login");
                exit();
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            // Check if user is admin (username starts with 'admin' or email starts with 'admin')
            $_SESSION['is_admin'] = (stripos($user['username'], 'admin') === 0 || stripos($user['email'], 'admin') === 0);
            
            // Redirect to admin dashboard if admin, otherwise to student dashboard
            $redirect_page = $_SESSION['is_admin'] ? 'admin_dashboard' : 'dashboard';
            header("Location: ../index.php?page=" . $redirect_page);
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header("Location: ../index.php?page=login");
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: ../index.php?page=login");
        exit();
    }
}
?>