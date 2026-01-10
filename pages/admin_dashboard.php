<?php
// Admin dashboard - pending approvals
$user_id = $_SESSION['user_id'];
?>

<style>
    @media (max-width: 768px) {
        .dashboard {
            padding: 15px !important;
        }
        .dashboard-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 15px !important;
        }
        .section {
            padding: 15px !important;
        }
        table {
            font-size: 12px !important;
        }
        th, td {
            padding: 8px !important;
        }
        .btn {
            padding: 6px 12px !important;
            font-size: 11px !important;
            margin-bottom: 5px !important;
        }
    }
    @media (max-width: 480px) {
        .dashboard {
            padding: 10px !important;
        }
        .dashboard-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        .section {
            padding: 12px !important;
        }
        .section h3 {
            font-size: 15px !important;
        }
        table {
            font-size: 11px !important;
            width: 100% !important;
            overflow-x: auto !important;
        }
        th, td {
            padding: 6px !important;
            word-break: break-word !important;
        }
        .btn {
            padding: 5px 10px !important;
            font-size: 10px !important;
            display: block !important;
            width: 100% !important;
            margin-bottom: 5px !important;
        }
        thead {
            display: none !important;
        }
        tbody tr {
            display: block !important;
            margin-bottom: 10px !important;
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            padding: 10px !important;
        }
        tbody td {
            display: block !important;
            text-align: left !important;
            padding-left: 100px !important;
            position: relative !important;
        }
        tbody td:before {
            content: attr(data-label) !important;
            position: absolute !important;
            left: 10px !important;
            font-weight: bold !important;
            width: 90px !important;
        }
    }
</style>

<?php
// Admin dashboard - pending approvals
$user_id = $_SESSION['user_id'];

// Get pending user registrations
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = 'pending' ORDER BY created_at DESC");
$stmt->execute();
$pending_users = $stmt->fetchAll();

// Get approved users
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = 'approved' ORDER BY created_at DESC");
$stmt->execute();
$approved_users = $stmt->fetchAll();

// Get declined users
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = 'declined' ORDER BY created_at DESC");
$stmt->execute();
$declined_users = $stmt->fetchAll();

