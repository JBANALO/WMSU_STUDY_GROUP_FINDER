<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
session_start();

$page = $_GET['page'] ?? 'register';

if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMSU Study Group Finder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php
        switch($page) {
            case 'register':
                include 'pages/register.php';
                break;
            case 'login':
                include 'pages/login.php';
                break;
            case 'dashboard':
                if (!isLoggedIn()) redirectTo('login');
                include 'pages/dashboard.php';
                break;
            case 'admin_dashboard':
                if (!isLoggedIn()) redirectTo('login');
                if (!isAdmin()) redirectTo('dashboard');
                include 'pages/admin_dashboard.php';
                break;
            case 'my_groups':
                if (!isLoggedIn()) redirectTo('login');
                include 'pages/my_groups.php';
                break;
            case 'group_details':
                if (!isLoggedIn()) redirectTo('login');
                include 'pages/group_details.php';
                break;
            case 'group_feed':
                if (!isLoggedIn()) redirectTo('login');
                include 'pages/group_feed.php';
                break;
            case 'notifications':
                if (!isLoggedIn()) redirectTo('login');
                include 'pages/notifications.php';
                break;
            case 'profile':
                if (!isLoggedIn()) redirectTo('login');
                include 'pages/profile.php';
                break;
            case 'change_password':
                if (!isLoggedIn()) redirectTo('login');
                include 'pages/change_password.php';
                break;
            default:
                include 'pages/register.php';
        }
        ?>
    </div>
    
    <script>
        function togglePassword(btn) {
            const input = btn.parentElement.querySelector('.password-input');
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>