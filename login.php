<?php
session_start();
require_once 'classes/User.php';
require_once 'classes/CSRFProtection.php';

// Check if already logged in
if (isset($_SESSION['user'])) {
    header('Location: user_dashboard.php');
    exit();
} elseif (isset($_SESSION['admin'])) {
    header('Location: admin_dashboard.php');
    exit();
}

// Generate CSRF token
$csrf = new CSRFProtection();
$token = $csrf->generateToken();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token'])) {
            throw new Exception('Invalid request: Missing CSRF token');
        }
        
        $csrf->validateToken($_POST['csrf_token']);

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            throw new Exception('All fields are required');
        }

        $user = new User();
        if ($user->login($email, $password)) {
            header('Location: user_dashboard.php');
            exit();
        } else {
            throw new Exception('Invalid email or password');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AIM ID System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #0061f2;
            --secondary-color: #6900f2;
            --error-color: #dc3545;
            --success-color: #198754;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 1rem;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #6c757d;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            font-size: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0,97,242,0.25);
        }

        .login-type {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .login-type label {
            flex: 1;
            padding: 0.8rem;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .login-type input[type="radio"] {
            display: none;
        }

        .login-type input[type="radio"]:checked + label {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            padding: 0.8rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-login:hover {
            background: #0056b3;
        }

        .error-message {
            background: #f8d7da;
            color: var(--error-color);
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-message i {
            font-size: 1.1rem;
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #6c757d;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>AIM ID System</h1>
            <p>Sign in to your account</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="login-type">
                <input type="radio" id="student" name="login_type" value="student" checked>
                <label for="student">
                    <i class="fas fa-user-graduate"></i> Student
                </label>
                
                <input type="radio" id="admin" name="login_type" value="admin">
                <label for="admin">
                    <i class="fas fa-user-shield"></i> Admin
                </label>
            </div>

            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Email address" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>

            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </form>
    </div>

    <script>
        // Redirect to admin login when admin radio is selected
        document.querySelectorAll('input[name="login_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'admin') {
                    window.location.href = 'admin_login.php';
                }
            });
        });
    </script>
</body>
</html>
