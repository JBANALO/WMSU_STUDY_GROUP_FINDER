<?php
// Forgot Password page
?>
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
    
    .back-link {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #8B0000;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 30px;
        transition: all 0.3s;
        font-weight: 500;
    }
    
    .back-link:hover {
        gap: 12px;
    }
    
    .info-box {
        background: #fff8e1;
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin-bottom: 25px;
        border-radius: 4px;
    }
    
    .info-box p {
        font-size: 13px;
        color: #856404;
        margin: 0;
        line-height: 1.5;
    }
    
    .btn-secondary {
        background: white !important;
        color: #8B0000 !important;
        border: 2px solid #8B0000 !important;
    }
    
    .btn-secondary:hover {
        background: #f8f9fa !important;
    }
</style>

<div class="auth-container" style="min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div class="auth-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-radius: 15px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.2); background: white; width: 100%; max-width: 1000px; height: 550px;">
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
            <p style="font-size: 14px; opacity: 0.95; line-height: 1.5; max-width: 300px; text-align: center;">Reset your password to continue your learning journey with your study group.</p>

            <div style="position: absolute; bottom: 20px; text-align: center; width: 100%;">
                <p style="font-size: 12px; opacity: 0.85;">Western Mindanao State University</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-right" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; overflow-y: auto;">
            <div class="auth-form-container" style="width: 100%; max-width: 450px;">
                <a href="?page=login" class="back-link">
                    <span>←</span> Back to Sign In
                </a>

                <h2 style="color: #333; margin-bottom: 10px; text-align: left; font-size: 32px; font-weight: 700;">Forgot Password?</h2>
                <p style="text-align: left; color: #666; margin-bottom: 25px; font-size: 15px; line-height: 1.6;">No worries! Enter your WMSU email address and we'll send you instructions to reset your password.</p>

                <div class="info-box">
                    <p>💡 Make sure to check your spam folder if you don't see the email in your inbox.</p>
                </div>

                <form action="handlers/forgot_password_handler.php" method="POST">
                    <div class="form-group">
                        <label>WMSU Email</label>
                        <input type="email" name="email" placeholder="your.name@wmsu.edu.ph" required autofocus pattern="[a-z0-9._%+-]+@wmsu\.edu\.ph$" title="Please enter a valid WMSU email address">
                    </div>

                    <button type="submit" class="btn" style="margin-bottom: 15px;">Send Reset Link</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='?page=login'">Cancel</button>
                </form>

                <div style="text-align: center; margin: 30px 0;">
                    <p style="color: #999; font-size: 14px; margin: 0;">Remember your password? <a href="?page=login" style="color: #8B0000; text-decoration: none; font-weight: 600;">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
