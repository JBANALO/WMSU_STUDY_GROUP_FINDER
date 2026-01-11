#!/usr/bin/env php
<?php
require 'config/database.php';

echo "=== Testing Notification System ===\n\n";

// Get admin user
$stmt = $pdo->query("SELECT id, username FROM users WHERE username LIKE 'admin%' LIMIT 1");
$admin = $stmt->fetch();

if (!$admin) {
    echo "No admin user found!\n";
    exit(1);
}

echo "Admin found: {$admin['username']} (ID: {$admin['id']})\n\n";

// Create a test notification
echo "Creating test notification for admin...\n";
$stmt = $pdo->prepare("
    INSERT INTO notifications (user_id, type, title, message, related_id)
    VALUES (?, 'test', 'Test Notification', 'This is a test notification with related_id', 123)
");
$stmt->execute([$admin['id']]);
echo "✓ Notification created (ID: " . $pdo->lastInsertId() . ")\n\n";

// Check unread count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
$stmt->execute([$admin['id']]);
$count = $stmt->fetch()['count'];

echo "Unread notifications for admin: {$count}\n";

// Show latest notifications
echo "\nLatest 5 notifications:\n";
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$admin['id']]);
while ($row = $stmt->fetch()) {
    $read = $row['is_read'] ? '✓' : '✗';
    echo "  [{$read}] {$row['title']} - {$row['message']} (related_id: {$row['related_id']})\n";
}
