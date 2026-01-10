<?php
$group_id = $_GET['id'] ?? null;
if (!$group_id) { 
    header("Location: index.php?page=my_groups");
    exit();
}

$stmt = $pdo->prepare("SELECT sg.*, u.first_name, u.last_name FROM study_groups sg JOIN users u ON sg.creator_id = u.id WHERE sg.id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) { 
    header("Location: index.php?page=my_groups");
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM group_members WHERE group_id = ?");
$stmt->execute([$group_id]);
$member_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->execute([$group_id, $_SESSION['user_id']]);
$is_member = (bool)$stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($group['group_name']) ?> - Announcements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #8B0000 0%, #a00000 100%);
            color: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 12px rgba(139, 0, 0, 0.15);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 2px solid #e5e7eb;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #8B0000;
            margin-bottom: 1rem;
        }

        .group-card {
            background: white;
            border: 3px solid #8B0000;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(139, 0, 0, 0.1);
        }

        .group-name {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .group-meta {
            display: flex;
            gap: 1rem;
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .btn-announcement {
            background: #8B0000;
            color: white;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-announcement:hover {
            background: #a00000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 0, 0, 0.3);
        }

        .announcements-section {
            margin-top: 2rem;
        }

        .announcement-card {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }

        .announcement-card:hover {
            border-color: #8B0000;
            box-shadow: 0 4px 12px rgba(139, 0, 0, 0.1);
        }

        .announcement-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .announcement-icon {
            width: 40px;
            height: 40px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .delete-btn {
            background: #FF6B6B;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }

        .delete-btn:hover {
            background: #ff5252;
        }

        .announcement-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .announcement-content {
            color: #666;
            font-size: 14px;
            margin: 10px 0;
            line-height: 1.6;
        }

        .announcement-attachment {
            margin-top: 10px;
        }

        .announcement-attachment img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            cursor: pointer;
        }

        .announcement-attachment a {
            display: inline-block;
            padding: 8px 16px;
            background: #8B0000;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.2s;
        }

        .announcement-attachment a:hover {
            background: #a00000;
        }

        .announcement-meta {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 16px;
            max-width: 600px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
            font-size: 2rem;
            cursor: pointer;
            color: #999;
        }

        .close-modal:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            font-family: inherit;
        }

        .due-date-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #FEE;
            color: #C00;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .group-name {
                font-size: 1.75rem;
            }

            .group-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <a href="?page=my_groups" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Groups
                </a>
                <div class="header-title">Announcements</div>
            </div>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div class="page-title">
                <i class="fas fa-bullhorn"></i> <?= htmlspecialchars($group['group_name']) ?>
            </div>
            <div class="group-meta">
                <span><i class="fas fa-book"></i> <?= htmlspecialchars($group['subject']) ?></span>
                <span>•</span>
                <span><i class="fas fa-users"></i> <?= $member_count ?> Members</span>
            </div>
        </div>

        <div class="group-card">
            <?php if ($is_member): ?>
                <button class="btn-announcement" onclick="openModal('announcementModal')">
                    <i class="fas fa-edit"></i> New Announcement
                </button>
            <?php endif; ?>

            <div class="announcements-section" id="announcementsContainer">
                <p style="text-align: center; color: #999; padding: 2rem;">Loading announcements...</p>
            </div>
        </div>
    </div>

    <div id="announcementModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('announcementModal')">&times;</span>
            <h2 style="color: #8B0000; margin-bottom: 1.5rem;"><i class="fas fa-bullhorn"></i> New Announcement</h2>
            <form id="announcementForm">
                <input type="hidden" name="group_id" value="<?= $group_id ?>">
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Type</label>
                    <select name="announcement_type" id="announcementType">
                        <option value="announcement">Announcement</option>
                        <option value="assignment">Assignment</option>
                        <option value="material">Material</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Title *</label>
                    <input type="text" name="title" required placeholder="Enter announcement title">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Content</label>
                    <textarea name="content" rows="5" placeholder="Enter announcement details..."></textarea>
                </div>
                
                <div class="form-group" id="dueDateField" style="display: none;">
                    <label><i class="fas fa-calendar"></i> Due Date</label>
                    <input type="datetime-local" name="due_date" min="<?= date('Y-m-d\TH:i') ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-paperclip"></i> Attachment (Optional)</label>
                    <input type="file" id="announcementAttachment" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                </div>
                
                <button type="submit" class="btn-announcement" style="width: 100%; justify-content: center;">
                    <i class="fas fa-paper-plane"></i> Post Announcement
                </button>
            </form>
        </div>
    </div>

    <script>
        const GROUP_ID = <?= $group_id ?>;
        const USER_ID = <?= $_SESSION['user_id'] ?>;

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        document.getElementById('announcementType').addEventListener('change', function() {
            document.getElementById('dueDateField').style.display = this.value === 'assignment' ? 'block' : 'none';
        });

        document.getElementById('announcementForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const file = document.getElementById('announcementAttachment').files[0];
            if (file) {
                formData.append('attachment', file);
            }
            
            fetch('handlers/create_announcement_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.reset();
                    closeModal('announcementModal');
                    loadAnnouncements();
                } else {
                    alert('Error: ' + (data.error || 'Failed to create announcement'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to create announcement. Please try again.');
            });
        });

        function deleteAnnouncement(announcementId) {
            if (!confirm('Are you sure you want to delete this announcement?')) return;
            
            const formData = new FormData();
            formData.append('announcement_id', announcementId);
            
            fetch('handlers/delete_announcement_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAnnouncements();
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete announcement'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete announcement');
            });
        }

        function loadAnnouncements() {
            fetch('handlers/get_announcements_handler.php?group_id=' + GROUP_ID)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('announcementsContainer');
                    
                    if (data.announcements && data.announcements.length > 0) {
                        container.innerHTML = '';
                        
                        data.announcements.forEach(announcement => {
                            const typeIcons = {
                                'announcement': '<i class="fas fa-bullhorn"></i>',
                                'assignment': '<i class="fas fa-tasks"></i>',
                                'material': '<i class="fas fa-book"></i>'
                            };
                            
                            const card = document.createElement('div');
                            card.className = 'announcement-card';
                            
                            let attachmentHTML = '';
                            if (announcement.attachment) {
                                const fileName = announcement.attachment.split('/').pop();
                                const fileExt = fileName.split('.').pop().toLowerCase();
                                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);
                                
                                if (isImage) {
                                    attachmentHTML = `
                                        <div class="announcement-attachment">
                                            <img src="${announcement.attachment}" onclick="window.open('${announcement.attachment}', '_blank');">
                                        </div>
                                    `;
                                } else {
                                    attachmentHTML = `
                                        <div class="announcement-attachment">
                                            <a href="${announcement.attachment}" download>
                                                <i class="fas fa-download"></i> ${fileName}
                                            </a>
                                        </div>
                                    `;
                                }
                            }
                            
                            let dueDateHTML = '';
                            if (announcement.due_date) {
                                const dueDate = new Date(announcement.due_date);
                                dueDateHTML = `
                                    <div class="due-date-badge">
                                        <i class="fas fa-clock"></i> Due: ${dueDate.toLocaleString()}
                                    </div>
                                `;
                            }
                            
                            let deleteButton = '';
                            if (announcement.user_id == USER_ID) {
                                deleteButton = `
                                    <button class="delete-btn" onclick="event.stopPropagation(); deleteAnnouncement(${announcement.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                `;
                            }
                            
                            card.innerHTML = `
                                <div class="announcement-header">
                                    <div class="announcement-icon">${typeIcons[announcement.announcement_type]}</div>
                                    ${deleteButton}
                                </div>
                                <div class="announcement-title">
                                    ${announcement.user_name} posted a new ${announcement.announcement_type}: ${announcement.title}
                                </div>
                                ${announcement.content ? `<div class="announcement-content">${announcement.content}</div>` : ''}
                                ${attachmentHTML}
                                ${dueDateHTML}
                                <div class="announcement-meta">
                                    Created ${announcement.date_format}
                                    ${announcement.created_at !== announcement.updated_at ? ` • Edited ${announcement.edited_format}` : ''}
                                </div>
                            `;
                            
                            container.appendChild(card);
                        });
                    } else {
                        container.innerHTML = '<p style="text-align: center; color: #999; padding: 2rem;">No announcements yet. Be the first to post!</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading announcements:', error);
                    document.getElementById('announcementsContainer').innerHTML = '<p style="text-align: center; color: #f44; padding: 2rem;">Failed to load announcements</p>';
                });
        }

        loadAnnouncements();
        setInterval(loadAnnouncements, 10000);
        
        markAsRead();
        
        function markAsRead() {
            const formData = new FormData();
            formData.append('group_id', GROUP_ID);
            
            fetch('handlers/update_last_seen_handler.php', {
                method: 'POST',
                body: formData
            }).catch(error => console.error('Error updating last seen:', error));
        }
    </script>
</body>
</html>