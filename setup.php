<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup - WMSU Study Finder</title>
    <style>body{font-family:Arial;max-width:600px;margin:50px auto;padding:20px;background:#f5f5f5}.ok{color:green}.err{color:red}</style>
</head>
<body>
<h2>Database Setup</h2>
<?php
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'railway';

echo "<p>Connecting to: $host:$port...</p>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass);
    echo "<p class='ok'>✓ Connected!</p>";
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name`");
    $pdo->exec("USE `$name`");
    echo "<p class='ok'>✓ Database ready</p>";
    
    // Create tables
    $tables = [
        "CREATE TABLE IF NOT EXISTS users (id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(255) UNIQUE NOT NULL, email VARCHAR(255) UNIQUE NOT NULL, first_name VARCHAR(255), last_name VARCHAR(255), password VARCHAR(255) NOT NULL, status ENUM('pending','approved','declined') DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
        "CREATE TABLE IF NOT EXISTS study_groups (id INT PRIMARY KEY AUTO_INCREMENT, group_name VARCHAR(255) NOT NULL, description TEXT, subject VARCHAR(255), creator_id INT NOT NULL, status ENUM('pending','approved','declined') DEFAULT 'pending', decline_reason TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE)",
        "CREATE TABLE IF NOT EXISTS group_members (id INT PRIMARY KEY AUTO_INCREMENT, group_id INT NOT NULL, user_id INT NOT NULL, role VARCHAR(50) DEFAULT 'member', joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, UNIQUE KEY unique_member (group_id, user_id))",
        "CREATE TABLE IF NOT EXISTS group_messages (id INT PRIMARY KEY AUTO_INCREMENT, group_id INT NOT NULL, user_id INT NOT NULL, message TEXT, attachment VARCHAR(255), message_type VARCHAR(50) DEFAULT 'user', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE)",
        "CREATE TABLE IF NOT EXISTS meetings (id INT PRIMARY KEY AUTO_INCREMENT, group_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT, meeting_date DATETIME NOT NULL, location VARCHAR(255), is_online BOOLEAN DEFAULT 1, created_by INT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE, FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE)",
        "CREATE TABLE IF NOT EXISTS notifications (id INT PRIMARY KEY AUTO_INCREMENT, user_id INT NOT NULL, type VARCHAR(50), title VARCHAR(255), message TEXT, is_read BOOLEAN DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE)",
        "CREATE TABLE IF NOT EXISTS user_last_seen (id INT PRIMARY KEY AUTO_INCREMENT, user_id INT NOT NULL, group_id INT NOT NULL, last_seen_message_id INT, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE, UNIQUE KEY unique_user_group (user_id, group_id))"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    
    echo "<h3 class='ok'>✓ All 7 tables created!</h3>";
    echo "<ul class='ok'><li>users</li><li>study_groups</li><li>group_members</li><li>group_messages</li><li>meetings</li><li>notifications</li><li>user_last_seen</li></ul>";
    echo "<p><strong>Done! Setup complete!</strong></p>";
} catch (Exception $e) {
    echo "<p class='err'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
</body>
</html>