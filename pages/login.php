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

                <?php
                require_once 'config/google_oauth.php';
                $googleOAuthAvailable = isGoogleOAuthAvailable();
                $googleSignInUrl = getGoogleSignInUrl();
                ?>
                
                <?php if ($googleOAuthAvailable): ?>
                <a href="<?= htmlspecialchars($googleSignInUrl) ?>" class="btn" style="background: white; color: #333; border: 1.5px solid #e0e0e0; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; text-decoration: none;">
                    <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        <path fill="none" d="M0 0h48v48H0z"/>
                    </svg>
                    Sign in with Google
                </a>
                <?php else: ?>
                <button type="button" disabled class="btn" style="background: #f0f0f0; color: #999; border: 1.5px solid #e0e0e0; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; cursor: not-allowed;" title="Google Sign-In not configured">
                    <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="opacity: 0.5;">
                        <path fill="#999" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#999" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#999" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#999" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        <path fill="none" d="M0 0h48v48H0z"/>
                    </svg>
                    Google Sign-In (Not Configured)
                </button>
                <?php endif; ?>

                <p style="text-align: center; color: #666; font-size: 13px;">
                    Don't have an account? <a href="?page=register" style="color: #8B0000; font-weight: 600; text-decoration: none;">Create Account</a>
                </p>
            </div>
        </div>
    </div>
</div>
