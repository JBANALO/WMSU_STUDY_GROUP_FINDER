<?php
require_once 'config/database.php';

try {
    // Check if status column exists
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    echo "Current columns in users table:<br>";
    foreach ($columns as $col) {
        echo "- " . $col . "<br>";
    }
    echo "<br>";
    
    if (!in_array('status', $columns)) {
        echo "Adding 'status' column...<br>";
        $pdo->exec("ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
        echo "✓ Status column added!<br>";
    } else {
        echo "Status column already exists<br>";
    }
    
    // Now check/create admin
    $stmt = $pdo->prepare("SELECT id, email, status FROM users WHERE email = ?");
    $stmt->execute(['admin@wmsu.edu.ph']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "Admin user found with status: " . $user['status'] . "<br>";
        if ($user['status'] !== 'approved') {
            $updateStmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            echo "✓ Admin status updated to 'approved'<br>";
        } else {
            echo "✓ Admin is already approved<br>";
        }
    } else {
        echo "Creating admin account...<br>";
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $insertStmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, username, email, password, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'approved', NOW())
        ");
        $insertStmt->execute(['Admin', 'User', 'admin', 'admin@wmsu.edu.ph', $hashed]);
        echo "✓ Admin account created and approved!<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
