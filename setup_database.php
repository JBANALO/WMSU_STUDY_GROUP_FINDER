<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - WMSU Study Group Finder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<?php
// Get database credentials from environment
$db_host = getenv('DB_HOST');
$db_port = getenv('DB_PORT') ?: '3306';
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

echo "<h2>Database Setup - WMSU Study Group Finder</h2>";

if (!$db_host || !$db_user || !$db_pass || !$db_name) {
    echo "<p class='error'>✗ Missing database environment variables!</p>";
    echo "<p>Required variables:</p>";
    echo "<ul>";
    echo "<li>DB_HOST: " . ($db_host ?: "NOT SET") . "</li>";
    echo "<li>DB_PORT: $db_port</li>";
    echo "<li>DB_USER: " . ($db_user ?: "NOT SET") . "</li>";
    echo "<li>DB_PASS: " . ($db_pass ? "SET" : "NOT SET") . "</li>";
    echo "<li>DB_NAME: " . ($db_name ?: "NOT SET") . "</li>";
    echo "</ul>";
    exit;
}

echo "<p>Connecting to: $db_host:$db_port as $db_user...</p>";

try {
    $pdo = new PDO(
        "mysql:host=$db_host;port=$db_port;charset=utf8mb4",
        $db_user,
        $db_pass,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    
    echo "<p class='success'>✓ Connected to MySQL</p>";
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "<p class='success'>✓ Database created/verified</p>";
    
    $pdo->exec("USE `$db_name`");
    
    // Create tables
    $tables = array(
        "CREATE TABLE IF NOT EXISTS users (
          id INT PRIMARY KEY AUTO_INCREMENT,
          username VARCHAR(255) UNIQUE NOT NULL,
          email VARCHAR(255) UNIQUE NOT NULL,
          first_name VARCHAR(255),
          last_name VARCHAR(255),
          password VARCHAR(255) NOT NULL,
          status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS study_groups (
          id INT PRIMARY KEY AUTO_INCREMENT,
          group_name VARCHAR(255) NOT NULL,
          description TEXT,
          subject VARCHAR(255),
          creator_id INT NOT NULL,
          status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
          decline_reason TEXT,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        "CREATE TABLE IF NOT EXISTS group_members (
          id INT PRIMARY KEY AUTO_INCREMENT,
          group_id INT NOT NULL,
          user_id INT NOT NULL,
          role VARCHAR(50) DEFAULT 'member',
          joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
          UNIQUE KEY unique_member (group_id, user_id)
        )",
        
        "CREATE TABLE IF NOT EXISTS group_messages (
          id INT PRIMARY KEY AUTO_INCREMENT,
          group_id INT NOT NULL,
          user_id INT NOT NULL,
          message TEXT,
          attachment VARCHAR(255),
          message_type VARCHAR(50) DEFAULT 'user',
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        "CREATE TABLE IF NOT EXISTS meetings (
          id INT PRIMARY KEY AUTO_INCREMENT,
          group_id INT NOT NULL,
          title VARCHAR(255) NOT NULL,
          description TEXT,
          meeting_date DATETIME NOT NULL,
          location VARCHAR(255),
          is_online BOOLEAN DEFAULT 1,
          created_by INT NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
          FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        "CREATE TABLE IF NOT EXISTS notifications (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT NOT NULL,
          type VARCHAR(50),
          title VARCHAR(255),
          message TEXT,
          is_read BOOLEAN DEFAULT 0,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        "CREATE TABLE IF NOT EXISTS user_last_seen (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT NOT NULL,
          group_id INT NOT NULL,
          last_seen_message_id INT,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
          FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
          UNIQUE KEY unique_user_group (user_id, group_id)
        )"
    );
    
    foreach ($tables as $table) {
        $pdo->exec($table);
    }
    
    echo "<h3 class='success'>✓ All tables created successfully!</h3>";
    echo "<ul class='success'>";
    echo "<li>✓ users</li>";
    echo "<li>✓ study_groups</li>";
    echo "<li>✓ group_members</li>";
    echo "<li>✓ group_messages</li>";
    echo "<li>✓ meetings</li>";
    echo "<li>✓ notifications</li>";
    echo "<li>✓ user_last_seen</li>";
    echo "</ul>";
    echo "<p><strong>Database setup complete!</strong></p>";
    echo "<p><a href='/index.php'>Go to Home Page</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Check your environment variables and database connection.</p>";
}
?>
</body>
</html>
