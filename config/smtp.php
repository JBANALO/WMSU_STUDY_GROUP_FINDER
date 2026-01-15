<?php
// config/smtp.php - Email sending functionality using PHPMailer

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email using Gmail SMTP
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $body Email body (HTML supported)
 * @param string $replyTo Optional reply-to email address
 * @return bool True if sent successfully, false otherwise
 */
function sendEmail($to, $subject, $body, $replyTo = null) {
    $mail = new PHPMailer(true);
    
    try {
        // Load environment variables
        $isProduction = getenv('RAILWAY_ENVIRONMENT') !== false;
        
        if ($isProduction) {
            // Production: Use Railway environment variables
            $smtpUsername = getenv('SMTP_USERNAME');
            $smtpPassword = getenv('SMTP_PASSWORD');
            $smtpFromName = getenv('SMTP_FROM_NAME') ?: 'WMSU Study Group Finder';
        } else {
            // Local: Use .env file
            $envFile = __DIR__ . '/../.env';
            if (file_exists($envFile)) {
                $env = parse_ini_file($envFile);
                $smtpUsername = $env['SMTP_USERNAME'];
                $smtpPassword = $env['SMTP_PASSWORD'];
                $smtpFromName = $env['SMTP_FROM_NAME'] ?? 'WMSU Study Group Finder';
            } else {
                // Fallback to hardcoded values (NOT RECOMMENDED for production)
                $smtpUsername = 'josiebanalo977@gmail.com';
                $smtpPassword = 'qqvxoikciphvawuw';
                $smtpFromName = 'WMSU Study Group Finder';
            }
        }
        
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUsername;
        $mail->Password   = $smtpPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Sender settings
        $mail->setFrom($smtpUsername, $smtpFromName);
        
        // Reply-To (optional)
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }
        
        // Recipient
        $mail->addAddress($to);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // Plain text for non-HTML clients
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Send welcome email to new user
 */
function sendWelcomeEmail($email, $firstName) {
    $subject = "Welcome to WMSU Study Group Finder!";
    $body = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Welcome to WMSU Study Group Finder, {$firstName}!</h2>
        <p>Your account has been successfully created.</p>
        <p>You can now:</p>
        <ul>
            <li>Browse and join study groups</li>
            <li>Create your own study groups</li>
            <li>Connect with fellow WMSU students</li>
            <li>Schedule study sessions</li>
        </ul>
        <p>Please wait for admin approval to access all features.</p>
        <br>
        <p>Best regards,<br>WMSU Study Group Finder Team</p>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

/**
 * Send approval notification email
 */
function sendApprovalEmail($email, $firstName) {
    $subject = "Your Account has been Approved!";
    $body = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Congratulations, {$firstName}!</h2>
        <p>Your WMSU Study Group Finder account has been approved by an administrator.</p>
        <p>You now have full access to all features. Log in to start exploring study groups!</p>
        <br>
        <p><a href='https://web-production-76301.up.railway.app/?page=login' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login Now</a></p>
        <br>
        <p>Best regards,<br>WMSU Study Group Finder Team</p>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

/**
 * Send group approval notification
 */
function sendGroupApprovalEmail($email, $firstName, $groupName) {
    $subject = "Your Study Group has been Approved!";
    $body = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Great news, {$firstName}!</h2>
        <p>Your study group <strong>{$groupName}</strong> has been approved by an administrator.</p>
        <p>Your group is now visible to all WMSU students. Start inviting members and scheduling study sessions!</p>
        <br>
        <p>Best regards,<br>WMSU Study Group Finder Team</p>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

/**
 * Send new message notification
 */
function sendMessageNotification($email, $firstName, $senderName, $groupName) {
    $subject = "New Message in {$groupName}";
    $body = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Hi {$firstName},</h2>
        <p><strong>{$senderName}</strong> sent a message in <strong>{$groupName}</strong>.</p>
        <p>Log in to view and reply to the message.</p>
        <br>
        <p><a href='https://web-production-76301.up.railway.app/?page=login' style='background-color: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Message</a></p>
        <br>
        <p>Best regards,<br>WMSU Study Group Finder Team</p>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}
