<?php
$user_id = $_SESSION['user_id'];
?>

<style>
    @media (max-width: 768px) {
        .dashboard {
            padding: 15px !important;
        }
        .notification-item {
            padding: 15px !important;
        }
        .notification-item h3 {
            font-size: 14px !important;
        }
        .notification-item p {
            font-size: 12px !important;
        }
    }
    @media (max-width: 480px) {
        .dashboard {
            padding: 10px !important;
        }
        .dashboard h2 {
            font-size: 18px !important;
        }
        .notification-item {
            padding: 12px !important;
            border-left-width: 3px !important;
        }
        .notification-item h3 {
            font-size: 13px !important;
        }
        .notification-item p {
            font-size: 11px !important;
        }
    }
</style>

<?php
$user_id = $_SESSION['user_id'];

// Try to get all notifications, but handle if table doesn't exist
$notifications = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll();

    // Mark all as read
    if (!empty($notifications)) {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ? AND is_read = FALSE");
        $stmt->execute([$user_id]);
    }
} catch(PDOException $e) {
    // Table doesn't exist yet
}
?>

<div class="dashboard">
    <h2 style="color: white; margin-bottom: 30px; font-size: 28px;"><i class="fas fa-bell"></i> Notifications</h2>
    
    <?php if (empty($notifications)): ?>
        <div class="section" style="background: white; padding: 30px; text-align: center; border-radius: 10px;">
            <p style="color: #666; font-size: 16px;">No notifications yet</p>
            <p style="color: #999; font-size: 13px; margin-top: 10px;">
                <a href="setup_notifications.php" style="color: #8B0000; text-decoration: none;">Click here to initialize notifications</a>
            </p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($notifications as $notif): ?>
                <div class="notification-item section" style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid <?php 
                    if ($notif['type'] === 'account') echo '#8B0000';
                    elseif ($notif['type'] === 'group') echo '#FFA500';
                    elseif ($notif['type'] === 'join') echo '#28a745';
                    else echo '#999';
                ?>; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h3 style="color: #333; font-size: 16px; margin: 0 0 10px 0; font-weight: bold;">
                                <?php 
                                if ($notif['type'] === 'account') echo '<i class="fas fa-user"></i>';
                                elseif ($notif['type'] === 'group') echo '<i class="fas fa-book"></i>';
                                elseif ($notif['type'] === 'join') echo '<i class="fas fa-users"></i>';
                                ?>
                                <?= htmlspecialchars($notif['title']) ?>
                            </h3>
                            <p style="color: #666; font-size: 14px; margin: 0 0 10px 0;"><?= htmlspecialchars($notif['message']) ?></p>
                            <p style="color: #999; font-size: 12px; margin: 0;">
                                <?= date('M d, Y h:i A', strtotime($notif['created_at'])) ?>
                            </p>
                        </div>
                        <?php if ($notif['related_id'] && $notif['type'] === 'group'): ?>
                            <a href="?page=group_details&id=<?= $notif['related_id'] ?>" class="btn" style="width: auto; padding: 8px 15px; font-size: 12px; margin-left: 15px;">View</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
