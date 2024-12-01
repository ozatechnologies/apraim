<?php
session_start();
require_once 'classes/Admin.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

$admin = new Admin();
$error = '';
$success = '';

// Get user ABC ID from URL
$abc_id = $_GET['abc_id'] ?? '';

if (empty($abc_id)) {
    header('Location: admin_dashboard.php');
    exit();
}

// Get user details
$user = $admin->getUserById($abc_id);

if (!$user) {
    header('Location: admin_dashboard.php');
    exit();
}

// Handle points modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $points = (int)$_POST['points'];
    $description = trim($_POST['description']);
    
    if (empty($description)) {
        $error = "Description is required";
    } else {
        $result = $admin->updateUserPoints($abc_id, $points, $description);
        
        if ($result['success']) {
            $success = $result['message'];
            // Refresh user data
            $user = $admin->getUserById($abc_id);
        } else {
            $error = $result['message'];
        }
    }
}

// Get recent transactions
$transactions = $admin->getUserTransactions($abc_id, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Points - ABC ID System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">ABC ID System - Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">User Information</h5>
                        <p><strong>ABC ID:</strong> <?php echo htmlspecialchars($abc_id); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Current Points:</strong> <?php echo htmlspecialchars($user['points'] ?? 0); ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Modify Points</h5>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="points" class="form-label">Points</label>
                                <input type="number" class="form-control" id="points" name="points" required>
                                <small class="text-muted">Use positive number to add points, negative to deduct</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Points</button>
                            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Transactions</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Points</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i', $transaction['timestamp'] / 1000); ?></td>
                                            <td><?php echo htmlspecialchars($transaction['points']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $transaction['type'] === 'credit' ? 'success' : 'danger'; ?>">
                                                    <?php echo htmlspecialchars($transaction['type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <a href="admin_user_transactions.php?abc_id=<?php echo $abc_id; ?>" class="btn btn-info">
                            View All Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
