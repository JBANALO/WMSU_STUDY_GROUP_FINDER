<?php if (!isset($page) || ($page !== 'home' && $page !== 'register' && $page !== 'login' && $page !== 'forgot_password' && $page !== 'reset_password')): ?>
<div class="header">
    <div class="header-left">
        <div class="logo" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <img src="images/logo.png" alt="WMSU Logo" style="width: 50px; height: 50px; object-fit: contain;">
        </div>
        <h2>Crimson Study Squad</h2>
    </div>
    <div class="nav-links">
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <!-- Admin Navigation -->
                <a href="?page=admin_dashboard"><i class="fas fa-user-tie"></i> <span class="nav-text">Admin Dashboard</span></a>
                <a href="?page=dashboard"><i class="fas fa-th-large"></i> <span class="nav-text">Groups</span></a>
            <?php else: ?>
                <!-- Student Navigation -->
                <a href="?page=dashboard"><i class="fas fa-home"></i> <span class="nav-text">Home</span></a>
                <a href="?page=my_groups"><i class="fas fa-book"></i> <span class="nav-text">My Study Groups</span></a>
            <?php endif; ?>
            
            <!-- Notifications - After main nav, before profile -->
            <a href="?page=notifications" style="position: relative;" class="notification-link">
                <i class="fas fa-envelope"></i>
                <span class="nav-text">Notifications</span>
                <?php 
                require_once 'config/database.php';
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
                    $stmt->execute([$_SESSION['user_id']]);
                    $unread_count = $stmt->fetch()['count'];
                    if ($unread_count > 0): 
                ?>
                    <span class="notification-badge" style="position: absolute; top: -8px; right: -8px; background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">
                        <?= $unread_count > 99 ? '99+' : $unread_count ?>
                    </span>
                <?php 
                    endif;
                } catch(PDOException $e) {
                    // Notifications table might not exist
                }
                ?>
            </a>
            
            <!-- User Info with Dropdown -->
            <?php
            try {
                if (!isset($unread_count)) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
                    $stmt->execute([$_SESSION['user_id']]);
                    $unread_count = $stmt->fetch()['count'];
                }
            } catch(PDOException $e) {
                $unread_count = 0;
            }
            ?>
            
            <div style="position: relative; display: inline-block;">
                <button style="background: transparent; border: none; color: white; cursor: pointer; padding: 8px 12px; display: flex; align-items: center; gap: 8px; font-size: 14px;" onclick="toggleProfileMenu()" id="profileBtn">
                    <i class="fas fa-user-circle"></i> 
                    <span class="nav-text">
                    <?php if (isAdmin()): ?>
                        Admin
                    <?php else: ?>
                        <?= htmlspecialchars($_SESSION['full_name']) ?>
                    <?php endif; ?>
                    </span>
                    <i class="fas fa-caret-down"></i>
                </button>
                
                <div id="profileMenu" style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px; z-index: 1000;">
                    <a href="?page=profile" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee; transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                    <a href="?page=change_password" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee; transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                    <a href="?page=logout" style="display: block; padding: 12px 16px; color: #dc3545; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <script>
                function toggleProfileMenu() {
                    const menu = document.getElementById('profileMenu');
                    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
                }
                
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    const profileBtn = document.getElementById('profileBtn');
                    const profileMenu = document.getElementById('profileMenu');
                    if (!event.target.closest('button[id="profileBtn"]') && !event.target.closest('#profileMenu')) {
                        profileMenu.style.display = 'none';
                    }
                });
            </script>
        <?php else: ?>
            <a href="?page=login">Login</a>
            <a href="?page=register">Register</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
