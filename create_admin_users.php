#!/usr/bin/env php
<?php
/**
 * Create Admin and Initial Users for Railway
 */

require_once 'config/database.php';

echo "=== Creating Admin and Initial Users ===\n\n";

// Users to create
$users = [
    [
        'username' => 'admin',
        'email' => 'admin@wmsu.edu.ph',
        'password' => 'admin123',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'status' => 'approved'
    ],
    [
        'username' => 'hz202305178',
        'email' => 'hz202305178@wmsu.edu.ph',
        'password' => 'password123',
        'first_name' => 'Student',
        'last_name' => 'Hz',
        'status' => 'approved'
    ],
    [
        'username' => 'eh202202743',
        'email' => 'eh202202743@wmsu.edu.ph',
        'password' => 'password123',
        'first_name' => 'Student',
        'last_name' => 'Eh',
        'status' => 'approved'
    ]
];

foreach ($users as $user) {
    echo "Creating user: {$user['username']} ({$user['email']})...";
    
    try {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$user['username'], $user['email']]);
        
        if ($stmt->fetch()) {
            echo " ⚠ Already exists\n";
            continue;
        }
        
        // Hash the password
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, first_name, last_name, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $user['username'],
            $user['email'],
            $hashedPassword,
            $user['first_name'],
            $user['last_name'],
            $user['status']
        ]);
        
        echo " ✓ Created (ID: " . $pdo->lastInsertId() . ")\n";
        
    } catch (PDOException $e) {
        echo " ✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== User Creation Complete! ===\n";
echo "You can now login with:\n";
echo "  • admin@wmsu.edu.ph / admin123\n";
echo "  • hz202305178@wmsu.edu.ph / password123\n";
echo "  • eh202202743@wmsu.edu.ph / password123\n";
