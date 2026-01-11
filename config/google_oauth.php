<?php
/**
 * Google OAuth Configuration
 * 
 * Setup Instructions:
 * 1. Go to https://console.cloud.google.com/
 * 2. Create a new project or select existing
 * 3. Enable Google+ API
 * 4. Go to Credentials → Create Credentials → OAuth 2.0 Client ID
 * 5. Application type: Web application
 * 6. Authorized redirect URIs: 
 *    - Local: http://localhost:8000/handlers/google_oauth_handler.php
 *    - Railway: https://your-app.up.railway.app/handlers/google_oauth_handler.php
 * 7. Copy Client ID and Client Secret below
 */

// Detect environment
$is_production = isset($_ENV['RAILWAY_ENVIRONMENT']) || 
                 (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false);

// Google OAuth Credentials
// For local: credentials are in config/.env.local (not committed to git)
// For Railway: Set via environment variables in Railway dashboard

// Load local credentials if file exists
$local_env_file = __DIR__ . '/.env.local';
if (file_exists($local_env_file) && !$is_production) {
    $lines = file($local_env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

define('GOOGLE_CLIENT_ID', 
    $is_production 
        ? ($_ENV['GOOGLE_CLIENT_ID'] ?? '') 
        : (getenv('GOOGLE_CLIENT_ID') ?: '')
);

define('GOOGLE_CLIENT_SECRET', 
    $is_production 
        ? ($_ENV['GOOGLE_CLIENT_SECRET'] ?? '') 
        : (getenv('GOOGLE_CLIENT_SECRET') ?: '')
);

// Redirect URI
define('GOOGLE_REDIRECT_URI', $is_production
    ? 'https://web-production-...up.railway.app/handlers/google_oauth_handler.php'
    : 'http://localhost:8000/handlers/google_oauth_handler.php'
);

// Scopes
define('GOOGLE_SCOPES', [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile'
]);

/**
 * Check if Google OAuth is available
 */
function isGoogleOAuthAvailable() {
    // Check if vendor autoload exists
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        return false;
    }
    
    // Check if Google API client exists
    if (!file_exists(__DIR__ . '/../vendor/google/apiclient/src/Client.php')) {
        return false;
    }
    
    // Check if credentials are configured
    if (GOOGLE_CLIENT_ID === '' || GOOGLE_CLIENT_SECRET === '') {
        return false;
    }
    
    return true;
}

/**
 * Initialize Google Client
 */
function getGoogleClient() {
    if (!isGoogleOAuthAvailable()) {
        throw new Exception('Google OAuth is not configured');
    }
    
    // Try to load autoloader
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
    } catch (Exception $e) {
        throw new Exception('Vendor autoload failed: ' . $e->getMessage());
    }
    
    // Check if Google_Client class exists after loading
    if (!class_exists('Google_Client')) {
        throw new Exception('Google_Client class not found - composer dependencies may not be installed');
    }
    
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->setScopes(GOOGLE_SCOPES);
    $client->setAccessType('online');
    $client->setPrompt('select_account consent');
    
    return $client;
}

/**
 * Get Google Sign-In URL
 */
function getGoogleSignInUrl() {
    // Check availability FIRST before doing anything
    if (!isGoogleOAuthAvailable()) {
        return '#';
    }
    
    try {
        $client = getGoogleClient();
        return $client->createAuthUrl();
    } catch (Exception $e) {
        error_log('Google OAuth Error: ' . $e->getMessage());
        return '#';
    }
}
?>
