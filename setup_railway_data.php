<?php
/**
 * Railway Database Data Setup Script
 * This script creates initial data including admin account and test users
 */

require_once 'config/database.php';

echo "Starting Railway database data setup...\n\n";

try {
    // Check if tables exist
    $tables = ['users', 'study_groups', 'group_members', 'group_messages', 'meetings', 'notifications', 'user_last_seen'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() === 0) {
            die("Error: Table '$table' does not exist. Please run the schema setup first.\n");
        }
    }
    echo "✓ All required tables exist.\n\n";

    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute(['admin', 'admin@wmsu.edu.ph']);
    
    if ($stmt->rowCount() > 0) {
        echo "⚠ Admin account already exists. Skipping admin creation.\n";
        
        // Get existing user IDs
        $stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC");
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "✓ Found " . count($userIds) . " existing users\n\n";
        
        // Check if we need to add test data
        $stmt = $pdo->query("SELECT COUNT(*) FROM study_groups");
        $groupCount = $stmt->fetchColumn();
        
        if ($groupCount > 0) {
            echo "⚠ Study groups already exist. Database already has data.\n\n";
            echo "✅ Database setup completed!\n\n";
            echo "=== Login Credentials ===\n";
            echo "Admin Account:\n";
            echo "  Username: admin\n";
            echo "  Password: admin123\n";
            echo "  Email: admin@wmsu.edu.ph\n\n";
            exit(0);
        }
        
        // Create test users if needed
        echo "Creating additional test users...\n";
        $testUsers = [
            ['jdoe', 'jdoe@wmsu.edu.ph', 'John', 'Doe', 'password123'],
            ['jsmith', 'jsmith@wmsu.edu.ph', 'Jane', 'Smith', 'password123'],
            ['mgarcia', 'mgarcia@wmsu.edu.ph', 'Maria', 'Garcia', 'password123'],
            ['rbrown', 'rbrown@wmsu.edu.ph', 'Robert', 'Brown', 'password123']
        ];

        foreach ($testUsers as $user) {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$user[0], $user[1]]);
            
            if ($stmt->rowCount() === 0) {
                $hashedPassword = password_hash($user[4], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, first_name, last_name, password, status) 
                    VALUES (?, ?, ?, ?, ?, 'approved')
                ");
                $stmt->execute([$user[0], $user[1], $user[2], $user[3], $hashedPassword]);
                $userIds[] = $pdo->lastInsertId();
                echo "✓ Test user created: {$user[0]}\n";
            } else {
                echo "  User {$user[0]} already exists\n";
            }
        }
        
        // Refresh user IDs
        $stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC");
        $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "\n";
        
    } else {
        echo "Creating initial users...\n";
        
        // Create admin account
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, first_name, last_name, password, status) 
            VALUES (?, ?, ?, ?, ?, 'approved')
        ");
        $stmt->execute(['admin', 'admin@wmsu.edu.ph', 'Admin', 'User', $adminPassword]);
        $adminId = $pdo->lastInsertId();
        echo "✓ Admin account created (username: admin, password: admin123)\n";

        // Create test users
        $testUsers = [
            ['jdoe', 'jdoe@wmsu.edu.ph', 'John', 'Doe', 'password123'],
            ['jsmith', 'jsmith@wmsu.edu.ph', 'Jane', 'Smith', 'password123'],
            ['mgarcia', 'mgarcia@wmsu.edu.ph', 'Maria', 'Garcia', 'password123'],
            ['rbrown', 'rbrown@wmsu.edu.ph', 'Robert', 'Brown', 'password123']
        ];

        $userIds = [$adminId];
        foreach ($testUsers as $user) {
            $hashedPassword = password_hash($user[4], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, first_name, last_name, password, status) 
                VALUES (?, ?, ?, ?, ?, 'approved')
            ");
            $stmt->execute([$user[0], $user[1], $user[2], $user[3], $hashedPassword]);
            $userIds[] = $pdo->lastInsertId();
            echo "✓ Test user created: {$user[0]}\n";
        }
        echo "\n";

        // Create sample study groups
        echo "Creating sample study groups...\n";
        $groups = [
            ['PHP Programming Study Group', 'Learn PHP from basics to advanced', 'Computer Science', $userIds[1]],
            ['Database Design Workshop', 'Master MySQL and database design principles', 'Information Technology', $userIds[2]],
            ['Web Development Bootcamp', 'Full-stack web development study sessions', 'Computer Science', $userIds[3]]
        ];

        $groupIds = [];
        foreach ($groups as $group) {
            $stmt = $pdo->prepare("
                INSERT INTO study_groups (group_name, description, subject, creator_id, status) 
                VALUES (?, ?, ?, ?, 'approved')
            ");
            $stmt->execute($group);
            $groupId = $pdo->lastInsertId();
            $groupIds[] = $groupId;
            
            // Add creator as member
            $stmt = $pdo->prepare("
                INSERT INTO group_members (group_id, user_id, role) 
                VALUES (?, ?, 'admin')
            ");
            $stmt->execute([$groupId, $group[3]]);
            
            echo "✓ Study group created: {$group[0]}\n";
        }
        echo "\n";

        // Add some members to groups
        echo "Adding members to study groups...\n";
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'member')");
        
        // Add admin to all groups
        foreach ($groupIds as $groupId) {
            $stmt->execute([$groupId, $adminId]);
        }
        
        // Add some users to first group
        $stmt->execute([$groupIds[0], $userIds[2]]);
        $stmt->execute([$groupIds[0], $userIds[3]]);
        
        // Add some users to second group
        $stmt->execute([$groupIds[1], $userIds[1]]);
        $stmt->execute([$groupIds[1], $userIds[4]]);
        
        echo "✓ Members added to study groups\n\n";

        // Create sample messages
        echo "Creating sample messages...\n";
        $messages = [
            [$groupIds[0], $userIds[1], 'Welcome everyone! Let\'s learn PHP together!', 'user'],
            [$groupIds[0], $userIds[2], 'Excited to start learning!', 'user'],
            [$groupIds[1], $userIds[2], 'First topic: Database normalization', 'user'],
            [$groupIds[2], $userIds[3], 'We\'ll cover HTML, CSS, JavaScript, and PHP', 'user']
        ];

        $stmt = $pdo->prepare("
            INSERT INTO group_messages (group_id, user_id, message, message_type) 
            VALUES (?, ?, ?, ?)
        ");
        foreach ($messages as $message) {
            $stmt->execute($message);
        }
        echo "✓ Sample messages created\n\n";

        // Create sample meetings
        echo "Creating sample meetings...\n";
        $meetings = [
            [$groupIds[0], 'PHP Basics Session', 'Introduction to PHP syntax and variables', date('Y-m-d H:i:s', strtotime('+3 days')), 'Online - Google Meet', 1, $userIds[1]],
            [$groupIds[1], 'Database Design Workshop', 'Hands-on database modeling exercise', date('Y-m-d H:i:s', strtotime('+5 days')), 'Room 301, IT Building', 0, $userIds[2]],
            [$groupIds[2], 'HTML & CSS Fundamentals', 'Building your first webpage', date('Y-m-d H:i:s', strtotime('+7 days')), 'Online - Zoom', 1, $userIds[3]]
        ];

        $stmt = $pdo->prepare("
            INSERT INTO meetings (group_id, title, description, meeting_date, location, is_online, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($meetings as $meeting) {
            $stmt->execute($meeting);
        }
        echo "✓ Sample meetings created\n\n";

        // Create sample notifications
        echo "Creating sample notifications...\n";
        $notifications = [
            [$adminId, 'welcome', 'Welcome to WMSU Study Group Finder!', 'Get started by joining or creating a study group.'],
            [$userIds[1], 'group_approved', 'Your group was approved', 'PHP Programming Study Group is now live!'],
            [$userIds[2], 'new_member', 'New member joined', 'Someone joined Database Design Workshop']
        ];

        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message) 
            VALUES (?, ?, ?, ?)
        ");
        foreach ($notifications as $notification) {
            $stmt->execute($notification);
        }
        echo "✓ Sample notifications created\n\n";
    }

    echo "✅ Database setup completed successfully!\n\n";
    echo "=== Login Credentials ===\n";
    echo "Admin Account:\n";
    echo "  Username: admin\n";
    echo "  Password: admin123\n";
    echo "  Email: admin@wmsu.edu.ph\n\n";
    echo "Test Users (all use password: password123):\n";
    echo "  - jdoe / jdoe@wmsu.edu.ph\n";
    echo "  - jsmith / jsmith@wmsu.edu.ph\n";
    echo "  - mgarcia / mgarcia@wmsu.edu.ph\n";
    echo "  - rbrown / rbrown@wmsu.edu.ph\n\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
