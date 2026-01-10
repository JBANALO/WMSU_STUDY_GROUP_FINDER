<?php
require_once 'config/database.php';

// Admin account details
$admin_first_name = 'Admin';
$admin_last_name = 'User';
$admin_username = 'admin';
$admin_email = 'admin@wmsu.edu.ph';
$admin_password = 'admin123'; // Change this!

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$admin_email]);
    
    if ($stmt->fetch()) {
        echo "Admin account already exists!";
        exit();
    }
    
    // Create admin account
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, middle_name, last_name, username, email, password, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'approved', NOW())
    ");
    
    $stmt->execute([
        $admin_first_name,
        '',
        $admin_last_name,
        $admin_username,
        $admin_email,
        $hashed_password
    ]);
    
    echo "✅ Admin account created successfully!<br>";
    echo "Email: " . $admin_email . "<br>";
    echo "Password: " . $admin_password . "<br>";
    echo "<strong>Change the password after first login!</strong>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
