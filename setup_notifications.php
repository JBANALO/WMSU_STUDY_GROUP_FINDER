<?php
require_once 'config/database.php';

echo "<h2>Database Schema Update - Notifications</h2>";

try {
    // Create notifications table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(200) NOT NULL,
            message TEXT,
            related_id INT(11),
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    echo "<p style='color: green;'><strong>✓ Notifications table created/verified!</strong></p>";
    
    // Add notification preferences table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notification_preferences (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL UNIQUE,
            account_updates BOOLEAN DEFAULT TRUE,
            group_updates BOOLEAN DEFAULT TRUE,
            join_requests BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    echo "<p style='color: green;'><strong>✓ Notification preferences table created/verified!</strong></p>";
    
    echo "<p><a href='index.php?page=dashboard' style='color: #8B0000; text-decoration: none;'>&larr; Go to Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'><strong>Error: " . $e->getMessage() . "</strong></p>";
}
?>
