<?php
// Railway MySQL Connection Script
echo "Connecting to Railway MySQL database...\n\n";

$host = 'trolley.proxy.rlwy.net';
$port = 39024;
$user = 'root';
$password = 'xubWKXyVtExslXDSbLMIOeLADDXEPRpG';
$database = 'railway';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to Railway MySQL successfully!\n\n";
    
    echo "Creating tables...\n\n";
    
    $success = 0;
    $failed = 0;
    
    // Create tables in order
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
          id INT PRIMARY KEY AUTO_INCREMENT,
          username VARCHAR(255) UNIQUE NOT NULL,
          email VARCHAR(255) UNIQUE NOT NULL,
          first_name VARCHAR(255),
          last_name VARCHAR(255),
          password VARCHAR(255) NOT NULL,
          status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'study_groups' => "CREATE TABLE IF NOT EXISTS study_groups (
          id INT PRIMARY KEY AUTO_INCREMENT,
          group_name VARCHAR(255) NOT NULL,
          description TEXT,
          subject VARCHAR(255),
          creator_id INT NOT NULL,
          status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
          decline_reason TEXT,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (creator_id) REFERENCES users(id)
        )",
        
        'group_members' => "CREATE TABLE IF NOT EXISTS group_members (
          id INT PRIMARY KEY AUTO_INCREMENT,
          group_id INT NOT NULL,
          user_id INT NOT NULL,
          role VARCHAR(50) DEFAULT 'member',
          joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (group_id) REFERENCES study_groups(id),
          FOREIGN KEY (user_id) REFERENCES users(id)
        )",
        
        'group_messages' => "CREATE TABLE IF NOT EXISTS group_messages (
          id INT PRIMARY KEY AUTO_INCREMENT,
          group_id INT NOT NULL,
          user_id INT NOT NULL,
          message TEXT,
          attachment VARCHAR(255),
          message_type VARCHAR(50) DEFAULT 'user',
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (group_id) REFERENCES study_groups(id),
          FOREIGN KEY (user_id) REFERENCES users(id)
        )",
        
        'meetings' => "CREATE TABLE IF NOT EXISTS meetings (
          id INT PRIMARY KEY AUTO_INCREMENT,
          group_id INT NOT NULL,
          title VARCHAR(255) NOT NULL,
          description TEXT,
          meeting_date DATETIME NOT NULL,
          location VARCHAR(255),
          is_online BOOLEAN DEFAULT 1,
          created_by INT NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (group_id) REFERENCES study_groups(id),
          FOREIGN KEY (created_by) REFERENCES users(id)
        )",
        
        'notifications' => "CREATE TABLE IF NOT EXISTS notifications (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT NOT NULL,
          type VARCHAR(50),
          title VARCHAR(255),
          message TEXT,
          is_read BOOLEAN DEFAULT 0,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (user_id) REFERENCES users(id)
        )",
        
        'user_last_seen' => "CREATE TABLE IF NOT EXISTS user_last_seen (
          id INT PRIMARY KEY AUTO_INCREMENT,
          user_id INT NOT NULL,
          group_id INT NOT NULL,
          last_seen_message_id INT,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY (user_id) REFERENCES users(id),
          FOREIGN KEY (group_id) REFERENCES study_groups(id)
        )"
    ];
    
    foreach ($tables as $tableName => $sql) {
        try {
            $pdo->exec($sql);
            echo "✓ Created table: $tableName\n";
            $success++;
        } catch (PDOException $e) {
            echo "✗ Failed to create table $tableName: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo "  ✓ Tables created successfully: $success\n";
    if ($failed > 0) {
        echo "  ✗ Failed: $failed\n";
    }
    echo str_repeat("=", 50) . "\n\n";
    
    // Verify tables exist
    echo "Verifying tables in database...\n\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "Tables in railway database:\n";
        foreach ($tables as $table) {
            echo "  • $table\n";
        }
    } else {
        echo "No tables found.\n";
    }
    
    echo "\n✓ Database setup complete!\n";
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
