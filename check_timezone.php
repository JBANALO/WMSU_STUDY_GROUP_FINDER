<?php
require_once 'config/database.php';

echo "<h2>Timezone Debug</h2>";

// Check MySQL NOW()
$stmt = $pdo->query("SELECT NOW() as mysql_now, @@session.time_zone as mysql_tz");
$result = $stmt->fetch();
echo "<p><strong>MySQL NOW():</strong> {$result['mysql_now']}</p>";
echo "<p><strong>MySQL Timezone:</strong> {$result['mysql_tz']}</p>";

// Check PHP time
echo "<p><strong>PHP date():</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>PHP Timezone:</strong> " . date_default_timezone_get() . "</p>";

// Check latest reset code
$stmt = $pdo->query("
    SELECT 
        code,
        expires_at,
        NOW() as current_time,
        (expires_at > NOW()) as is_valid,
        TIMESTAMPDIFF(SECOND, NOW(), expires_at) as seconds_diff
    FROM password_reset_codes 
    ORDER BY created_at DESC 
    LIMIT 1
");
$code = $stmt->fetch();

if ($code) {
    echo "<h3>Latest Reset Code</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>Code</td><td>{$code['code']}</td></tr>";
    echo "<tr><td>Expires At (saved)</td><td>{$code['expires_at']}</td></tr>";
    echo "<tr><td>NOW() (current)</td><td>{$code['current_time']}</td></tr>";
    echo "<tr><td>Is Valid?</td><td>" . ($code['is_valid'] ? 'YES ✅' : 'NO ❌') . "</td></tr>";
    echo "<tr><td>Seconds Difference</td><td>{$code['seconds_diff']} seconds</td></tr>";
    echo "</table>";
    
    if ($code['seconds_diff'] < 0) {
        echo "<p style='color: red;'><strong>Problem: expires_at ({$code['expires_at']}) is BEFORE NOW() ({$code['current_time']})</strong></p>";
        echo "<p>This means the expiration time was set in the past!</p>";
    }
}
?>
