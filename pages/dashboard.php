<?php
$user_id = $_SESSION['user_id'];
$subject_filter = $_GET['subject'] ?? null;
?>

<style>
    @media (max-width: 768px) {
        .dashboard {
            padding: 15px !important;
        }
        .dashboard > div:first-child {
            flex-direction: column !important;
            gap: 15px !important;
        }
        .dashboard > div:first-child h2 {
            font-size: 22px !important;
        }
        .dashboard > div:first-child button {
            width: 100% !important;
        }
        .dashboard-grid-2col {
            grid-template-columns: 1fr !important;
        }
        .group-card {
            padding: 15px !important;
        }
        .group-card h4 {
            font-size: 14px !important;
        }
        .form-group {
            margin-bottom: 12px !important;
        }
    }
    @media (max-width: 480px) {
        .dashboard {
            padding: 10px !important;
        }
        .dashboard > div:first-child h2 {
            font-size: 18px !important;
        }
        .group-card {
            padding: 12px !important;
            border-left-width: 3px !important;
        }
        .group-card h4 {
            font-size: 13px !important;
        }
        .group-card p {
            font-size: 12px !important;
        }
        .btn {
            font-size: 11px !important;
            padding: 6px 12px !important;
        }
    }
</style>

<?php
$user_id = $_SESSION['user_id'];
$subject_filter = $_GET['subject'] ?? null;

// Get available groups (approved groups from OTHER users)
$query = "
    SELECT sg.*, u.first_name, u.last_name,
    (SELECT COUNT(*) FROM group_members WHERE group_id = sg.id) as member_count,
    (SELECT COUNT(*) FROM group_members WHERE group_id = sg.id AND user_id = ?) as is_member
    FROM study_groups sg
    JOIN users u ON sg.creator_id = u.id
    WHERE sg.status = 'approved' 
    AND sg.creator_id != ?
";
$params = [$user_id, $user_id];

if ($subject_filter) {
    $query .= " AND sg.subject = ?";
    $params[] = $subject_filter;
}

$query .= " ORDER BY sg.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$available_groups = $stmt->fetchAll();

