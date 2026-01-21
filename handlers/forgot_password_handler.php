<?php
session_start();
require_once '../config/database.php';
require_once '../config/smtp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=forgot_password');
    exit;
}

$email = trim($_POST['email']);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Please enter a valid email address.";
    header('Location: ../index.php?page=forgot_password');
    exit;
}

// Check if email is WMSU email
if (substr($email, -13) !== '@wmsu.edu.ph') {
    $_SESSION['error'] = "Please use your WMSU email address.";
    header('Location: ../index.php?page=forgot_password');
    exit;
}

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, first_name, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Don't reveal if email exists or not for security
        $_SESSION['success'] = "If an account exists with that email, a verification code has been sent.";
        header('Location: ../index.php?page=forgot_password');
        exit;
    }
    
    // Generate 6-digit verification code
    $verification_code = sprintf("%06d", mt_rand(0, 999999));
    
    // Store verification code in database (use MySQL NOW() + INTERVAL for timezone consistency)
    $stmt = $pdo->prepare("
        INSERT INTO password_reset_codes (user_id, email, code, expires_at, created_at) 
        VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE), NOW())
        ON DUPLICATE KEY UPDATE 
            code = VALUES(code), 
            expires_at = DATE_ADD(NOW(), INTERVAL 15 MINUTE), 
            created_at = NOW(),
            used = 0
    ");
    $stmt->execute([$user['id'], $email, $verification_code]);
    
    // Send email with verification code
    $subject = "Password Reset Verification Code - WMSU Study Finder";
    $body = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;'>
            <div style='background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <h2 style='color: #8B0000; margin-bottom: 20px;'>Password Reset Request</h2>
                <p>Hi {$user['first_name']},</p>
                <p>We received a request to reset your password. Use the verification code below:</p>
                
                <div style='background: #f5f5f5; padding: 20px; border-radius: 8px; text-align: center; margin: 25px 0;'>
                    <p style='margin: 0; color: #666; font-size: 12px; margin-bottom: 8px;'>Your Verification Code</p>
                    <h1 style='margin: 0; color: #8B0000; font-size: 36px; letter-spacing: 5px; font-weight: 700;'>{$verification_code}</h1>
                </div>
                
                <p style='color: #666; font-size: 13px;'>
                    <strong>This code will expire in 15 minutes.</strong>
                </p>
                
                <p>Or click this link to reset your password:</p>
                <p style='text-align: center; margin: 20px 0;'>
                    <a href='http://localhost:8000/index.php?page=reset_password&code={$verification_code}&email=" . urlencode($email) . "' 
                       style='display: inline-block; background: #8B0000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: 600;'>
                        Reset Password
                    </a>
                </p>
                
                <hr style='border: none; border-top: 1px solid #eee; margin: 25px 0;'>
                
                <p style='color: #999; font-size: 12px; margin: 0;'>
                    If you didn't request this password reset, please ignore this email or contact support if you have concerns.
                </p>
                <p style='color: #999; font-size: 12px; margin-top: 10px;'>
                    <strong>WMSU Study Group Finder</strong><br>
                    Western Mindanao State University
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $emailSent = sendEmail($email, $subject, $body);
    
    if ($emailSent) {
        $_SESSION['success'] = "Verification code sent to your email! Check your inbox.";
        $_SESSION['reset_email'] = $email; // Store for the next step
        header('Location: ../index.php?page=reset_password&email=' . urlencode($email));
        exit;
    } else {
        $_SESSION['error'] = "Failed to send email. Please try again later.";
        header('Location: ../index.php?page=forgot_password');
        exit;
    }
    
} catch (Exception $e) {
    error_log("Forgot password error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred. Please try again later.";
    header('Location: ../index.php?page=forgot_password');
    exit;
}
?>
