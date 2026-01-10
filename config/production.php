<?php
// Production Configuration for Railway Deployment

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'studyfinder');

// Site Configuration
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost:8000');
define('SITE_NAME', 'Crimson Study Squad');

// Security
define('SECRET_KEY', getenv('SECRET_KEY') ?: 'your-secret-key-change-in-production');

// Email Configuration (optional, for future use)
define('MAIL_HOST', getenv('MAIL_HOST') ?: '');
define('MAIL_PORT', getenv('MAIL_PORT') ?: 587);
define('MAIL_USER', getenv('MAIL_USER') ?: '');
define('MAIL_PASS', getenv('MAIL_PASS') ?: '');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@studyfinder.wmsu.edu.ph');

// Error Handling for Production
error_reporting(E_ALL);
ini_set('display_errors', getenv('APP_ENV') === 'development' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error.log');

// Session Configuration
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', 1); // Set to 1 for HTTPS only
?>
