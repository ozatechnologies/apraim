<?php
require_once __DIR__ . '/../config/firebase-config.php';

class Admin {
    private $database;

    public function __construct() {
        $this->database = getFirebaseDatabase();
    }

    public function login($email, $password) {
        try {
            // Get admin by email
            $admins = $this->database->getReference('admins')
                ->orderByChild('email')
                ->equalTo($email)
                ->getValue();

            if (empty($admins)) {
                throw new Exception("Admin not found");
            }

            // Get the first admin (email should be unique)
            $admin = current($admins);
            $adminId = key($admins);

            // Verify password
            if (!password_verify($password, $admin['password'])) {
                throw new Exception("Invalid password");
            }

            // Update last login
            $this->database->getReference('admins/' . $adminId . '/last_login')
                ->set(time() * 1000);

            // Remove password from session data
            unset($admin['password']);
            
            // Set session
            $_SESSION['admin'] = array_merge(['id' => $adminId], $admin);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'admin' => $admin
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getAllUsers($limit = 10, $startAfter = null) {
        try {
            $ref = $this->database->getReference('users')
                ->orderByChild('created_at');
            
            if ($startAfter) {
                $ref = $ref->startAfter($startAfter);
            }
            
            $users = $ref->limitToLast($limit)->getValue() ?: [];
            return array_reverse($users); // To maintain descending order
            
        } catch (Exception $e) {
            return [];
        }
    }

    public function searchUsers($searchTerm) {
        try {
            $users = $this->database->getReference('users')->getValue() ?: [];
            
            return array_filter($users, function($user) use ($searchTerm) {
                $searchLower = strtolower($searchTerm);
                return (
                    strpos(strtolower($user['name']), $searchLower) !== false ||
                    strpos(strtolower($user['email']), $searchLower) !== false ||
                    strpos(strtolower($user['abc_id']), $searchLower) !== false
                );
            });
            
        } catch (Exception $e) {
            return [];
        }
    }

    public function updateUserPoints($abc_id, $points, $description) {
        try {
            $userRef = $this->database->getReference('users/' . $abc_id);
            $user = $userRef->getValue();
            
            if (!$user) {
                throw new Exception("User not found");
            }

            // Update points
            $currentPoints = $user['points'] ?? 0;
            $newPoints = $currentPoints + $points;
            
            // Create transaction
            $transaction = [
                'user_id' => $abc_id,
                'points' => $points,
                'type' => $points >= 0 ? 'credit' : 'debit',
                'description' => $description,
                'timestamp' => time() * 1000,
                'admin_id' => $_SESSION['admin']['id'] ?? null
            ];
            
            // Update user points and add transaction
            $updates = [
                'users/' . $abc_id . '/points' => $newPoints,
                'transactions/' . uniqid() => $transaction
            ];
            
            $this->database->getReference()->update($updates);
            
            return [
                'success' => true,
                'message' => 'Points updated successfully',
                'new_points' => $newPoints
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getUserTransactions($abc_id, $limit = 10) {
        try {
            return $this->database->getReference('transactions')
                ->orderByChild('user_id')
                ->equalTo($abc_id)
                ->limitToLast($limit)
                ->getValue() ?: [];
                
        } catch (Exception $e) {
            return [];
        }
    }

    public function getSystemStats() {
        try {
            $users = $this->database->getReference('users')->getValue() ?: [];
            $transactions = $this->database->getReference('transactions')->getValue() ?: [];
            
            return [
                'total_users' => count($users),
                'total_transactions' => count($transactions),
                'active_users' => count(array_filter($users, fn($u) => $u['status'] === 'active')),
                'total_points' => array_reduce($users, fn($sum, $u) => $sum + ($u['points'] ?? 0), 0)
            ];
            
        } catch (Exception $e) {
            return [
                'total_users' => 0,
                'total_transactions' => 0,
                'active_users' => 0,
                'total_points' => 0
            ];
        }
    }
}
?>