// Get all subjects for filtering
$stmt = $pdo->prepare("SELECT DISTINCT subject FROM study_groups WHERE status = 'approved' ORDER BY subject ASC");
$stmt->execute();
$all_subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get my created groups (pending approval)
$stmt = $pdo->prepare("SELECT *, (SELECT COUNT(*) FROM group_members WHERE group_id = id) as member_count FROM study_groups WHERE creator_id = ? AND status = 'pending' ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$pending_groups = $stmt->fetchAll();

// Get my created groups (already approved)
$stmt = $pdo->prepare("SELECT *, (SELECT COUNT(*) FROM group_members WHERE group_id = id) as member_count FROM study_groups WHERE creator_id = ? AND status = 'approved' ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$my_approved_groups = $stmt->fetchAll();

// Get declined groups (rejected by admin)
$stmt = $pdo->prepare("SELECT * FROM study_groups WHERE creator_id = ? AND status = 'declined' ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$declined_groups = $stmt->fetchAll();
?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: white; font-size: 28px;">Study Group Dashboard</h2>
        <button onclick="openModal('createGroupModal')" class="btn" style="width: auto; padding: 12px 24px; font-size: 14px;">
            + Create Group
        </button>
    </div>

    <!-- Subject Filter -->
    <?php if (!empty($all_subjects)): ?>
        <div style="background: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="color: #333; font-weight: 600; margin: 0 0 12px 0; font-size: 13px;">Filter by Subject:</p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <a href="?page=dashboard" style="display: inline-block; padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; text-decoration: none; background: <?= !$subject_filter ? '#8B0000' : '#f0f0f0' ?>; color: <?= !$subject_filter ? 'white' : '#333' ?>; cursor: pointer; transition: all 0.3s;">
                    All Subjects
                </a>
                <?php foreach ($all_subjects as $subject): ?>
                    <a href="?page=dashboard&subject=<?= urlencode($subject) ?>" style="display: inline-block; padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; text-decoration: none; background: <?= $subject_filter === $subject ? '#8B0000' : '#f0f0f0' ?>; color: <?= $subject_filter === $subject ? 'white' : '#333' ?>; cursor: pointer; transition: all 0.3s;">
                        <?= htmlspecialchars($subject) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="dashboard-grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Left Column -->
        <div>
            <!-- Group Available -->
            <div class="section" style="margin-bottom: 30px;">
                <h3 style="color: #8B0000; font-size: 20px; margin-bottom: 20px;"><i class="fas fa-book"></i> Group Available</h3>
                <?php if (empty($available_groups)): ?>
                    <p style="color: #999;">No groups available</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach ($available_groups as $group): ?>
                            <div class="group-card" style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #8B0000; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <h4 style="color: #333; font-size: 16px; margin-bottom: 10px; font-weight: bold;"><?= htmlspecialchars($group['group_name']) ?></h4>
                                <p style="color: #666; font-size: 13px; margin-bottom: 10px;"><?= htmlspecialchars(substr($group['description'], 0, 60)) ?>...</p>
                                <p style="color: #999; font-size: 12px; margin-bottom: 10px;"><strong>Subject:</strong> <?= htmlspecialchars($group['subject']) ?></p>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: #999; font-size: 12px;"><i class="fas fa-users"></i> <?= $group['member_count'] ?> members</span>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn" style="width: auto; padding: 8px 16px; font-size: 12px;" onclick="viewGroupDetails(<?= $group['id'] ?>)">View Details</button>
                                        <?php if ($group['is_member']): ?>
                                            <span style="background: #28a745; color: white; padding: 8px 12px; border-radius: 6px; font-size: 11px; font-weight: bold;">✓ Joined</span>
                                        <?php else: ?>
                                            <form action="handlers/join_group_handler.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                                <button type="submit" class="btn" style="width: auto; padding: 8px 16px; font-size: 12px; background: #28a745;">Join</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- My Approved Groups (groups you created and admin approved) -->
            <div class="section">
                <h3 style="color: #28a745; font-size: 20px; margin-bottom: 20px;"><i class="fas fa-check-circle"></i> My Approved Groups</h3>
                <?php if (empty($my_approved_groups)): ?>
                    <p style="color: #999;">No approved groups created</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach ($my_approved_groups as $group): ?>
                            <div class="group-card" style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #28a745; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4 style="color: #333; font-size: 16px; margin-bottom: 5px; font-weight: bold;"><?= htmlspecialchars($group['group_name']) ?></h4>
                                        <p style="color: #666; font-size: 13px; margin-bottom: 5px;"><?= htmlspecialchars(substr($group['description'], 0, 60)) ?>...</p>
                                        <p style="color: #999; font-size: 12px;"><strong>Subject:</strong> <?= htmlspecialchars($group['subject']) ?></p>
                                        <p style="color: #999; font-size: 12px;"><i class="fas fa-users"></i> <?= $group['member_count'] ?> members</p>
                                        <button class="btn" style="width: auto; padding: 8px 16px; font-size: 12px; margin-top: 10px;" onclick="viewGroupDetails(<?= $group['id'] ?>)">View Details</button>
                                    </div>
                                    <span style="background: #28a745; color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold;">Approved</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Pending Groups (waiting for admin approval) -->
            <div class="section" style="margin-bottom: 30px;">
                <h3 style="color: #8B0000; font-size: 20px; margin-bottom: 20px;"><i class="fas fa-hourglass-half"></i> Pending Groups (Waiting Admin Approval)</h3>
                <?php if (empty($pending_groups)): ?>
                    <p style="color: #999;">No pending groups</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach ($pending_groups as $group): ?>
                            <div class="group-card" style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #FFA500; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div>
                                        <h4 style="color: #333; font-size: 16px; margin-bottom: 5px; font-weight: bold;"><?= htmlspecialchars($group['group_name']) ?></h4>
                                        <p style="color: #999; font-size: 12px;"><?= htmlspecialchars($group['subject']) ?></p>
                                        <p style="color: #999; font-size: 12px;"><i class="fas fa-users"></i> <?= $group['member_count'] ?> members</p>
                                    </div>
                                    <span style="background: #FFA500; color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold;">Pending</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Declined Groups -->
            <div class="section">
                <h3 style="color: #8B0000; font-size: 20px; margin-bottom: 20px;"><i class="fas fa-times-circle"></i> Declined Groups</h3>
                <?php if (empty($declined_groups)): ?>
                    <p style="color: #999;">No declined groups</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach ($declined_groups as $group): ?>
                            <div class="group-card" style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #dc3545; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4 style="color: #333; font-size: 16px; margin-bottom: 5px; font-weight: bold;"><?= htmlspecialchars($group['group_name']) ?></h4>
                                        <p style="color: #666; font-size: 12px; margin: 5px 0; font-style: italic;">Reason: <?= htmlspecialchars($group['decline_reason'] ?? 'No reason provided') ?></p>
                                        <p style="color: #999; font-size: 12px;"><?= htmlspecialchars($group['subject']) ?></p>
                                        <div style="display: flex; gap: 8px; margin-top: 10px;">
                                            <button type="button" class="btn" style="width: auto; padding: 6px 14px; font-size: 11px; background: #FFA500;" onclick="openEditDeclinedModal(<?= $group['id'] ?>, '<?= htmlspecialchars(addslashes($group['group_name'])) ?>', '<?= htmlspecialchars(addslashes($group['description'])) ?>', '<?= htmlspecialchars(addslashes($group['subject'])) ?>')"><i class="fas fa-edit"></i> Edit & Resubmit</button>
                                            <button type="button" class="btn" style="width: auto; padding: 6px 14px; font-size: 11px; background: #dc3545;" onclick="confirmDeleteGroup(<?= $group['id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
                                        </div>
                                    </div>
                                    <span style="background: #dc3545; color: white; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold;">Declined</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div id="createGroupModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('createGroupModal')">&times;</span>
        <h2 style="color: #8B0000; margin-bottom: 20px;">Create Study Group</h2>
        <form action="handlers/create_group_handler.php" method="POST">
            <div class="form-group">
                <label>Group Name</label>
                <input type="text" name="group_name" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" required>
            </div>
            <button type="submit" class="btn">Create Group</button>
        </form>
    </div>
</div>

<script>
function viewGroupDetails(groupId) {
    window.location.href = '?page=group_details&id=' + groupId;
}

function openEditDeclinedModal(groupId, groupName, description, subject) {
    document.getElementById('edit_group_id').value = groupId;
    document.getElementById('edit_group_name').value = groupName;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_subject').value = subject;
    document.getElementById('editDeclinedModal').style.display = 'block';
}

function confirmDeleteGroup(groupId) {
    if (confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
        window.location.href = 'handlers/delete_group_handler.php?id=' + groupId;
    }
}
</script>

<!-- Edit Declined Group Modal -->
<div id="editDeclinedModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('editDeclinedModal')">&times;</span>
        <h2 style="color: #8B0000; margin-bottom: 20px;"><i class="fas fa-edit"></i> Edit & Resubmit Group</h2>
        <p style="color: #666; font-size: 13px; margin-bottom: 20px;">Make changes to your group and resubmit for admin approval.</p>
        <form action="handlers/resubmit_group_handler.php" method="POST">
            <input type="hidden" name="group_id" id="edit_group_id">
            <div class="form-group">
                <label>Group Name</label>
                <input type="text" name="group_name" id="edit_group_name" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="edit_description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" id="edit_subject" required>
            </div>
            <button type="submit" class="btn" style="background: #FFA500;"><i class="fas fa-paper-plane"></i> Resubmit for Approval</button>
        </form>
    </div>
</div>

