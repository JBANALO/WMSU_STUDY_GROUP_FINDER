<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once '../config/database.php';
require_once '../config/google_oauth.php';

try {
    $client = getGoogleClient();
    
    // Handle OAuth callback
    if (isset($_GET['code'])) {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception('Error fetching access token: ' . $token['error']);
        }
        
        $client->setAccessToken($token['access_token']);
        
        // Get user info from Google
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account = $google_oauth->userinfo->get();
        
        $google_id = $google_account->id;
        $email = $google_account->email;
        $first_name = $google_account->givenName ?? '';
        $last_name = $google_account->familyName ?? '';
        $picture = $google_account->picture ?? '';
        
        // Verify email is from WMSU domain
        if (!str_ends_with($email, '@wmsu.edu.ph')) {
            $_SESSION['error'] = "Please use your WMSU email address (@wmsu.edu.ph)";
            header("Location: ../index.php?page=login");
            exit();
        }
        
        // Check if user already exists by Google ID
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([$google_id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Check if email already exists (user registered manually)
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Link Google account to existing user
                $stmt = $pdo->prepare("UPDATE users SET google_id = ? WHERE id = ?");
                $stmt->execute([$google_id, $user['id']]);
            } else {
                // Create new user with Google account - still needs admin approval
                $username = explode('@', $email)[0]; // Use email prefix as username
                
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, first_name, last_name, google_id, password, status) 
                    VALUES (?, ?, ?, ?, ?, ?, 'pending')
                ");
                // Set random password since they're using Google OAuth
                $random_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
                $stmt->execute([$username, $email, $first_name, $last_name, $google_id, $random_password]);
                
                $user_id = $pdo->lastInsertId();
                
                // Fetch newly created user
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                $_SESSION['success'] = "Account created successfully! Please wait for admin approval to access the system.";
            }
        }
        
        // Check user status
        if ($user['status'] === 'pending') {
            $_SESSION['error'] = "Your account is pending admin approval. Please wait for verification.";
            header("Location: ../index.php?page=login");
            exit();
        } elseif ($user['status'] === 'declined') {
            $_SESSION['error'] = "Your account has been declined. Please contact the administrator.";
            header("Location: ../index.php?page=login");
            exit();
        }
        
        // Login successful - user is approved
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['success'] = "Welcome back, " . $user['first_name'] . "!";
        
        header("Location: ../index.php?page=dashboard");
        exit();
        
    } else {
        // No code parameter
        $_SESSION['error'] = "Authentication failed. Please try again.";
        header("Location: ../index.php?page=login");
        exit();
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = "Google Sign-In error: " . $e->getMessage();
    header("Location: ../index.php?page=login");
    exit();
}
?>
