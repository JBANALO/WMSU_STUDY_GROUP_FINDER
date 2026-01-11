<?php
$group_id = $_GET['id'] ?? null;
?>

<style>
    @media (max-width: 768px) {
        .group-details-grid {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        .back-btn {
            margin-bottom: 15px !important;
            font-size: 13px !important;
        }
        .section {
            padding: 15px !important;
        }
    }
    @media (max-width: 480px) {
        .group-details-grid {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
        }
        .group-info {
            padding: 15px !important;
        }
        .group-info h2 {
            font-size: 16px !important;
        }
        .group-info h4 {
            font-size: 13px !important;
        }
        .btn {
            padding: 6px 10px !important;
            font-size: 11px !important;
            flex: 1 !important;
            min-width: 0 !important;
        }
        #messagesContainer {
            flex: 1 !important;
            min-height: 0 !important;
        }
        .modal-content {
            width: 95% !important;
            max-width: 95% !important;
        }
        .form-group input,
        .form-group textarea {
            font-size: 14px !important;
        }
    }
</style>

<?php
$group_id = $_GET['id'] ?? null;

if (!$group_id) {
    redirectTo('dashboard');
}

$stmt = $pdo->prepare("
    SELECT sg.*, u.first_name, u.last_name
    FROM study_groups sg
    JOIN users u ON sg.creator_id = u.id
    WHERE sg.id = ?
");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    redirectTo('dashboard');
}