// Get pending group creations
$stmt = $pdo->prepare("
    SELECT sg.*, u.first_name, u.last_name 
    FROM study_groups sg
    JOIN users u ON sg.creator_id = u.id
    WHERE sg.status = 'pending'
    ORDER BY sg.created_at DESC
");
$stmt->execute();
$pending_groups = $stmt->fetchAll();

// Get approved groups
$stmt = $pdo->prepare("
    SELECT sg.*, u.first_name, u.last_name 
    FROM study_groups sg
    JOIN users u ON sg.creator_id = u.id
    WHERE sg.status = 'approved'
    ORDER BY sg.created_at DESC
");
$stmt->execute();
$approved_groups = $stmt->fetchAll();

// Get declined groups
$stmt = $pdo->prepare("
    SELECT sg.*, u.first_name, u.last_name 
    FROM study_groups sg
    JOIN users u ON sg.creator_id = u.id
    WHERE sg.status = 'declined'
    ORDER BY sg.created_at DESC
");
$stmt->execute();
$declined_groups = $stmt->fetchAll();

// Get approved groups count
$approved_groups_count = count($approved_groups);

// Get approved users count
$approved_users_count = count($approved_users);
?>

<div class="dashboard" style="color: #333;">
    <h2 style="color: #8B0000; margin-bottom: 30px; font-size: 28px;">Admin Dashboard</h2>
    
    <!-- Statistics -->
    <div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div class="section" style="background: #f5f5f5; border-left: 4px solid #28a745;">
            <h3 style="color: #333; border-bottom-color: #28a745;">Approved Users</h3>
            <p style="font-size: 32px; font-weight: bold; color: #28a745; margin: 0;"><?= $approved_users_count ?></p>
        </div>
        <div class="section" style="background: #f5f5f5; border-left: 4px solid #28a745;">
            <h3 style="color: #333; border-bottom-color: #28a745;">Approved Groups</h3>
            <p style="font-size: 32px; font-weight: bold; color: #28a745; margin: 0;"><?= $approved_groups_count ?></p>
        </div>
        <div class="section" style="background: #fff3cd; border-left: 4px solid #FFA500;">
            <h3 style="color: #333; border-bottom-color: #FFA500;">Pending Users</h3>
            <p style="font-size: 32px; font-weight: bold; color: #FFA500; margin: 0;"><?= count($pending_users) ?></p>
        </div>
        <div class="section" style="background: #fff3cd; border-left: 4px solid #FFA500;">
            <h3 style="color: #333; border-bottom-color: #FFA500;">Pending Groups</h3>
            <p style="font-size: 32px; font-weight: bold; color: #FFA500; margin: 0;"><?= count($pending_groups) ?></p>
        </div>
    </div>

    <!-- Pending User Registrations -->
    <div class="section" style="margin-bottom: 30px;">
        <h3 style="color: #8B0000;"><i class="fas fa-hourglass-half"></i> Pending User Registrations</h3>
        <?php if (empty($pending_users)): ?>
            <p style="color: #999;">No pending user registrations</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #8B0000; color: #333; font-weight: 600;">Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #8B0000; color: #333; font-weight: 600;">Email</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #8B0000; color: #333; font-weight: 600;">Username</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #8B0000; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_users as $user): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; color: #333;"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td style="padding: 12px; color: #666;"><?= htmlspecialchars($user['email']) ?></td>
                            <td style="padding: 12px; color: #666;"><?= htmlspecialchars($user['username']) ?></td>
                            <td style="padding: 12px; text-align: center;">
                                <form action="handlers/approve_user_handler.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn" style="width: auto; padding: 8px 15px; background: #28a745; font-size: 12px; margin-right: 5px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);">Approve</button>
                                </form>
                                <button type="button" class="btn" style="width: auto; padding: 8px 15px; background: #dc3545; font-size: 12px; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);" onclick="openDeclineModal('user', <?= $user['id'] ?>)">Decline</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Approved User Registrations -->
    <div class="section" style="margin-bottom: 30px;">
        <h3 style="color: #28a745;"><i class="fas fa-check-circle"></i> Approved Users</h3>
        <?php if (empty($approved_users)): ?>
            <p style="color: #999;">No approved users</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #28a745; color: #333; font-weight: 600;">Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #28a745; color: #333; font-weight: 600;">Email</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #28a745; color: #333; font-weight: 600;">Username</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #28a745; color: #333; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approved_users as $user): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; color: #333;"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td style="padding: 12px; color: #666;"><?= htmlspecialchars($user['email']) ?></td>
                            <td style="padding: 12px; color: #666;"><?= htmlspecialchars($user['username']) ?></td>
                            <td style="padding: 12px; text-align: center;"><span style="background: #d4edda; color: #28a745; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">APPROVED</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Pending Groups -->
    <div class="section" style="margin-bottom: 30px;">
        <h3 style="color: #8B0000;"><i class="fas fa-hourglass-half"></i> Pending Group Approvals</h3>
        <?php if (empty($pending_groups)): ?>
            <p style="color: #999;">No pending group approvals</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #8B0000; color: #333; font-weight: 600;">Group Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #8B0000; color: #333; font-weight: 600;">Subject</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #8B0000; color: #333; font-weight: 600;">Created By</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #8B0000; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_groups as $group): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; color: #333;"><?= htmlspecialchars($group['group_name']) ?></td>
                            <td style="padding: 12px; color: #666;"><?= htmlspecialchars($group['subject']) ?></td>
                            <td style="padding: 12px; color: #666;"><?= htmlspecialchars($group['first_name'] . ' ' . $group['last_name']) ?></td>
                            <td style="padding: 12px; text-align: center;">
                                <form action="handlers/approve_group_handler.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn" style="width: auto; padding: 8px 15px; background: #28a745; font-size: 12px; margin-right: 5px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);">Approve</button>
                                </form>
                                <button type="button" class="btn" style="width: auto; padding: 8px 15px; background: #dc3545; font-size: 12px; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);" onclick="openDeclineModal('group', <?= $group['id'] ?>)">Decline</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Approved Groups -->
    <div class="section" style="margin-bottom: 30px;">
        <h3 style="color: #28a745;"><i class="fas fa-check-circle"></i> Approved Groups</h3>
        <?php if (empty($approved_groups)): ?>
            <p style="color: #999;">No approved groups</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #28a745; color: #333; font-weight: 600;">Group Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #28a745; color: #333; font-weight: 600;">Subject</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #28a745; color: #333; font-weight: 600;">Created By</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #28a745; color: #333; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approved_groups as $group): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; color: #333;"><?= htmlspecialchars($group['group_name']) ?></td>
                            <td style="padding: 12px; color: #666;"><?= htmlspecialchars($group['subject']) ?></td>
                            <td style="padding: 12px; color: #666;"><?= htmlspecialchars($group['first_name'] . ' ' . $group['last_name']) ?></td>
                            <td style="padding: 12px; text-align: center;"><span style="background: #d4edda; color: #28a745; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">APPROVED</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Decline Modal -->
<div id="declineModal" class="modal" style="display: none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 400px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <span class="close" style="float: right; font-size: 28px; font-weight: bold; cursor: pointer; color: #999;" onclick="closeModal('declineModal')">&times;</span>
        <h3 style="color: #8B0000; margin-bottom: 20px;">Decline Request</h3>
        <form id="declineForm" method="POST" style="display: flex; flex-direction: column;">
            <input type="hidden" name="type" id="declineType">
            <input type="hidden" name="id" id="declineId">
            <label style="margin-bottom: 10px; color: #333; font-weight: 600;">Reason for declining:</label>
            <textarea name="decline_reason" style="width: 100%; height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: Arial; resize: none; color: #333;" placeholder="Enter reason..." required></textarea>
            <button type="submit" class="btn" style="margin-top: 15px; background: #dc3545; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);">Submit Decline</button>
        </form>
    </div>
</div>

<script>
function openDeclineModal(type, id) {
    document.getElementById('declineType').value = type;
    document.getElementById('declineId').value = id;
    document.getElementById('declineModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

document.getElementById('declineForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const type = document.getElementById('declineType').value;
    const id = document.getElementById('declineId').value;
    const reason = document.querySelector('textarea[name="decline_reason"]').value;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = type === 'user' ? 'handlers/approve_user_handler.php' : 'handlers/approve_group_handler.php';
    
    const idField = document.createElement('input');
    idField.type = 'hidden';
    idField.name = type === 'user' ? 'user_id' : 'group_id';
    idField.value = id;
    
    const actionField = document.createElement('input');
    actionField.type = 'hidden';
    actionField.name = 'action';
    actionField.value = 'decline';
    
    const reasonField = document.createElement('input');
    reasonField.type = 'hidden';
    reasonField.name = 'decline_reason';
    reasonField.value = reason;
    
    form.appendChild(idField);
    form.appendChild(actionField);
    form.appendChild(reasonField);
    document.body.appendChild(form);
    form.submit();
});
</script>
