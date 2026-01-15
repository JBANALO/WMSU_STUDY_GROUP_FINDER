<?php
/**
 * Cloudinary Configuration for File Uploads
 * 
 * Setup Instructions:
 * 1. Sign up at https://cloudinary.com (FREE)
 * 2. Get your credentials from Dashboard
 * 3. For local: Add to config/.env.local
 * 4. For Railway: Add to environment variables
 */

// Detect environment
$is_production = isset($_ENV['RAILWAY_ENVIRONMENT']) || 
                 (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false);

// Load local credentials if file exists
$local_env_file = __DIR__ . '/.env.local';
if (file_exists($local_env_file) && !$is_production) {
    $lines = file($local_env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            if (!getenv(trim($key))) {
                putenv(trim($key) . '=' . trim($value));
            }
        }
    }
}

// Cloudinary Configuration
define('CLOUDINARY_CLOUD_NAME', 
    $is_production 
        ? ($_ENV['CLOUDINARY_CLOUD_NAME'] ?? '') 
        : (getenv('CLOUDINARY_CLOUD_NAME') ?: '')
);

define('CLOUDINARY_API_KEY', 
    $is_production 
        ? ($_ENV['CLOUDINARY_API_KEY'] ?? '') 
        : (getenv('CLOUDINARY_API_KEY') ?: '')
);

define('CLOUDINARY_API_SECRET', 
    $is_production 
        ? ($_ENV['CLOUDINARY_API_SECRET'] ?? '') 
        : (getenv('CLOUDINARY_API_SECRET') ?: '')
);

/**
 * Check if Cloudinary is configured
 */
function isCloudinaryConfigured() {
    return !empty(CLOUDINARY_CLOUD_NAME) && 
           !empty(CLOUDINARY_API_KEY) && 
           !empty(CLOUDINARY_API_SECRET);
}

/**
 * Initialize Cloudinary
 */
function initCloudinary() {
    if (!isCloudinaryConfigured()) {
        return false;
    }
    
    require_once __DIR__ . '/../vendor/autoload.php';
    
    \Cloudinary\Configuration\Configuration::instance([
        'cloud' => [
            'cloud_name' => CLOUDINARY_CLOUD_NAME,
            'api_key' => CLOUDINARY_API_KEY,
            'api_secret' => CLOUDINARY_API_SECRET
        ],
        'url' => [
            'secure' => true
        ]
    ]);
    
    return true;
}
?>
