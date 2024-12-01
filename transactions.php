<?php
require_once 'includes/header.php';
require_once 'classes/User.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$userData = $user->getUserById($_SESSION['user']['id']);

// Get transactions with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$transactions = $user->getTransactions($_SESSION['user']['id'], $limit, $offset);
$totalTransactions = $user->getTransactionCount($_SESSION['user']['id']);
$totalPages = ceil($totalTransactions / $limit);
?>

<div class="transactions-container fade-in">
    <!-- Header Section -->
    <div class="card">
        <div class="transactions-header">
            <h2><i class="fas fa-history"></i> Transaction History</h2>
            <div class="points-summary">
                <span>Total Points:</span>
                <strong><?php echo number_format($user->getRewardPoints($_SESSION['user']['id'])); ?></strong>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mt-20">
        <div class="filters">
            <div class="form-group">
                <i class="fas fa-calendar form-icon"></i>
                <select class="form-control" id="timeFilter" onchange="filterTransactions()">
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
            </div>

            <div class="form-group">
                <i class="fas fa-filter form-icon"></i>
                <select class="form-control" id="typeFilter" onchange="filterTransactions()">
                    <option value="all">All Types</option>
                    <option value="earned">Points Earned</option>
                    <option value="spent">Points Spent</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card mt-20">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Points</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo date('M d, Y h:i A', strtotime($transaction['transaction_date'])); ?></td>
                            <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                            <td>
                                <span class="badge <?php echo $transaction['points'] >= 0 ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $transaction['points'] >= 0 ? 'Earned' : 'Spent'; ?>
                                </span>
                            </td>
                            <td class="<?php echo $transaction['points'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $transaction['points'] >= 0 ? '+' : ''; ?><?php echo number_format($transaction['points']); ?>
                            </td>
                            <td><?php echo number_format($transaction['balance']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1); ?>" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>

                <div class="page-numbers">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" 
                           class="page-number <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo ($page + 1); ?>" class="btn btn-secondary">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Export Options -->
    <div class="card mt-20">
        <h3><i class="fas fa-download"></i> Export Transactions</h3>
        <div class="export-buttons">
            <button class="btn btn-secondary" onclick="exportTransactions('pdf')">
                <i class="fas fa-file-pdf"></i> Export as PDF
            </button>
            <button class="btn btn-secondary" onclick="exportTransactions('csv')">
                <i class="fas fa-file-csv"></i> Export as CSV
            </button>
            <button class="btn btn-secondary" onclick="exportTransactions('excel')">
                <i class="fas fa-file-excel"></i> Export as Excel
            </button>
        </div>
    </div>
</div>

<script>
function filterTransactions() {
    // Implement client-side filtering
    const timeFilter = document.getElementById('timeFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    // Add filtering logic here
}

function exportTransactions(format) {
    // Implement export functionality
    alert('Exporting as ' + format.toUpperCase() + '...');
    // Add export logic here
}
</script>

<?php require_once 'includes/footer.php'; ?>
