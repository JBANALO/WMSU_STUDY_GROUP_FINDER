<?php
function sendNotification($pdo, $user_id, $type, $title, $message, $related_id = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, related_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$user_id, $type, $title, $message, $related_id]);
    } catch(PDOException $e) {
        error_log("Notification error: " . $e->getMessage());
        return false;
    }
}

function notifyAdminsNewGroup($pdo, $group_id, $group_name, $creator_name) {
    // Admin is determined by username or email starting with 'admin'
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username LIKE 'admin%' OR email LIKE 'admin%'");
    $stmt->execute();
    $admins = $stmt->fetchAll();
    
    foreach ($admins as $admin) {
        sendNotification(
            $pdo,
            $admin['id'],
            'group',
            'New Group Request',
            "{$creator_name} created a new study group: {$group_name}",
            $group_id
        );
    }
}

function notifyUserGroupApproved($pdo, $user_id, $group_name, $group_id) {
    sendNotification(
        $pdo,
        $user_id,
        'group',
        'Group Approved!',
        "Your study group '{$group_name}' has been approved by admin.",
        $group_id
    );
}

function notifyUserGroupDeclined($pdo, $user_id, $group_name) {
    sendNotification(
        $pdo,
        $user_id,
        'group',
        'Group Declined',
        "Your study group '{$group_name}' has been declined by admin.",
        null
    );
}

function notifyGroupNewMember($pdo, $group_id, $new_member_name) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT gm.user_id, sg.group_name
        FROM group_members gm
        JOIN study_groups sg ON gm.group_id = sg.id
        WHERE gm.group_id = ? AND gm.user_id != (
            SELECT user_id FROM group_members WHERE group_id = ? ORDER BY joined_at DESC LIMIT 1
        )
    ");
    $stmt->execute([$group_id, $group_id]);
    $members = $stmt->fetchAll();
    
    if (!empty($members)) {
        $group_name = $members[0]['group_name'];
        foreach ($members as $member) {
            sendNotification(
                $pdo,
                $member['user_id'],
                'join',
                'New Member',
                "{$new_member_name} joined {$group_name}",
                $group_id
            );
        }
    }
}
?>