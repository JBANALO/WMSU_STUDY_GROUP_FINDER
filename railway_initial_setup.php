<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Railway Initial Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Railway Initial Setup</h1>
        
        <?php
        require_once 'config/database.php';
        
        if (isset($_GET['run']) && $_GET['run'] === 'setup') {
            echo "<h2>Creating Admin and Initial Users...</h2>";
            
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
            
            echo "<pre>";
            foreach ($users as $user) {
                echo "Creating user: {$user['username']} ({$user['email']})... ";
                
                try {
                    // Check if user already exists
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$user['username'], $user['email']]);
                    
                    if ($stmt->fetch()) {
                        echo "<span class='warning'>⚠ Already exists</span>\n";
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
                    
                    echo "<span class='success'>✓ Created (ID: " . $pdo->lastInsertId() . ")</span>\n";
                    
                } catch (PDOException $e) {
                    echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span>\n";
                }
            }
            echo "</pre>";
            
            echo "<div class='success'>";
            echo "<h3>✓ Setup Complete!</h3>";
            echo "<p>You can now login with:</p>";
            echo "<ul>";
            echo "<li><strong>admin@wmsu.edu.ph</strong> / admin123</li>";
            echo "<li><strong>hz202305178@wmsu.edu.ph</strong> / password123</li>";
            echo "<li><strong>eh202202743@wmsu.edu.ph</strong> / password123</li>";
            echo "</ul>";
            echo "</div>";
            
            echo '<a href="index.php?page=login" class="btn">Go to Login Page</a>';
            
        } else {
            echo "<p>This page will create the initial admin and user accounts for the Railway deployment.</p>";
            echo "<p><strong>Click the button below to create:</strong></p>";
            echo "<ul>";
            echo "<li>Admin account: <code>admin@wmsu.edu.ph</code> / <code>admin123</code></li>";
            echo "<li>Student 1: <code>hz202305178@wmsu.edu.ph</code> / <code>password123</code></li>";
            echo "<li>Student 2: <code>eh202202743@wmsu.edu.ph</code> / <code>password123</code></li>";
            echo "</ul>";
            echo '<a href="?run=setup" class="btn">Run Setup Now</a>';
        }
        ?>
    </div>
</body>
</html>
