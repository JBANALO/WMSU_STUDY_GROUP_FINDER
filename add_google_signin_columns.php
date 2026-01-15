<?php
// add_google_signin_columns.php - Add columns for Google Sign-In support

require_once 'config/database.php';

echo "<h2>Adding Google Sign-In Columns to Users Table</h2>";

try {
    // 1. Add auth_provider column
    echo "<p>Adding auth_provider column...</p>";
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN auth_provider VARCHAR(20) DEFAULT 'local' AFTER email");
        echo "✅ auth_provider column added<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "ℹ️ auth_provider column already exists<br>";
        } else {
            throw $e;
        }
    }
    
    // 2. Add profile_picture column
    echo "<p>Adding profile_picture column...</p>";
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) NULL AFTER last_name");
        echo "✅ profile_picture column added<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "ℹ️ profile_picture column already exists<br>";
        } else {
            throw $e;
        }
    }
    
    // 3. Make password nullable (for Google users who don't have password)
    echo "<p>Making password column nullable...</p>";
    try {
        $pdo->exec("ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NULL");
        echo "✅ password column is now nullable<br>";
    } catch (PDOException $e) {
        echo "⚠️ Error modifying password column: " . $e->getMessage() . "<br>";
    }
    
    // 4. Check if google_id column exists (should already exist from previous migration)
    echo "<p>Checking google_id column...</p>";
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_id'");
    if ($stmt->rowCount() > 0) {
        echo "✅ google_id column already exists<br>";
    } else {
        echo "<p>Adding google_id column...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) UNIQUE NULL AFTER email");
        echo "✅ google_id column added<br>";
    }
    
    echo "<h3>✅ Database Migration Complete!</h3>";
    echo "<p>Your database is now ready for Google Sign-In.</p>";
    
    // Show updated table structure
    echo "<h3>Updated Users Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $stmt->fetch()) {
        $highlight = in_array($row['Field'], ['google_id', 'auth_provider', 'profile_picture', 'password']) 
            ? " style='background: #ffffcc;'" : "";
        echo "<tr{$highlight}>";
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
    echo "<li>✅ Database is ready</li>";
    echo "<li>✅ Login page updated with Google Sign-In button</li>";
    echo "<li>✅ Handler created (handlers/google_signin_handler.php)</li>";
    echo "<li>🔄 Test Google Sign-In at: <a href='http://localhost:8000/?page=login'>http://localhost:8000/?page=login</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div style='background: #ffcccc; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
