<?php
require_once 'classes/Admin.php';
require_once 'classes/CSRFProtection.php';

// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Get admin details from session
$admin_id = $_SESSION['admin']['id'];
$admin_username = $_SESSION['admin']['username'];

// Create Admin object
$admin = new Admin();
$admin->id = $admin_id;

// Get ABC ID from URL
$abc_id = filter_input(INPUT_GET, 'abc_id', FILTER_SANITIZE_STRING);
if (!$abc_id) {
    header("Location: admin_dashboard.php");
    exit();
}

// Handle transaction filtering
$start_date = filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_STRING);
$end_date = filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_STRING);

// Get user transactions
$transactions = $admin->getUserTransactions($abc_id, $start_date, $end_date);

// Generate CSRF token
$csrf_token = CSRFProtection::generateToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Transactions - ABC ID System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .filter-form { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .points-positive { color: green; }
        .points-negative { color: red; }
    </style>
</head>
<body>
    <h2>Transactions for User <?php echo $abc_id; ?></h2>
    
    <form method="GET" action="" class="filter-form">
        <input type="hidden" name="abc_id" value="<?php echo $abc_id; ?>">
        
        <label>Start Date:</label>
        <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        
        <label>End Date:</label>
        <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        
        <button type="submit">Filter Transactions</button>
    </form>

    <?php if (!empty($transactions)): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Points</th>
                    <th>Description</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['transaction_date']; ?></td>
                        <td class="<?php echo $transaction['points'] > 0 ? 'points-positive' : 'points-negative'; ?>">
                            <?php echo $transaction['points']; ?>
                        </td>
                        <td><?php echo $transaction['description']; ?></td>
                        <td>Admin ID: <?php echo $transaction['admin_id']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No transactions found for this user.</p>
    <?php endif; ?>
    
    <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
