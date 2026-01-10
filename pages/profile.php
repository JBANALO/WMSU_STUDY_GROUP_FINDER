<?php
$user_id = $_SESSION['user_id'];
?>

<style>
    @media (max-width: 768px) {
        .profile-container {
            max-width: 100% !important;
            padding: 15px !important;
        }
        .form-group {
            margin-bottom: 15px !important;
        }
        .btn {
            font-size: 13px !important;
            padding: 10px 16px !important;
        }
    }
    @media (max-width: 480px) {
        .profile-container {
            max-width: 100% !important;
            padding: 12px !important;
        }
        .profile-container h1 {
            font-size: 18px !important;
        }
        .form-group label {
            font-size: 12px !important;
        }
        .form-group input {
            font-size: 14px !important;
            padding: 8px !important;
        }
        .btn {
            font-size: 12px !important;
            padding: 8px 12px !important;
            width: 100% !important;
        }
    }
</style>

<?php
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<div class="dashboard">
    <h2 style="color: white; margin-bottom: 30px; font-size: 28px;"><i class="fas fa-user-circle"></i> My Profile</h2>
    
    <div class="profile-container" style="max-width: 600px; margin: 0 auto;">
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <form action="handlers/update_profile_handler.php" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#8B0000'" onblur="this.style.borderColor='#ddd'">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#8B0000'" onblur="this.style.borderColor='#ddd'">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#8B0000'" onblur="this.style.borderColor='#ddd'">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">Username</label>
                    <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 6px; font-size: 14px; box-sizing: border-box; background: #f9f9f9; color: #999;">
                    <p style="font-size: 12px; color: #999; margin-top: 5px;"><i class="fas fa-info-circle"></i> Username cannot be changed</p>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">Account Status</label>
                    <div style="padding: 12px; border-radius: 6px; background: #e8f5e9; color: #2e7d32; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> <?= ucfirst($user['status']) ?>
                    </div>
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 12px; background: #8B0000; color: white; font-size: 16px; font-weight: bold; border: none; border-radius: 6px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#a00000'" onmouseout="this.style.background='#8B0000'">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>

            <hr style="border: none; border-top: 2px solid #eee; margin: 30px 0;">

            <div style="text-align: center;">
                <a href="?page=change_password" style="display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#0056b3'" onmouseout="this.style.background='#007bff'">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
        </div>
    </div>
</div>