$stmt = $pdo->prepare("
    SELECT u.id, u.first_name, u.last_name, gm.role
    FROM group_members gm
    JOIN users u ON gm.user_id = u.id
    WHERE gm.group_id = ?
    ORDER BY gm.role DESC
");
$stmt->execute([$group_id]);
$members = $stmt->fetchAll();

$is_member = false;
$user_role = null;
foreach ($members as $member) {
    if ($member['id'] == $_SESSION['user_id']) {
        $is_member = true;
        $user_role = $member['role'];
        break;
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT m.*, u.first_name, u.last_name
        FROM meetings m
        JOIN users u ON m.created_by = u.id
        WHERE m.group_id = ? AND m.meeting_date >= NOW()
        ORDER BY m.meeting_date ASC
    ");
    $stmt->execute([$group_id]);
    $upcoming_meetings = $stmt->fetchAll();
} catch(PDOException $e) {
    $upcoming_meetings = [];
}
?>

<div style="margin-bottom: 20px;">
    <a class="back-btn" href="?page=dashboard" style="display: inline-flex; align-items: center; gap: 8px; color: #8B0000; text-decoration: none; font-weight: 600; padding: 8px 16px; background: #f5f5f5; border-radius: 8px; border: 2px solid #8B0000; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='#8B0000'; this.style.color='white'" onmouseout="this.style.background='#f5f5f5'; this.style.color='#8B0000'">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="group-details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
    <div>
        <div class="group-info" style="background: white; padding: 20px; border-radius: 10px; border: 3px solid #8B0000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #8B0000; font-size: 20px; margin: 0; display: flex; align-items: center; gap: 10px;"><span style="font-size: 22px;"><i class="fas fa-calendar-alt"></i></span> Upcoming Meetings</h3>
            </div>
            <button class="btn" style="width: 100%; padding: 10px 16px; font-size: 14px; background: #8B0000; color: white; margin-bottom: 20px; font-weight: 600; cursor: pointer; border: none; border-radius: 6px;" onclick="openModal('scheduleMeetingModal')">+ Schedule Meeting</button>
            
            <?php if (!empty($upcoming_meetings)): ?>
                <?php foreach ($upcoming_meetings as $meeting): ?>
                    <?php 
                    $meeting_link = "https://meet.studyfinder.local/meeting/" . base64_encode($meeting['id'] . '|' . $group_id . '|' . time());
                    ?>
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #8B0000;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <h4 style="color: #8B0000; font-size: 14px; margin: 0; font-weight: bold; flex: 1;"><?= htmlspecialchars($meeting['title']) ?></h4>
                            <?php if ($meeting['created_by'] == $_SESSION['user_id']): ?>
                                <div style="display: flex; gap: 5px;">
                                    <button class="btn" style="padding: 4px 8px; font-size: 11px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="editMeeting(<?= $meeting['id'] ?>)">Edit</button>
                                    <button class="btn" style="padding: 4px 8px; font-size: 11px; background: #FF6B6B; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="deleteMeeting(<?= $meeting['id'] ?>)">Delete</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($meeting['description']): ?>
                            <p style="color: #666; font-size: 12px; margin: 5px 0;"><?= htmlspecialchars($meeting['description']) ?></p>
                        <?php endif; ?>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <span style="font-size: 14px;"><i class="fas fa-calendar"></i></span>
                            <span style="color: #666; font-size: 12px;"><?= date('M d, Y', strtotime($meeting['meeting_date'])) ?></span>
                            <span style="font-size: 14px; margin-left: 10px;"><i class="fas fa-clock"></i></span>
                            <span style="color: #666; font-size: 12px;"><?= date('g:i A', strtotime($meeting['meeting_date'])) ?></span>
                        </div>
                        <p style="color: #8B0000; font-size: 11px; font-weight: bold; margin: 5px 0;">
                            <?= $meeting['is_online'] ? 'Online' : '<i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($meeting['location']) ?>
                        </p>
                        <?php if ($meeting['is_online']): ?>
                            <div style="background: white; padding: 8px 12px; border-radius: 6px; margin-top: 10px; border: 1px solid #ddd;">
                                <p style="color: #666; font-size: 11px; margin: 0 0 5px 0;">Meeting Link:</p>
                                <div style="display: flex; gap: 5px;">
                                    <input type="text" value="<?= htmlspecialchars($meeting_link) ?>" id="meetingLink<?= $meeting['id'] ?>" style="flex: 1; padding: 6px 8px; font-size: 11px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;" readonly>
                                    <button class="btn" style="padding: 6px 10px; font-size: 11px; background: #8B0000; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="copyMeetingLink(<?= $meeting['id'] ?>)">Copy</button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <p style="color: #999; font-size: 10px; margin-top: 8px;">Scheduled by: <?= htmlspecialchars($meeting['first_name'] . ' ' . $meeting['last_name']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <div style="background: white; padding: 0; border-radius: 10px; border: 3px solid #8B0000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); height: 700px; display: flex; flex-direction: column; overflow: hidden;">
            <div style="background: #8B0000; color: white; padding: 20px; border-bottom: 3px solid #8B0000; flex-shrink: 0;">
                <h2 style="color: white; font-size: 20px; margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 22px;"><i class="fas fa-users"></i></span> <?= htmlspecialchars($group['group_name']) ?>
                </h2>
                
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 15px;">
                    <span style="background: rgba(255, 255, 255, 0.3); color: white; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: bold;">
                        <?= htmlspecialchars($group['subject']) ?>
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 12px; line-height: 1.6;">
                    <div>
                        <p style="margin: 0 0 5px 0;"><strong>Created by:</strong> <?= htmlspecialchars($group['first_name'] . ' ' . $group['last_name']) ?></p>
                        <p style="margin: 0 0 5px 0;"><strong>Members:</strong> <?= count($members) ?></p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0;"><strong>Status:</strong> ✓ Approved</p>
                        <p style="margin: 0 0 5px 0;"><strong>Category:</strong> Active</p>
                    </div>
                </div>

                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255, 255, 255, 0.3);">
                    <p style="color: white; font-size: 12px; line-height: 1.5; margin: 0;"><?= htmlspecialchars($group['description']) ?></p>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <form action="handlers/toggle_notification_handler.php" method="POST" style="flex: 1;">
                        <input type="hidden" name="group_id" value="<?= $group_id ?>">
                        <button type="submit" class="btn" style="width: 100%; padding: 8px 12px; font-size: 12px; background: white; color: #8B0000; font-weight: 600; border: none; border-radius: 6px; cursor: pointer;"><i class="fas fa-bell"></i> Enable Notification</button>
                    </form>
                    <?php if ($group['creator_id'] == $_SESSION['user_id']): ?>
                        <button type="button" class="btn" style="padding: 8px 12px; font-size: 12px; background: #dc3545; color: white; font-weight: 600; border: none; border-radius: 6px; cursor: pointer;" onclick="confirmDeleteGroup(<?= $group_id ?>)"><i class="fas fa-trash"></i> Delete Group</button>
                    <?php elseif ($is_member): ?>
                        <form action="handlers/leave_group_handler.php" method="POST" style="flex: 1;">
                            <input type="hidden" name="group_id" value="<?= $group_id ?>">
                            <button type="submit" class="btn" style="width: 100%; padding: 8px 12px; font-size: 12px; background: #FFD700; color: #333; font-weight: 600; border: none; border-radius: 6px; cursor: pointer;"><i class="fas fa-sign-out-alt"></i> Leave Group</button>
                        </form>
                    <?php else: ?>
                        <form action="handlers/join_group_handler.php" method="POST" style="display: flex; flex: 1;">
                            <input type="hidden" name="group_id" value="<?= $group_id ?>">
                            <button type="submit" class="btn" style="flex: 1; padding: 8px 12px; font-size: 12px; background: #FFD700; color: #333; font-weight: 600; border: none; border-radius: 6px; cursor: pointer;"><i class="fas fa-plus-circle"></i> Join Group</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div style="padding: 20px; border-bottom: 1px solid #e8e8e8; flex-shrink: 0;">
                <h4 style="color: #8B0000; font-size: 14px; margin: 0 0 10px 0; font-weight: bold;">Members</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php foreach ($members as $member): ?>
                        <span style="background: #e8e8e8; color: #333; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                            <?= htmlspecialchars($member['first_name']) ?>
                            <?php if ($member['id'] == $_SESSION['user_id']): ?>
                                (You)
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div id="messagesContainer" style="flex: 1; overflow-y: auto; padding: 20px; background: #f9f9f9; display: flex; flex-direction: column; min-height: 0;">
                <p style="color: #999; text-align: center; padding: 20px;">No messages yet. Start the conversation!</p>
            </div>

            <div style="padding: 15px 20px; border-top: 1px solid #e8e8e8; background: white; flex-shrink: 0;">
                <div id="filePreview" style="display: none; font-size: 12px; color: #666; margin-bottom: 8px; padding: 8px 12px; background: #f0f0f0; border-radius: 6px; align-items: center; gap: 8px;">
                    <span><i class="fas fa-paperclip"></i></span> <span id="fileName" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></span> 
                    <a href="#" id="clearFile" style="color: #8B0000; text-decoration: none; font-weight: bold; font-size: 16px;">×</a>
                </div>
                <div style="display: flex; gap: 8px; align-items: flex-end;">
                    <label style="cursor: pointer; padding: 0; background: #8B0000; color: white; border: none; border-radius: 50%; font-size: 16px; display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; flex-shrink: 0; transition: background 0.2s;" onmouseover="this.style.background='#a00000'" onmouseout="this.style.background='#8B0000'">
                        <i class="fas fa-paperclip"></i>
                        <input type="file" id="fileInput" style="display: none;" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                    </label>
                    <textarea id="messageInput" placeholder="Type a message..." rows="1" style="flex: 1; padding: 12px 14px; border: 2px solid #ddd; border-radius: 20px; font-size: 14px; outline: none; resize: none; min-height: 44px; max-height: 150px; font-family: inherit; line-height: 1.5; overflow-y: auto; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#8B0000'" onblur="this.style.borderColor='#ddd'"></textarea>
                    <button id="sendBtn" type="button" style="background: #8B0000; color: white; padding: 0; border: none; border-radius: 50%; cursor: pointer; font-size: 16px; font-weight: bold; width: 44px; height: 44px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='#a00000'" onmouseout="this.style.background='#8B0000'"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="scheduleMeetingModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close-modal" onclick="closeModal('scheduleMeetingModal')">&times;</span>
        <h2 style="color: #8B0000; margin-bottom: 20px;"><i class="fas fa-calendar-plus"></i> Schedule Meeting</h2>
        <form action="handlers/schedule_meeting_handler.php" method="POST">
            <input type="hidden" name="group_id" value="<?= $group_id ?>">
            <div class="form-group">
                <label>Meeting Title *</label>
                <input type="text" name="title" required placeholder="e.g., Math 101 Review">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Add notes or agenda for the meeting..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif;"></textarea>
            </div>
            <div class="form-group">
                <label>Meeting Date *</label>
                <input type="date" name="meeting_date" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Meeting Time *</label>
                <input type="time" name="meeting_time" required>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_online" value="1" checked>
                    Online Meeting
                </label>
            </div>
            <div class="form-group" id="locationField" style="display: none;">
                <label>Location</label>
                <input type="text" name="location" placeholder="Meeting location">
            </div>
            <button type="submit" class="btn" style="width: 100%; padding: 12px; background: #8B0000; color: white; font-size: 14px; font-weight: bold; border: none; border-radius: 6px; cursor: pointer;">Schedule Meeting</button>
        </form>
    </div>
</div>

<script>
const GROUP_ID = <?= $group_id ?>;
const USER_ID = <?= $_SESSION['user_id'] ?>;

document.querySelector('input[name="is_online"]').addEventListener('change', function() {
    document.getElementById('locationField').style.display = this.checked ? 'none' : 'block';
});

function copyMeetingLink(meetingId) {
    const linkInput = document.getElementById('meetingLink' + meetingId);
    linkInput.select();
    document.execCommand('copy');
    alert('Meeting link copied to clipboard!');
}

function editMeeting(meetingId) {
    alert('Edit feature coming soon! Meeting ID: ' + meetingId);
}

function deleteMeeting(meetingId) {
    if (confirm('Are you sure you want to delete this meeting?')) {
        window.location.href = 'handlers/delete_meeting_handler.php?id=' + meetingId + '&group_id=' + GROUP_ID;
    }
}

function confirmDeleteGroup(groupId) {
    if (confirm('Are you sure you want to delete this group? This action cannot be undone. All meetings and messages will be deleted.')) {
        window.location.href = 'handlers/delete_group_handler.php?id=' + groupId;
    }
}

function markMessagesAsRead() {
    const formData = new FormData();
    formData.append('group_id', GROUP_ID);
    
    fetch('handlers/update_last_seen_handler.php', {
        method: 'POST',
        body: formData
    }).catch(error => console.error('Error updating last seen:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    setInterval(loadMessages, 2000);
    
    markMessagesAsRead();
    
    const sendBtn = document.getElementById('sendBtn');
    const messageInput = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const clearFile = document.getElementById('clearFile');
    
    if (sendBtn && messageInput) {
        sendBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sendMessage();
        });
        
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        messageInput.addEventListener('input', function() {
            this.style.height = '44px';
            const newHeight = Math.min(Math.max(this.scrollHeight, 44), 150);
            this.style.height = newHeight + 'px';
        });
    }
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                filePreview.style.display = 'flex';
            }
        });
    }
    
    if (clearFile) {
        clearFile.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.value = '';
            filePreview.style.display = 'none';
        });
    }
});

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const message = messageInput.value.trim();
    const file = fileInput.files[0];
    
    if (!message && !file) return;
    
    const formData = new FormData();
    formData.append('group_id', GROUP_ID);
    formData.append('message', message);
    if (file) {
        formData.append('attachment', file);
    }
    
    fetch('handlers/send_message_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.status === 401) {
            window.location.href = '?page=login';
            return null;
        }
        return response.json();
    })
    .then(data => {
        if (!data) return;
        
        if (data.success) {
            messageInput.value = '';
            messageInput.style.height = '44px';
            fileInput.value = '';
            document.getElementById('filePreview').style.display = 'none';
            loadMessages();
            markMessagesAsRead();
        } else {
            alert('Error: ' + (data.error || 'Failed to send message'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send message. Please try again.');
    });
}

function loadMessages() {
    fetch('handlers/get_messages_handler.php?group_id=' + GROUP_ID)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('messagesContainer');
            const sendBtn = document.getElementById('sendBtn');
            const messageInput = document.getElementById('messageInput');
            
            if (sendBtn && !data.is_member) {
                sendBtn.style.display = 'none';
                messageInput.style.display = 'none';
            }
            
            if (data.messages && data.messages.length > 0) {
                container.innerHTML = '';
                data.messages.forEach(msg => {
                    if (msg.message_type === 'system') {
                        const sysDiv = document.createElement('div');
                        sysDiv.style.marginBottom = '10px';
                        sysDiv.style.display = 'flex';
                        sysDiv.style.justifyContent = 'center';
                        
                        const sysContent = document.createElement('div');
                        sysContent.style.padding = '8px 12px';
                        sysContent.style.borderRadius = '8px';
                        sysContent.style.fontSize = '11px';
                        sysContent.style.backgroundColor = '#f0f0f0';
                        sysContent.style.color = '#666';
                        sysContent.style.fontStyle = 'italic';
                        sysContent.innerHTML = msg.message;
                        
                        sysDiv.appendChild(sysContent);
                        container.appendChild(sysDiv);
                        return;
                    }
                    
                    const messageDiv = document.createElement('div');
                    const isOwn = msg.user_id == USER_ID;
                    messageDiv.style.marginBottom = '10px';
                    messageDiv.style.display = 'flex';
                    messageDiv.style.justifyContent = isOwn ? 'flex-end' : 'flex-start';
                    
                    const msgContent = document.createElement('div');
                    msgContent.style.maxWidth = '70%';
                    msgContent.style.padding = '8px 12px';
                    msgContent.style.borderRadius = '8px';
                    msgContent.style.fontSize = '12px';
                    msgContent.style.wordWrap = 'break-word';
                    msgContent.style.backgroundColor = isOwn ? '#8B0000' : '#e8e8e8';
                    msgContent.style.color = isOwn ? 'white' : '#333';
                    
                    let contentHTML = `<strong>${msg.user_name}</strong><br>${msg.message || ''}`;
                    if (msg.attachment && msg.attachment.trim()) {
                        const attachmentPath = msg.attachment;
                        const fileName = attachmentPath.split('/').pop();
                        const fileExt = fileName.split('.').pop().toLowerCase();
                        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);
                        
                        if (isImage) {
                            contentHTML += `<br><img src="${attachmentPath}" style="max-width: 100%; max-height: 200px; margin-top: 5px; border-radius: 4px; cursor: pointer;" onclick="window.open('${attachmentPath}', '_blank');">`;
                        } else {
                            contentHTML += `<br><a href="${attachmentPath}" download style="display: inline-block; margin-top: 5px; padding: 4px 8px; background: ${isOwn ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.1)'}; border-radius: 4px; text-decoration: none; color: ${isOwn ? 'white' : '#333'}; font-size: 11px;"><i class="fas fa-paperclip"></i> ${fileName}</a>`;
                        }
                    }
                    contentHTML += `<br><span style="font-size: 10px; opacity: 0.7;">${msg.time_format}</span>`;
                    msgContent.innerHTML = contentHTML;
                    
                    messageDiv.appendChild(msgContent);
                    container.appendChild(messageDiv);
                });
                container.scrollTop = container.scrollHeight;
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
}
</script>