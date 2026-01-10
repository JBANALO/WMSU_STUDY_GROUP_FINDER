<?php
require_once 'config/database.php';

try {
    // Create notification_preferences table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_preferences (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            group_id INT NOT NULL,
            enabled BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_group (user_id, group_id)
        )
    ");
    
    echo "✓ notification_preferences table created successfully!<br>";
    
    // Create group_messages table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS group_messages (
            id INT PRIMARY KEY AUTO_INCREMENT,
            group_id INT NOT NULL,
            user_id INT NOT NULL,
            message TEXT NOT NULL,
            message_type VARCHAR(50) DEFAULT 'message',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_group_id (group_id)
        )
    ");
    
    echo "✓ group_messages table created successfully!<br>";
    echo "<br>All database tables ready!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
