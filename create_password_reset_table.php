<?php
// create_password_reset_table.php - Create table for password reset codes

require_once 'config/database.php';

echo "<h2>Creating Password Reset Codes Table</h2>";

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS password_reset_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        code VARCHAR(6) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at DATETIME NOT NULL,
        used TINYINT(1) DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_email_code (email, code),
        INDEX idx_expires (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    
    echo "<p style='color: green;'>✅ <strong>Table 'password_reset_codes' created successfully!</strong></p>";
    
    // Show table structure
    echo "<h3>Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE password_reset_codes");
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><strong>{$row['Field']}</strong></td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Password reset table created</li>";
    echo "<li>✅ Forgot password page created</li>";
    echo "<li>✅ Reset password page created</li>";
    echo "<li>✅ Email handlers configured</li>";
    echo "<li>🔄 Test forgot password at: <a href='http://localhost:8000/?page=forgot_password'>http://localhost:8000/?page=forgot_password</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div style='background: #ffcccc; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
