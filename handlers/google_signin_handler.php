<?php
// handlers/google_signin_handler.php - Google Sign-In with JWT verification
if (!isset($_SESSION)) {
    session_start();
}
require_once '../config/database.php';

header('Content-Type: application/json');

// Enable error logging
error_log("Google Sign-In handler called");

try {
    // Get JWT credential from request
    $input = json_decode(file_get_contents('php://input'), true);
    $credential = $input['credential'] ?? null;
    
    error_log("Credential received: " . ($credential ? "Yes" : "No"));
    
    if (!$credential) {
        error_log("No credential provided in request");
        echo json_encode(['success' => false, 'message' => 'No credential provided']);
        exit;
    }
    
    // Google Client ID
    $client_id = '174568861864-ed5p6jgvvbuc6gjbnrkvv5ki8h9vfkng.apps.googleusercontent.com';
    
    error_log("Verifying token with Google...");
    
    // Verify JWT token with Google
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential);
    $response = @file_get_contents($url);
    
    if ($response === false) {
        error_log("Failed to verify token with Google - network error");
        echo json_encode(['success' => false, 'message' => 'Failed to verify token with Google']);
        exit;
    }
    
    $user_data = json_decode($response, true);
    error_log("Google response: " . print_r($user_data, true));
    
    // Verify token validity
    if (!isset($user_data['email']) || $user_data['aud'] !== $client_id) {
        error_log("Invalid token - email: " . ($user_data['email'] ?? 'not set') . ", aud: " . ($user_data['aud'] ?? 'not set'));
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }
    
    // Check if email is WMSU email (case-insensitive check, PHP 7.x compatible)
    $email_lower = strtolower(trim($user_data['email']));
    $domain = '@wmsu.edu.ph';
    $email_ending = substr($email_lower, -strlen($domain));
    
    error_log("Email validation - Original: " . $user_data['email'] . ", Lowercase: " . $email_lower . ", Ending: " . $email_ending . ", Expected: " . $domain);
    
    if ($email_ending !== $domain) {
        error_log("Non-WMSU email attempted: " . $user_data['email'] . " (ending: '" . $email_ending . "')");
        echo json_encode(['success' => false, 'message' => 'Only WMSU emails (@wmsu.edu.ph) are allowed']);
        exit;
    }
    
    error_log("Email validation passed for: " . $user_data['email']);
    
    $email = $user_data['email'];
    $google_id = $user_data['sub']; // Google user ID
    $first_name = $user_data['given_name'] ?? '';
    $last_name = $user_data['family_name'] ?? '';
    $profile_picture = $user_data['picture'] ?? '';
    
    error_log("Processing user: $email (Google ID: $google_id)");
    
    // Check if user exists by email or google_id
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR google_id = ?");
    $stmt->execute([$email, $google_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Create new user
        error_log("Creating new user for: $email");
        
        $username = explode('@', $email)[0]; // Use email prefix as username
        
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            // Append random number if username exists
            $username = $username . rand(100, 999);
            error_log("Username taken, using: $username");
        }
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, first_name, last_name, google_id, auth_provider, profile_picture, password, status) 
                VALUES (?, ?, ?, ?, ?, 'google', ?, ?, 'pending')
            ");
            // Random password for Google users (won't be used)
            $random_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $stmt->execute([$username, $email, $first_name, $last_name, $google_id, $profile_picture, $random_password]);
            
            $user_id = $pdo->lastInsertId();
            
            // Fetch newly created user
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            error_log("New user created with ID: $user_id - Status: pending approval");
            
            echo json_encode([
                'success' => false, 
                'message' => "Account created! Please wait for admin approval before you can access the system."
            ]);
            exit;
            
        } catch (PDOException $e) {
            // If columns don't exist, try without them
            error_log("Database error: " . $e->getMessage());
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Database needs to be updated. Please run the database migration script first.'
                ]);
                exit;
            }
            throw $e;
        }
    } else {
        // User exists - update google_id and profile_picture if not set
        error_log("Existing user found: " . $user['email'] . " (ID: " . $user['id'] . ")");
        
        if (empty($user['google_id']) || empty($user['profile_picture'])) {
            error_log("Updating Google ID and profile picture for user: " . $user['id']);
            try {
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET google_id = ?, auth_provider = 'google', profile_picture = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$google_id, $profile_picture, $user['id']]);
            } catch (PDOException $e) {
                // If columns don't exist, silently continue
                error_log("Error updating user: " . $e->getMessage());
                if (strpos($e->getMessage(), 'Unknown column') === false) {
                    throw $e;
                }
            }
        }
    }
    
    // Check user status
    error_log("Checking user status: " . $user['status']);
    
    if ($user['status'] === 'pending') {
        error_log("User status is pending");
        echo json_encode([
            'success' => false,
            'message' => 'Your account is pending admin approval. Please wait for verification.'
        ]);
        exit;
    } elseif ($user['status'] === 'declined') {
        error_log("User status is declined");
        echo json_encode([
            'success' => false,
            'message' => 'Your account has been declined. Please contact the administrator.'
        ]);
        exit;
    }
    
    // Login successful - user is approved
    error_log("Login successful for user: " . $user['id']);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    
    error_log("Session variables set: user_id=" . $_SESSION['user_id']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Welcome back, ' . $user['first_name'] . '!',
        'redirect' => 'index.php?page=dashboard'
    ]);
    
} catch (Exception $e) {
    error_log("Google Sign-In Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during sign-in. Please try again.'
    ]);
}
?>
