<?php
require_once 'config/database.php';

echo "<h2>Fix Notification Preferences Table</h2>";

try {
    // Create notification_preferences table with correct schema
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
    
    echo "<p style='color: green;'><strong>✓ notification_preferences table created successfully!</strong></p>";
    echo "<p>The table now supports per-group notification settings.</p>";
    echo "<br><p><a href='index.php?page=dashboard' style='color: #8B0000; text-decoration: none;'>&larr; Go to Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'><strong>Error: " . $e->getMessage() . "</strong></p>";
    echo "<p>If the table already exists with a different schema, you may need to drop it first.</p>";
}
?>
