<?php
// Simple SMTP test to see what's failing
require_once 'config/smtp.php';

echo "Testing email sending...\n<br>";

$testEmail = "josiebanalo977@gmail.com";
$subject = "Test Email - WMSU Study Finder";
$body = "<h1>Test Email</h1><p>If you receive this, email is working!</p>";

try {
    $result = sendEmail($testEmail, $subject, $body);
    
    if ($result) {
        echo "✅ Email sent successfully!\n<br>";
    } else {
        echo "❌ Email failed to send.\n<br>";
        echo "Check error_log for details.\n<br>";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n<br>";
    echo "Trace: " . $e->getTraceAsString() . "\n<br>";
}
?>
