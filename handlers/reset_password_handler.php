<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=reset_password');
    exit;
}

$email = trim($_POST['email']);
$code = trim($_POST['code']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validate inputs
if (empty($email) || empty($code) || empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = "All fields are required.";
    header('Location: ../index.php?page=reset_password');
    exit;
}

// Validate password match
if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match.";
    header('Location: ../index.php?page=reset_password');
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    $_SESSION['error'] = "Password must be at least 6 characters long.";
    header('Location: ../index.php?page=reset_password');
    exit;
}

try {
    // DEBUG: Log what we're searching for
    error_log("Reset Password - Searching for: Email='{$email}', Code='{$code}'");
    
    // Verify code and check expiration
    $stmt = $pdo->prepare("
        SELECT * FROM password_reset_codes 
        WHERE email = ? AND code = ? AND used = 0 AND expires_at > NOW()
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$email, $code]);
    $reset_code = $stmt->fetch();
    
    // DEBUG: Log what we found
    if ($reset_code) {
        error_log("Reset Password - Found code: ID={$reset_code['id']}, Code={$reset_code['code']}");
    } else {
        error_log("Reset Password - No matching code found in database");
        // Check if there's ANY code for this email
        $checkStmt = $pdo->prepare("SELECT * FROM password_reset_codes WHERE email = ? ORDER BY created_at DESC LIMIT 1");
        $checkStmt->execute([$email]);
        $anyCode = $checkStmt->fetch();
        if ($anyCode) {
            error_log("Reset Password - But found this code for email: Code={$anyCode['code']}, Used={$anyCode['used']}, Expires={$anyCode['expires_at']}");
        }
    }
    
    if (!$reset_code) {
        // More detailed error message for debugging
        $checkStmt = $pdo->prepare("
            SELECT code, used, expires_at, 
                   (expires_at > NOW()) as is_valid,
                   TIMESTAMPDIFF(SECOND, NOW(), expires_at) as seconds_left
            FROM password_reset_codes 
            WHERE email = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $checkStmt->execute([$email]);
        $latestCode = $checkStmt->fetch();
        
        if ($latestCode) {
            if ($latestCode['used'] == 1) {
                $_SESSION['error'] = "This verification code has already been used. Please request a new one.";
            } elseif ($latestCode['seconds_left'] <= 0) {
                $_SESSION['error'] = "Verification code has expired. Please request a new one.";
            } elseif ($latestCode['code'] !== $code) {
                $_SESSION['error'] = "Incorrect verification code. Please check your email and try again.";
            } else {
                $_SESSION['error'] = "Unable to verify code. Please try again.";
            }
        } else {
            $_SESSION['error'] = "No verification code found for this email. Please request a new one.";
        }
        
        $_SESSION['reset_email'] = $email;
        header('Location: ../index.php?page=reset_password&email=' . urlencode($email));
        exit;
    }
    
    // Hash new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Update user password
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed_password, $reset_code['user_id']]);
    
    // Mark code as used
    $stmt = $pdo->prepare("UPDATE password_reset_codes SET used = 1 WHERE id = ?");
    $stmt->execute([$reset_code['id']]);
    
    $_SESSION['success'] = "Password reset successfully! You can now login with your new password.";
    header('Location: ../index.php?page=login');
    exit;
    
} catch (Exception $e) {
    error_log("Reset password error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred. Please try again later.";
    header('Location: ../index.php?page=reset_password');
    exit;
}
?>
