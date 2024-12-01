<?php
require_once __DIR__ . '/../config/firebase-config.php';

class User {
    private $database;
    public $id;
    public $abc_id;
    public $name;
    public $email;
    public $points;
    public $phone;
    public $status;
    public $last_login;
    public $created_at;

    public function __construct() {
        $this->database = getFirebaseDatabase();
        $this->initializeFromSession();
    }

    public function initializeFromSession() {
        if (isset($_SESSION['user'])) {
            foreach ($_SESSION['user'] as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            return true;
        }
        return false;
    }

    public function register($name, $email, $password) {
        try {
            // Validate input
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            // Check if email already exists
            $users = $this->database->getReference('users')
                ->orderByChild('email')
                ->equalTo($email)
                ->getValue();

            if (!empty($users)) {
                throw new Exception("Email already registered");
            }

            // Generate unique ABC ID
            $abc_id = generateUniqueAbcId($this->database);

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare user data
            $userData = [
                'abc_id' => $abc_id,
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'points' => 0,
                'status' => 'active',
                'created_at' => time() * 1000, // Firebase uses milliseconds
                'last_login' => null
            ];

            // Save to Firebase
            $this->database->getReference('users/' . $abc_id)->set($userData);

            return [
                'success' => true,
                'message' => 'Registration successful',
                'abc_id' => $abc_id
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function login($email, $password) {
        try {
            // Get user by email
            $users = $this->database->getReference('users')
                ->orderByChild('email')
                ->equalTo($email)
                ->getValue();

            if (empty($users)) {
                throw new Exception("User not found");
            }

            // Get the first user (email should be unique)
            $user = current($users);
            $abc_id = key($users);

            // Verify password
            if (!password_verify($password, $user['password'])) {
                throw new Exception("Invalid password");
            }

            // Update last login
            $this->database->getReference('users/' . $abc_id . '/last_login')
                ->set(time() * 1000);

            // Set session
            $_SESSION['user'] = array_merge(['abc_id' => $abc_id], $user);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $user
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getUserById($abc_id) {
        try {
            $user = $this->database->getReference('users/' . $abc_id)->getValue();
            return $user ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function updateUser($abc_id, $data) {
        try {
            $updates = [];
            foreach ($data as $key => $value) {
                if (!empty($value) && $key !== 'password' && $key !== 'abc_id') {
                    $updates[$key] = $value;
                }
            }

            if (!empty($updates)) {
                $this->database->getReference('users/' . $abc_id)->update($updates);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAllUsers() {
        try {
            return $this->database->getReference('users')->getValue() ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function updatePoints($abc_id, $points) {
        try {
            $currentPoints = $this->database->getReference('users/' . $abc_id . '/points')->getValue() ?: 0;
            $newPoints = $currentPoints + $points;
            
            $this->database->getReference('users/' . $abc_id . '/points')->set($newPoints);
            
            // Record transaction
            $transaction = [
                'user_id' => $abc_id,
                'points' => $points,
                'type' => $points >= 0 ? 'credit' : 'debit',
                'timestamp' => time() * 1000
            ];
            
            $this->database->getReference('transactions')->push($transaction);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getTransactions($abc_id) {
        try {
            return $this->database->getReference('transactions')
                ->orderByChild('user_id')
                ->equalTo($abc_id)
                ->getValue() ?: [];
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
