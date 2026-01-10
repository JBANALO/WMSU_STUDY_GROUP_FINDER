<?php
// Database Setup Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get database credentials from environment or config
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_port = getenv('DB_PORT') ?: '3306';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'studyfinder';

echo "<h2>Database Setup - WMSU Study Group Finder</h2>";
echo "<p>Connecting to: $db_host:$db_port as $db_user...</p>";

try {
    // Connect to MySQL
    $pdo = new PDO(
        "mysql:host=$db_host;port=$db_port",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color: green;'>✓ Connected to MySQL</p>";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "<p style='color: green;'>✓ Database created/verified</p>";
    
    // Select database
    $pdo->exec("USE `$db_name`");
    
    // SQL statements
    $sql = <<<SQL
    -- Create users table
    CREATE TABLE IF NOT EXISTS users (
      id INT PRIMARY KEY AUTO_INCREMENT,
      username VARCHAR(255) UNIQUE NOT NULL,
      email VARCHAR(255) UNIQUE NOT NULL,
      first_name VARCHAR(255),
      last_name VARCHAR(255),
      password VARCHAR(255) NOT NULL,
      status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Create study_groups table
    CREATE TABLE IF NOT EXISTS study_groups (
      id INT PRIMARY KEY AUTO_INCREMENT,
      group_name VARCHAR(255) NOT NULL,
      description TEXT,
      subject VARCHAR(255),
      creator_id INT NOT NULL,
      status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
      decline_reason TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- Create group_members table
    CREATE TABLE IF NOT EXISTS group_members (
      id INT PRIMARY KEY AUTO_INCREMENT,
      group_id INT NOT NULL,
      user_id INT NOT NULL,
      role VARCHAR(50) DEFAULT 'member',
      joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
      UNIQUE KEY unique_member (group_id, user_id)
    );

    -- Create group_messages table
    CREATE TABLE IF NOT EXISTS group_messages (
      id INT PRIMARY KEY AUTO_INCREMENT,
      group_id INT NOT NULL,
      user_id INT NOT NULL,
      message TEXT,
      attachment VARCHAR(255),
      message_type VARCHAR(50) DEFAULT 'user',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- Create meetings table
    CREATE TABLE IF NOT EXISTS meetings (
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
    );

    -- Create notifications table
    CREATE TABLE IF NOT EXISTS notifications (
      id INT PRIMARY KEY AUTO_INCREMENT,
      user_id INT NOT NULL,
      type VARCHAR(50),
      title VARCHAR(255),
      message TEXT,
      is_read BOOLEAN DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- Create user_last_seen table
    CREATE TABLE IF NOT EXISTS user_last_seen (
      id INT PRIMARY KEY AUTO_INCREMENT,
      user_id INT NOT NULL,
      group_id INT NOT NULL,
      last_seen_message_id INT,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
      FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
      UNIQUE KEY unique_user_group (user_id, group_id)
    );
SQL;

    // Execute statements one by one
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<h3 style='color: green;'>✓ All tables created successfully!</h3>";
    echo "<ul style='color: green;'>";
    echo "<li>✓ users</li>";
    echo "<li>✓ study_groups</li>";
    echo "<li>✓ group_members</li>";
    echo "<li>✓ group_messages</li>";
    echo "<li>✓ meetings</li>";
    echo "<li>✓ notifications</li>";
    echo "<li>✓ user_last_seen</li>";
    echo "</ul>";
    
    echo "<p><strong>Database setup complete!</strong> You can now:</p>";
    echo "<ol>";
    echo "<li><a href='index.php?page=register'>Register a new account</a></li>";
    echo "<li><a href='index.php?page=login'>Login</a></li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure your database credentials are correct in your environment variables:</p>";
    echo "<ul>";
    echo "<li>DB_HOST=$db_host</li>";
    echo "<li>DB_PORT=$db_port</li>";
    echo "<li>DB_USER=$db_user</li>";
    echo "<li>DB_NAME=$db_name</li>";
    echo "</ul>";
    exit;
}
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
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
</body>
</html>
