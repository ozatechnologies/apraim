<?php
require_once 'classes/User.php';
require_once 'classes/CSRFProtection.php';

// Start session
session_start();

// Generate CSRF token
$csrf_token = CSRFProtection::generateToken();

// Handle registration
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate CSRF token
        CSRFProtection::validateToken($_POST['csrf_token']);

        // Validate inputs
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate password match
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match");
        }

        // Create user
        $user = new User();
        $abc_id = $user->register($name, $email, $password);
        
        $success = "Registration successful! Your AIM ID is: $abc_id";
        
        // Clear CSRF token after successful registration
        CSRFProtection::destroyToken();
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
    <title>Register - AIM ID System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #1976d2;
            --error-color: #dc3545;
            --success-color: #28a745;
            --gradient: linear-gradient(135deg, #1976d2 0%, #64b5f6 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient);
            padding: 20px;
        }

        .register-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .register-header i {
            font-size: 3em;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .register-header h1 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 1.1em;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #1565c0;
            transform: translateY(-2px);
        }

        .error-message {
            background: #fde8e8;
            color: var(--error-color);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 0.9em;
        }

        .error-message i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .success-message {
            background: #e8f5e9;
            color: var(--success-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-message i {
            font-size: 3em;
            margin-bottom: 15px;
            display: block;
        }

        .success-message h2 {
            margin-bottom: 10px;
            color: var(--success-color);
        }

        .success-message .aim-id {
            font-size: 1.5em;
            font-weight: 600;
            color: var(--primary-color);
            margin: 15px 0;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 20px;
            }

            .register-header h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h2>Registration Successful!</h2>
                <p>Welcome to AIM ID System</p>
                <div class="aim-id"><?php echo $abc_id; ?></div>
                <p>Please save your AIM ID for future reference.</p>
            </div>
            <div class="login-link">
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Proceed to Login</a>
            </div>
        <?php else: ?>
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h1>Create Account</h1>
                <p>Join AIM ID System</p>
            </div>

            <form method="POST" action="" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required id="password">
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required id="confirm_password">
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Simple form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;

            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>
</html>
