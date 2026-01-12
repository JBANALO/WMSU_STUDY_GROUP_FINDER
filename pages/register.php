<?php
// Register page - no backend logic, just display
?>
<style>
    @media (max-width: 768px) {
        .auth-grid {
            grid-template-columns: 1fr !important;
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
            padding-bottom: 20px !important;
        }
        .auth-right h2 {
            font-size: 20px !important;
        }
        .form-group {
            margin-bottom: 12px !important;
        }
        .auth-form-grid {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
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
                max-height: calc(100vh - 20px) !important;
            }
            .auth-form-container {
                max-width: 100% !important;
                padding-bottom: 30px !important;
            }
        }
    }
</style>

<div class="auth-container" style="min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div class="auth-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0; border-radius: 15px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.2); background: white; width: 100%; max-width: 1200px; max-height: 700px;">
        <!-- Left Side - Branding with Background Image -->
        <div class="auth-left" style="background: linear-gradient(135deg, rgba(139, 0, 0, 0.9) 0%, rgba(96, 0, 0, 0.9) 100%), url('img/register-bg.svg') center/cover; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; color: white; position: relative; overflow: hidden;">
            <!-- WMSU Logo -->
            <div class="auth-logo-top" style="position: absolute; top: 20px; left: 20px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <img src="images/logo.png" alt="WMSU Logo" style="width: 50px; height: 50px; object-fit: contain;">
            </div>

            <!-- Crimson Study Squad Logo -->
            <div class="auth-logo-main" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px; box-shadow: 0 15px 40px rgba(0,0,0,0.3); position: relative; z-index: 10;">
                <img src="images/logo.png" alt="Crimson Study Squad" style="width: 150px; height: 150px; object-fit: contain;">
            </div>

            <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 12px; text-align: center;">Crimson Study Squad</h2>
            <p style="font-size: 14px; opacity: 0.95; line-height: 1.5; max-width: 300px; text-align: center;">Join our study group community. Connect with fellow students and grow together.</p>

            <div style="position: absolute; bottom: 20px; text-align: center; width: 100%;">
                <p style="font-size: 12px; opacity: 0.85;">Western Mindanao State University</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-right" style="display: flex; flex-direction: column; align-items: center; padding: 30px 30px 20px 30px; overflow-y: auto; max-height: 700px;">
            <div class="auth-form-container" style="width: 100%; max-width: 350px; padding-bottom: 20px;">
                <h2 style="color: #8B0000; margin-bottom: 8px; text-align: center; font-size: 24px; font-weight: 700;">Create Account</h2>
                <p style="text-align: center; color: #666; margin-bottom: 20px; font-size: 13px;">Join our study group community</p>

                <form action="handlers/register_handler.php" method="POST">
                    <div class="auth-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>First Name</label>
                            <input type="text" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Last Name</label>
                            <input type="text" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" placeholder="Middle Name (Optional)">
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Choose username" required>
                    </div>

                    <div class="form-group">
                        <label>WMSU Email</label>
                        <input type="email" name="email" placeholder="your.name@wmsu.edu.ph" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-group">
                            <input type="password" name="password" class="password-input" placeholder="Min. 6 characters" required>
                            <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="password-group">
                            <input type="password" name="confirm_password" class="password-input" placeholder="Confirm password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>

                    <button type="submit" class="btn" style="margin-top: 15px; margin-bottom: 12px;">Create Account</button>
                </form>

                <div style="text-align: center; margin: 12px 0;">
                    <p style="color: #999; font-size: 12px; margin: 0;">OR</p>
                </div>

                <!-- Google Sign-In Button -->
                <div style="display: flex; justify-content: center; margin: 15px 0;">
                    <div id="googleSignUpDiv"></div>
                </div>

                <script src="https://accounts.google.com/gsi/client" async defer></script>
                <script>
                function handleCredentialResponseRegister(response) {
                    console.log('Google Sign-Up credential received');
                    
                    if (!response || !response.credential) {
                        console.error('No credential in response');
                        alert('Google Sign-Up failed. Please try again.');
                        return;
                    }
                    
                    // Send JWT token to backend for verification
                    fetch('handlers/google_signin_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            credential: response.credential
                        })
                    })
                    .then(res => {
                        console.log('Response status:', res.status);
                        if (!res.ok) {
                            throw new Error('HTTP error ' + res.status);
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Server response:', data);
                        if (data.success) {
                            window.location.href = 'index.php?page=dashboard';
                        } else {
                            alert(data.message || 'Registration failed. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during registration: ' + error.message);
                    });
                }
                
                // Initialize Google Sign-In when library loads
                window.onload = function() {
                    console.log('Initializing Google Sign-Up...');
                    
                    if (typeof google === 'undefined') {
                        console.error('Google Sign-In library not loaded');
                        return;
                    }
                    
                    try {
                        google.accounts.id.initialize({
                            client_id: "174568861864-ed5p6jgvvbuc6gjbnrkvv5ki8h9vfkng.apps.googleusercontent.com",
                            callback: handleCredentialResponseRegister,
                            auto_select: false,
                            cancel_on_tap_outside: true
                        });
                        
                        google.accounts.id.renderButton(
                            document.getElementById("googleSignUpDiv"),
                            { 
                                type: "standard",
                                theme: "outline", 
                                size: "large",
                                text: "signup_with",
                                shape: "rectangular",
                                logo_alignment: "left",
                                width: 350
                            }
                        );
                        
                        console.log('Google Sign-Up button rendered successfully');
                    } catch (error) {
                        console.error('Error initializing Google Sign-Up:', error);
                    }
                };
                </script>

                <p style="text-align: center; color: #666; font-size: 13px; margin-top: 12px;">
                    Already have an account? <a href="?page=login" style="color: #8B0000; font-weight: 600; text-decoration: none;">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</div>

