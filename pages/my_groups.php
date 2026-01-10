<?php
$user_id = $_SESSION['user_id'];
?>

<style>
    @media (max-width: 768px) {
        .dashboard {
            padding: 15px !important;
        }
        .groups-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
            gap: 15px !important;
        }
    }
    @media (max-width: 480px) {
        .dashboard {
            padding: 10px !important;
        }
        .dashboard h2 {
            font-size: 18px !important;
        }
        .groups-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        .group-card {
            padding: 15px !important;
        }
        .group-card h3 {
            font-size: 14px !important;
        }
        .group-card p {
            font-size: 12px !important;
        }
    }
</style>

<?php
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT sg.*, gm.role,
    (SELECT COUNT(*) FROM group_members WHERE group_id = sg.id) as member_count,
    (
        SELECT COUNT(*) 
        FROM group_messages gm_msg
        LEFT JOIN user_last_seen uls ON uls.group_id = sg.id AND uls.user_id = ?
        WHERE gm_msg.group_id = sg.id 
        AND gm_msg.user_id != ?
        AND (uls.last_seen_message_id IS NULL OR gm_msg.id > uls.last_seen_message_id)
    ) as unread_count
    FROM study_groups sg
    JOIN group_members gm ON sg.id = gm.group_id
    WHERE gm.user_id = ? AND sg.status = 'approved'
    ORDER BY sg.created_at DESC
");
$stmt->execute([$user_id, $user_id, $user_id]);
$my_groups = $stmt->fetchAll();
?>

<div class="dashboard">
    <h2 style="color: white; margin-bottom: 30px; font-size: 28px;"><i class="fas fa-users"></i> My Study Groups</h2>
    
    <div class="groups-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php if (empty($my_groups)): ?>
            <p style="color: white; grid-column: 1/-1;">You haven't joined any groups yet.</p>
        <?php else: ?>
            <?php foreach ($my_groups as $group): ?>
                <div style="background: white; padding: 25px; border-radius: 10px; border-left: 4px solid #8B0000; box-shadow: 0 4px 15px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s; position: relative;" onclick="viewGroupFeed(<?= $group['id'] ?>)">
                    
                    <?php if ($group['unread_count'] > 0): ?>
                        <div style="position: absolute; top: 15px; right: 15px; background: #FF4444; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(255,68,68,0.3);">
                            <?= $group['unread_count'] > 9 ? '9+' : $group['unread_count'] ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <h3 style="color: #333; font-size: 18px; margin: 0; font-weight: bold; padding-right: 30px;"><?= htmlspecialchars($group['group_name']) ?></h3>
                        <span style="color: #666; font-size: 18px;"><i class="fas fa-eye"></i></span>
                    </div>
                    <p style="color: #666; font-size: 13px; margin: 5px 0;"><i class="fas fa-book"></i> <?= htmlspecialchars($group['subject']) ?></p>
                    <p style="color: #666; font-size: 13px; margin: 5px 0;"><i class="fas fa-users"></i> <?= $group['member_count'] ?> Members</p>
                    
                    <?php if ($group['unread_count'] > 0): ?>
                        <p style="color: #FF4444; font-size: 12px; margin: 10px 0 0 0; font-weight: bold;">
                            <i class="fas fa-envelope"></i> <?= $group['unread_count'] ?> new message<?= $group['unread_count'] > 1 ? 's' : '' ?>
                        </p>
                    <?php endif; ?>
                    
                    <p style="color: #999; font-size: 11px; margin-top: 15px;"><strong>Your Role:</strong> <?= ucfirst($group['role']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function viewGroupFeed(groupId) {
    window.location.href = '?page=group_feed&id=' + groupId;
}
</script>