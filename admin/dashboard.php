<?php
session_start();
require_once '../includes/header.php';
require_once '../classes/Admin.php';
require_once '../classes/User.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

$admin = new Admin();
$user = new User();
$users = $user->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AIM ID System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card i {
            font-size: 2em;
            color: #1976d2;
            margin-bottom: 10px;
        }

        .stat-card h3 {
            margin: 10px 0;
            color: #333;
        }

        .stat-card p {
            font-size: 1.5em;
            color: #1976d2;
            font-weight: 600;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .users-table th,
        .users-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .users-table th {
            background: #1976d2;
            color: white;
            font-weight: 500;
        }

        .users-table tr:hover {
            background: #f5f5f5;
        }

        .action-btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 0.9em;
            margin: 0 5px;
            display: inline-block;
        }

        .edit-btn {
            background: #1976d2;
        }

        .delete-btn {
            background: #dc3545;
        }

        .add-btn {
            background: #1976d2;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .search-box {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .search-box button {
            padding: 10px 20px;
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>Total Users</h3>
                <p><?php echo count($users); ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-star"></i>
                <h3>Total Points</h3>
                <p><?php echo array_sum(array_column($users, 'points')); ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3>Last Login</h3>
                <p><?php echo date('Y-m-d H:i'); ?></p>
            </div>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search users...">
            <button onclick="searchUsers()"><i class="fas fa-search"></i> Search</button>
        </div>

        <a href="add_user.php" class="add-btn"><i class="fas fa-plus"></i> Add New User</a>

        <table class="users-table">
            <thead>
                <tr>
                    <th>AIM ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Points</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['abc_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['points']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="modify_points.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn">
                            <i class="fas fa-star"></i> Points
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const tableRows = document.querySelectorAll('.users-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }

        // Add event listener for enter key in search input
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchUsers();
            }
        });
    </script>
</body>
</html>
