<?php
session_start();
require_once 'config/database.php';

// Simulate what reset_password_handler does
if (isset($_GET['test'])) {
    $email = trim($_GET['email'] ?? '');
    $code = trim($_GET['code'] ?? '');
    
    echo "<h2>Testing Reset Code Lookup</h2>";
    echo "<p><strong>Email:</strong> '{$email}' (length: " . strlen($email) . ")</p>";
    echo "<p><strong>Code:</strong> '{$code}' (length: " . strlen($code) . ")</p>";
    
    // Check for exact match
    $stmt = $pdo->prepare("
        SELECT * FROM password_reset_codes 
        WHERE email = ? AND code = ? AND used = 0 AND expires_at > NOW()
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$email, $code]);
    $reset_code = $stmt->fetch();
    
    if ($reset_code) {
        echo "<p style='color: green;'><strong>✅ MATCH FOUND!</strong></p>";
        echo "<pre>" . print_r($reset_code, true) . "</pre>";
    } else {
        echo "<p style='color: red;'><strong>❌ NO MATCH FOUND</strong></p>";
        
        // Check what codes exist for this email
        $checkStmt = $pdo->prepare("
            SELECT *, 
                   (expires_at > NOW()) as is_valid,
                   TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_left
            FROM password_reset_codes 
            WHERE email = ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $checkStmt->execute([$email]);
        $codes = $checkStmt->fetchAll();
        
        if ($codes) {
            echo "<h3>Codes found for this email:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Code</th><th>Used</th><th>Expires At</th><th>Minutes Left</th><th>Match?</th></tr>";
            foreach ($codes as $c) {
                $matches = ($c['code'] === $code) ? '✅ YES' : '❌ NO';
                $codeDisplay = "'{$c['code']}' (len: " . strlen($c['code']) . ")";
                echo "<tr>";
                echo "<td>{$codeDisplay}</td>";
                echo "<td>" . ($c['used'] ? 'YES' : 'NO') . "</td>";
                echo "<td>{$c['expires_at']}</td>";
                echo "<td>{$c['minutes_left']}</td>";
                echo "<td>{$matches}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    exit;
}
?>

<h2>Test Reset Password Code Lookup</h2>
<form method="GET">
    <input type="hidden" name="test" value="1">
    <p>
        <label>Email:</label><br>
        <input type="text" name="email" value="hz202305178@wmsu.edu.ph" style="width: 300px;">
    </p>
    <p>
        <label>Code (copy from check_reset_codes.php):</label><br>
        <input type="text" name="code" value="" style="width: 300px;">
    </p>
    <button type="submit">Test Lookup</button>
</form>

<p><a href="check_reset_codes.php" target="_blank">Open check_reset_codes.php to see latest code</a></p>
