<?php
// test_email.php - Test SMTP email configuration

require_once 'vendor/autoload.php';
require_once 'config/smtp.php';

echo "<h2>Testing SMTP Email Configuration</h2>";
echo "<p>Sending test email to josiebanalo977@gmail.com...</p>";

// Test basic email function
$result = sendEmail(
    'josiebanalo977@gmail.com',
    'Test Email - WMSU Study Finder',
    '<html>
    <body style="font-family: Arial, sans-serif;">
        <h1>Hello! 👋</h1>
        <p>This is a test email from <strong>WMSU Study Group Finder</strong>.</p>
        <p>If you receive this, your SMTP configuration is working correctly!</p>
        <hr>
        <p style="color: #666; font-size: 12px;">
            Sent: ' . date('Y-m-d H:i:s') . '<br>
            SMTP: Gmail (smtp.gmail.com:587)<br>
            From: josiebanalo977@gmail.com
        </p>
    </body>
    </html>'
);

if ($result) {
    echo "<div style='background: #4CAF50; color: white; padding: 15px; border-radius: 5px;'>";
    echo "✅ <strong>Email sent successfully!</strong><br>";
    echo "Check your inbox at josiebanalo977@gmail.com";
    echo "</div>";
    
    echo "<br><h3>Testing Helper Functions:</h3>";
    
    // Test welcome email
    echo "<p>Testing sendWelcomeEmail()...</p>";
    $result2 = sendWelcomeEmail('josiebanalo977@gmail.com', 'Josie');
    echo $result2 ? "✅ Welcome email sent!<br>" : "❌ Failed to send welcome email<br>";
    
    // Test approval email
    echo "<p>Testing sendApprovalEmail()...</p>";
    $result3 = sendApprovalEmail('josiebanalo977@gmail.com', 'Josie');
    echo $result3 ? "✅ Approval email sent!<br>" : "❌ Failed to send approval email<br>";
    
    // Test group approval email
    echo "<p>Testing sendGroupApprovalEmail()...</p>";
    $result4 = sendGroupApprovalEmail('josiebanalo977@gmail.com', 'Josie', 'Computer Science Study Group');
    echo $result4 ? "✅ Group approval email sent!<br>" : "❌ Failed to send group approval email<br>";
    
    echo "<br><p><strong>Check your email inbox for all test emails!</strong></p>";
    
} else {
    echo "<div style='background: #f44336; color: white; padding: 15px; border-radius: 5px;'>";
    echo "❌ <strong>Failed to send email.</strong><br>";
    echo "Check your error logs or verify your SMTP settings in config/smtp.php";
    echo "</div>";
    
    echo "<br><h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure .env file exists with correct credentials</li>";
    echo "<li>Verify Gmail App Password is correct (no spaces)</li>";
    echo "<li>Check if your firewall/antivirus blocks port 587</li>";
    echo "<li>Enable less secure app access if needed</li>";
    echo "</ul>";
}
?>
