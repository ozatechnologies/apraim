<?php
require_once 'includes/header.php';
require_once 'classes/User.php';
require_once 'classes/CSRFProtection.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$userData = $user->getUserById($_SESSION['user']['id']);
$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate CSRF token
        CSRFProtection::validateToken($_POST['csrf_token']);

        // Validate and sanitize inputs
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        
        // Update profile
        if ($user->updateProfile($_SESSION['user']['id'], [
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ])) {
            $message = "Profile updated successfully!";
            $userData = $user->getUserById($_SESSION['user']['id']); // Refresh data
        } else {
            throw new Exception("Failed to update profile");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Generate CSRF token
$csrf_token = CSRFProtection::generateToken();
?>

<div class="profile-container fade-in">
    <div class="card">
        <div class="profile-header">
            <h2><i class="fas fa-user-circle"></i> My Profile</h2>
            <div class="aim-id">
                AIM ID: <strong><?php echo htmlspecialchars($userData['abc_id']); ?></strong>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="profile-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <i class="fas fa-user form-icon"></i>
                <input type="text" 
                       name="name" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars($userData['name']); ?>" 
                       placeholder="Full Name" 
                       required>
            </div>

            <div class="form-group">
                <i class="fas fa-envelope form-icon"></i>
                <input type="email" 
                       name="email" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars($userData['email']); ?>" 
                       placeholder="Email Address" 
                       required>
            </div>

            <div class="form-group">
                <i class="fas fa-phone form-icon"></i>
                <input type="tel" 
                       name="phone" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" 
                       placeholder="Phone Number">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="change_password.php" class="btn btn-secondary">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
        </form>
    </div>

    <!-- Account Statistics -->
    <div class="stats-grid mt-20">
        <div class="stat-card">
            <i class="fas fa-calendar-alt"></i>
            <h3>Member Since</h3>
            <div class="stat-value"><?php echo date('M Y', strtotime($userData['created_at'])); ?></div>
            <p><?php echo date('d/m/Y', strtotime($userData['created_at'])); ?></p>
        </div>

        <div class="stat-card">
            <i class="fas fa-star"></i>
            <h3>Reward Points</h3>
            <div class="stat-value"><?php echo number_format($user->getRewardPoints($_SESSION['user']['id'])); ?></div>
            <p>Total Points Earned</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-shield-alt"></i>
            <h3>Account Status</h3>
            <div class="stat-value">Active</div>
            <p>Account in Good Standing</p>
        </div>
    </div>

    <!-- Account Actions -->
    <div class="card mt-20">
        <h2><i class="fas fa-cog"></i> Account Settings</h2>
        <div class="account-actions">
            <a href="notifications.php" class="btn btn-secondary">
                <i class="fas fa-bell"></i> Notification Settings
            </a>
            <a href="privacy.php" class="btn btn-secondary">
                <i class="fas fa-lock"></i> Privacy Settings
            </a>
            <a href="data.php" class="btn btn-secondary">
                <i class="fas fa-download"></i> Download My Data
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()">
                <i class="fas fa-trash-alt"></i> Delete Account
            </button>
        </div>
    </div>
</div>

<script>
function confirmDeleteAccount() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        window.location.href = 'delete_account.php';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
