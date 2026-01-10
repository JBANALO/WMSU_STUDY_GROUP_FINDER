<?php
require_once 'config/database.php';

try {
    // Create meetings table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS meetings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            group_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            meeting_date DATETIME NOT NULL,
            location VARCHAR(255),
            is_online BOOLEAN DEFAULT TRUE,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES study_groups(id),
            FOREIGN KEY (created_by) REFERENCES users(id)
        )
    ");
    
    echo "<p style='color: green; font-weight: bold;'>✓ Meetings table created successfully!</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
