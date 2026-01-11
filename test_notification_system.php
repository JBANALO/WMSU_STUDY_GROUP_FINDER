<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification System Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .badge {
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Notification System Test</h1>
        
        <?php
        require_once 'config/database.php';
        
        // Check if notifications table has related_id column
        echo "<h2>Database Structure Check</h2>";
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM notifications");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<table>";
            echo "<tr><th>Column</th><th>Status</th></tr>";
            
            $required_columns = ['id', 'user_id', 'type', 'title', 'message', 'related_id', 'is_read', 'created_at'];
            foreach ($required_columns as $col) {
                $exists = in_array($col, $columns);
                $status = $exists ? '<span class="success">✓ Exists</span>' : '<span class="error">✗ Missing</span>';
                echo "<tr><td>{$col}</td><td>{$status}</td></tr>";
            }
            echo "</table>";
            
        } catch (PDOException $e) {
            echo "<p class='error'>Error checking table: " . $e->getMessage() . "</p>";
        }
        
        // Get all users
        echo "<h2>User Notification Stats</h2>";
        try {
            $stmt = $pdo->query("
                SELECT 
                    u.id,
                    u.username,
                    u.email,
                    u.status,
                    COALESCE(n.total, 0) as total_notifications,
                    COALESCE(n.unread, 0) as unread_notifications
                FROM users u
                LEFT JOIN (
                    SELECT 
                        user_id,
                        COUNT(*) as total,
                        SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread
                    FROM notifications
                    GROUP BY user_id
                ) n ON u.id = n.user_id
                ORDER BY u.id
            ");
            
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th><th>Total Notifs</th><th>Unread</th></tr>";
            
            while ($user = $stmt->fetch()) {
                $badge = $user['unread_notifications'] > 0 
                    ? "<span class='badge'>{$user['unread_notifications']}</span>" 
                    : "0";
                    
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td>{$user['username']}</td>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['status']}</td>";
                echo "<td>{$user['total_notifications']}</td>";
                echo "<td>{$badge}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } catch (PDOException $e) {
            echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
        }
        
        // Recent notifications
        echo "<h2>Recent Notifications (Last 10)</h2>";
        try {
            $stmt = $pdo->query("
                SELECT 
                    n.*,
                    u.username
                FROM notifications n
                JOIN users u ON n.user_id = u.id
                ORDER BY n.created_at DESC
                LIMIT 10
            ");
            
            if ($stmt->rowCount() > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>User</th><th>Type</th><th>Title</th><th>Message</th><th>Related ID</th><th>Read</th><th>Created</th></tr>";
                
                while ($notif = $stmt->fetch()) {
                    $read = $notif['is_read'] ? '✓' : '✗';
                    echo "<tr>";
                    echo "<td>{$notif['id']}</td>";
                    echo "<td>{$notif['username']}</td>";
                    echo "<td>{$notif['type']}</td>";
                    echo "<td>{$notif['title']}</td>";
                    echo "<td>" . substr($notif['message'], 0, 50) . "...</td>";
                    echo "<td>{$notif['related_id']}</td>";
                    echo "<td>{$read}</td>";
                    echo "<td>{$notif['created_at']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='info'>No notifications found.</p>";
            }
            
        } catch (PDOException $e) {
            echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
        }
        ?>
        
        <div style="margin-top: 30px;">
            <a href="index.php?page=login" class="btn">Go to Login</a>
            <a href="index.php?page=dashboard" class="btn">Go to Dashboard</a>
        </div>
    </div>
</body>
</html>
