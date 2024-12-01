<?php
require_once '../classes/Admin.php';
require_once '../classes/Security.php';
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!Security::checkRateLimit('admin_login_attempts', 3, 900)) {
        $error = "Too many login attempts. Please try again later.";
        Security::logSecurityEvent('ADMIN_LOGIN_BLOCKED', "Multiple failed login attempts blocked");
    } else {
        try {
            $username = Security::sanitize($_POST['username']);
            $password = $_POST['password'];

            $admin = new Admin();
            if ($admin->login($username, $password)) {
                Security::logSecurityEvent('ADMIN_LOGIN_SUCCESS', "Admin login successful: $username");
                header("Location: admin_dashboard.php");
                exit();
            } else {
                throw new Exception("Invalid username or password");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            Security::logSecurityEvent('ADMIN_LOGIN_FAILED', "Failed admin login attempt for username: $username");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AIM ID System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
    <style>
        :root {
            --primary-color: #1976d2;
            --gradient: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #1a1a1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: #2a2a2a;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header i {
            font-size: 3em;
            color: white;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .login-header h1 {
            color: white;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #888;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: white;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background: #333;
            border: 2px solid #444;
            border-radius: 8px;
            font-size: 16px;
            color: white;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--gradient);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
        }

        .error-message {
            background: rgba(255, 51, 51, 0.1);
            color: #ff3333;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid rgba(255, 51, 51, 0.3);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #888;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            color: var(--primary-color);
        }

        /* Animated background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(45deg, #1a1a1a 0%, #2a2a2a 100%);
            opacity: 0.8;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(255,107,107,0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.5);
                opacity: 0.2;
            }
            100% {
                transform: scale(1);
                opacity: 0.5;
            }
        }
    </style>
</head>
<body>
    <div class="animated-bg"></div>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-user-shield"></i>
            <h1>Admin Login</h1>
            <p>Secure access to AIM ID System</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="back-link">
            <a href="../index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
