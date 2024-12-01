<?php
// Only set session settings if session hasn't started
if (session_status() === PHP_SESSION_NONE) {
    // Session Security Settings - MUST come before session_start
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Disabled for local development
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    session_start();
}

// Security Headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self' https: 'unsafe-inline' 'unsafe-eval'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data:;");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AIM ID System</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/abc-id-system/assets/css/style.css">
    
    <style>
        /* Base styles */
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        /* Navbar styles */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .navbar-brand {
            color: #0061f2;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .navbar-brand i {
            font-size: 1.8rem;
        }
        
        .navbar-nav {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .nav-link {
            color: #495057;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem;
            border-radius: 4px;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: #0061f2;
        }
        
        .nav-link.active {
            color: #0061f2;
        }
        
        .nav-link i {
            margin-right: 0.5rem;
        }
        
        /* Container styles */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .navbar-nav {
                gap: 1rem;
            }
            
            .nav-link {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/abc-id-system/" class="navbar-brand">
                <i class="fas fa-university"></i>
                AIM ID System
            </a>
            <?php if (isset($_SESSION['user'])): ?>
            <ul class="navbar-nav">
                <li>
                    <a href="/abc-id-system/user_dashboard.php" class="nav-link<?php echo strpos($_SERVER['PHP_SELF'], 'user_dashboard.php') !== false ? ' active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="/abc-id-system/profile.php" class="nav-link<?php echo strpos($_SERVER['PHP_SELF'], 'profile.php') !== false ? ' active' : ''; ?>">
                        <i class="fas fa-user"></i>Profile
                    </a>
                </li>
                <li>
                    <a href="/abc-id-system/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                </li>
            </ul>
            <?php elseif (isset($_SESSION['admin'])): ?>
                <ul class="navbar-nav">
                    <li>
                        <a href="/abc-id-system/admin/admin_dashboard.php" class="nav-link<?php echo strpos($_SERVER['PHP_SELF'], 'admin_dashboard.php') !== false ? ' active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/abc-id-system/admin/users.php" class="nav-link<?php echo strpos($_SERVER['PHP_SELF'], 'users.php') !== false ? ' active' : ''; ?>">
                            <i class="fas fa-users"></i>Users
                        </a>
                    </li>
                    <li>
                        <a href="/abc-id-system/admin/settings.php" class="nav-link<?php echo strpos($_SERVER['PHP_SELF'], 'settings.php') !== false ? ' active' : ''; ?>">
                            <i class="fas fa-cog"></i>Settings
                        </a>
                    </li>
                    <li>
                        <a href="/abc-id-system/logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </a>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav">
                    <li>
                        <a href="/abc-id-system/login.php" class="nav-link<?php echo strpos($_SERVER['PHP_SELF'], 'login.php') !== false ? ' active' : ''; ?>">
                            <i class="fas fa-sign-in-alt"></i>Login
                        </a>
                    </li>
                    <li>
                        <a href="/abc-id-system/register.php" class="nav-link<?php echo strpos($_SERVER['PHP_SELF'], 'register.php') !== false ? ' active' : ''; ?>">
                            <i class="fas fa-user-plus"></i>Register
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container"><?php // Main content will go here ?>
