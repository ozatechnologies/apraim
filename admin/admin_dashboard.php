<?php
require_once '../classes/Admin.php';
require_once '../classes/User.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

try {
    $user = new User();
    $users = $user->getAllUsersForAdmin();
} catch (Exception $e) {
    $error = "Error retrieving users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AIM ID System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin']['username']); ?>!</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="admin-controls">
            <a href="add_user.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>AIM ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Points</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No users found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['abc_id']); ?></td>
                                <td><?php echo htmlspecialchars($u['name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo number_format($u['points']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $u['status']; ?>">
                                        <?php echo ucfirst(htmlspecialchars($u['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $u['last_login'] ? date('M d, Y H:i', strtotime($u['last_login'])) : 'Never'; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_user.php?id=<?php echo $u['id']; ?>" 
                                           class="btn btn-sm btn-primary" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_points.php?id=<?php echo $u['id']; ?>" 
                                           class="btn btn-sm btn-success" title="Manage Points">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <a href="view_transactions.php?id=<?php echo $u['id']; ?>" 
                                           class="btn btn-sm btn-info" title="View Transactions">
                                            <i class="fas fa-history"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
