<style>
    @media (max-width: 768px) {
        .auth-grid {
            grid-template-columns: 1fr !important;
            height: auto !important;
            max-height: none !important;
        }
        .auth-left {
            padding: 30px 20px !important;
            min-height: 200px !important;
        }
        .auth-left h2 {
            font-size: 22px !important;
        }
        .auth-left p {
            font-size: 12px !important;
        }
        .auth-logo-top {
            width: 40px !important;
            height: 40px !important;
        }
        .auth-logo-top img {
            width: 40px !important;
            height: 40px !important;
        }
        .auth-logo-main {
            width: 100px !important;
            height: 100px !important;
            margin-bottom: 15px !important;
        }
        .auth-logo-main img {
            width: 100px !important;
            height: 100px !important;
        }
        .auth-right {
            padding: 30px 20px !important;
        }
        .auth-form-container {
            max-width: 100% !important;
        }
        .auth-right h2 {
            font-size: 20px !important;
        }
        .form-group {
            margin-bottom: 15px !important;
        }
        @media (max-width: 480px) {
            .auth-container {
                padding: 10px !important;
                min-height: 100vh !important;
            }
            .auth-left {
                display: none;
            }
            .auth-grid {
                max-width: 100% !important;
            }
            .auth-right {
                padding: 20px 15px !important;
            }
            .auth-form-container {
                max-width: 100% !important;
            }
        }
    }
</style>

<div class="auth-container" style="min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div class="auth-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-radius: 15px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.2); background: white; width: 100%; max-width: 1200px; height: 600px;">
        <!-- Left Side - Branding with Background Image -->
        <div class="auth-left" style="background: linear-gradient(135deg, rgba(139, 0, 0, 0.9) 0%, rgba(96, 0, 0, 0.9) 100%), url('img/login-bg.svg') center/cover; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; color: white; position: relative; overflow: hidden;">
            <!-- WMSU Logo -->
            <div class="auth-logo-top" style="position: absolute; top: 20px; left: 20px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <img src="images/logo.png" alt="WMSU Logo" style="width: 50px; height: 50px; object-fit: contain;">
            </div>

            <!-- Crimson Study Squad Logo -->
            <div class="auth-logo-main" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px; box-shadow: 0 15px 40px rgba(0,0,0,0.3); position: relative; z-index: 10;">
                <img src="images/logo.png" alt="Crimson Study Squad" style="width: 150px; height: 150px; object-fit: contain;">
            </div>

            <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 12px; text-align: center;">Crimson Study Squad</h2>
            <p style="font-size: 14px; opacity: 0.95; line-height: 1.5; max-width: 300px; text-align: center;">Continue your learning journey with your study group. Sign in to access your courses.</p>

            <div style="position: absolute; bottom: 20px; text-align: center; width: 100%;">
                <p style="font-size: 12px; opacity: 0.85;">Western Mindanao State University</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-right" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; overflow-y: auto;">
            <div class="auth-form-container" style="width: 100%; max-width: 350px;">
                <h2 style="color: #8B0000; margin-bottom: 8px; text-align: center; font-size: 24px; font-weight: 700;">Welcome Back</h2>
                <p style="text-align: center; color: #666; margin-bottom: 25px; font-size: 13px;">Sign in to continue your study journey</p>

                <form action="handlers/login_handler.php" method="POST">
                    <div class="form-group">
                        <label>WMSU Email</label>
                        <input type="email" name="email" placeholder="your.name@wmsu.edu.ph" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-group">
                            <input type="password" name="password" class="password-input" placeholder="Enter your password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                        <label style="display: flex; align-items: center; margin: 0;">
                            <input type="checkbox" name="remember_me" style="width: 16px; height: 16px; margin-right: 8px; cursor: pointer;">
                            <span style="font-size: 12px; color: #666;">Remember me</span>
                        </label>
                        <a href="?page=forgot_password" style="color: #8B0000; text-decoration: none; font-size: 12px; font-weight: 600;">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn" style="margin-bottom: 15px;">Sign In</button>
                </form>

                <div style="text-align: center; margin: 15px 0;">
                    <p style="color: #999; font-size: 12px; margin: 0;">OR</p>
                </div>

                <button type="button" class="btn" style="background: white; color: #333; border: 1.5px solid #e0e0e0; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px;">
                    <span style="font-size: 16px;"><i class="fas fa-lock"></i></span> Google
                </button>

                <p style="text-align: center; color: #666; font-size: 13px;">
                    Don't have an account? <a href="?page=register" style="color: #8B0000; font-weight: 600; text-decoration: none;">Create Account</a>
                </p>
            </div>
        </div>
    </div>
</div>
