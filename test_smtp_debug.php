<?php
// test_smtp_debug.php - Debug SMTP connection

require_once 'vendor/autoload.php';
require_once 'config/smtp.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing SMTP Connection</h2>";

// Test with verbose error messages
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Enable verbose debug output
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';
    
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'josiebanalo977@gmail.com';
    $mail->Password   = 'qqvxoikciphvawuw';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    
    // Sender
    $mail->setFrom('josiebanalo977@gmail.com', 'WMSU Study Group Finder');
    $mail->addAddress('josiebanalo977@gmail.com');
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - SMTP Debug';
    $mail->Body    = '<h1>This is a test email</h1><p>If you receive this, SMTP is working!</p>';
    
    $mail->send();
    echo "<div style='background: #4CAF50; color: white; padding: 15px; margin: 20px 0;'>";
    echo "<h3>✅ Email sent successfully!</h3>";
    echo "<p>Check your inbox at josiebanalo977@gmail.com</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f44336; color: white; padding: 15px; margin: 20px 0;'>";
    echo "<h3>❌ Failed to send email</h3>";
    echo "<p><strong>Error:</strong> {$mail->ErrorInfo}</p>";
    echo "</div>";
}
?>
