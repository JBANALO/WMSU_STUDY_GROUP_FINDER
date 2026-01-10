<?php
$user_id = $_SESSION['user_id'];
?>

<style>
    @media (max-width: 768px) {
        .password-container {
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
        .password-container {
            max-width: 100% !important;
            padding: 12px !important;
        }
        .password-container h1 {
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
?>

<div class="dashboard">
    <h2 style="color: white; margin-bottom: 30px; font-size: 28px;"><i class="fas fa-key"></i> Change Password</h2>
    
    <div class="password-container" style="max-width: 600px; margin: 0 auto;">
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <form action="handlers/change_password_handler.php" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">Current Password</label>
                    <div style="position: relative;">
                        <input type="password" name="current_password" id="currentPassword" required placeholder="Enter your current password" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#8B0000'" onblur="this.style.borderColor='#ddd'">
                        <button type="button" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 18px; color: #8B0000;" onclick="togglePassword('currentPassword')"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">New Password</label>
                    <div style="position: relative;">
                        <input type="password" name="new_password" id="newPassword" required placeholder="Enter new password (min 6 characters)" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#8B0000'" onblur="this.style.borderColor='#ddd'">
                        <button type="button" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 18px; color: #8B0000;" onclick="togglePassword('newPassword')"><i class="fas fa-eye"></i></button>
                    </div>
                    <p style="font-size: 12px; color: #666; margin-top: 5px;"><i class="fas fa-info-circle"></i> Password must be at least 6 characters long</p>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">Confirm New Password</label>
                    <div style="position: relative;">
                        <input type="password" name="confirm_password" id="confirmPassword" required placeholder="Confirm new password" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s;" onfocus="this.style.borderColor='#8B0000'" onblur="this.style.borderColor='#ddd'">
                        <button type="button" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 18px; color: #8B0000;" onclick="togglePassword('confirmPassword')"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 12px; background: #8B0000; color: white; font-size: 16px; font-weight: bold; border: none; border-radius: 6px; cursor: pointer; transition: background 0.2s; margin-bottom: 10px;" onmouseover="this.style.background='#a00000'" onmouseout="this.style.background='#8B0000'">
                    <i class="fas fa-save"></i> Update Password
                </button>

                <a href="?page=profile" style="display: block; text-align: center; padding: 12px; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#5a6268'" onmouseout="this.style.background='#6c757d'">
                    Back to Profile
                </a>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
