<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Check if user needs to update profile
if (isset($_SESSION['needs_profile_update'])) {
    unset($_SESSION['needs_profile_update']); // Remove the flag
    header('Location: profile.php');
    exit();
}

require_once 'classes/User.php';
require_once 'includes/header.php';

// Initialize user object and set properties from session
$user = new User();

// Initialize default values
$points = 0;
$totalTransactions = 0;
$transactions = [];
$error = null;

// Get user's transactions
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Get user data
    $points = $user->getRewardPoints($_SESSION['user']['id']);
    $totalTransactions = $user->getTransactionCount($_SESSION['user']['id']);
    $transactions = $user->getTransactions($_SESSION['user']['id'], $limit, $offset);
    $totalPages = ceil($totalTransactions / $limit);
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error = "An error occurred while loading your dashboard. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AIM ID System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #0061f2 0%, #6900f2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .stat-card h3 {
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: 600;
            color: #0061f2;
        }
        
        .transactions-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .transactions-table table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        
        .transactions-table th,
        .transactions-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .transactions-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .transactions-table tr:hover {
            background: #f8f9fa;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .pagination a {
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            color: #0061f2;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .pagination a:hover {
            background: #e9ecef;
        }
        
        .pagination .active {
            background: #0061f2;
            color: white;
            border-color: #0061f2;
        }
        
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .badge-earned {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-spent {
            background: #f8d7da;
            color: #721c24;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .no-data i {
            font-size: 2rem;
            margin-bottom: 1rem;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h1>
            <p>ABC ID: <?php echo htmlspecialchars($_SESSION['user']['abc_id']); ?></p>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Current Points</h3>
                <div class="value"><?php echo number_format($points); ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Transactions</h3>
                <div class="value"><?php echo number_format($totalTransactions); ?></div>
            </div>
        </div>

        <div class="transactions-table">
            <h2>Recent Transactions</h2>
            <?php if (!empty($transactions)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Points</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></td>
                                <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                <td><?php echo number_format($transaction['points']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $transaction['type']; ?>">
                                        <?php echo ucfirst($transaction['type']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="<?php echo $page === $i ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-receipt"></i>
                    <p>No transactions found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
