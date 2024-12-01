<?php
require_once __DIR__ . '/../config/firebase.php';

class FirebaseUser {
    private $database;
    
    public function __construct() {
        $this->database = getDatabase();
    }
    
    public function createUser($userData) {
        try {
            $abcId = generateUniqueAbcId($this->database);
            
            $newUser = [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'role' => 'user',
                'status' => 'active',
                'created_at' => time() * 1000 // Current timestamp in milliseconds
            ];
            
            $this->database->getReference('users/' . $abcId)->set($newUser);
            return ['success' => true, 'abc_id' => $abcId];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getUserById($abcId) {
        try {
            $snapshot = $this->database->getReference('users/' . $abcId)->getValue();
            return $snapshot ? $snapshot : null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function updateUser($abcId, $userData) {
        try {
            $updates = [];
            foreach ($userData as $key => $value) {
                if (!empty($value)) {
                    $updates[$key] = $value;
                }
            }
            
            if (!empty($updates)) {
                $this->database->getReference('users/' . $abcId)->update($updates);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function deleteUser($abcId) {
        try {
            $this->database->getReference('users/' . $abcId)->remove();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getAllUsers() {
        try {
            $snapshot = $this->database->getReference('users')->getValue();
            return $snapshot ? $snapshot : [];
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getUsersByStatus($status) {
        try {
            // In Firebase, we need to retrieve all users and filter in PHP
            $users = $this->getAllUsers();
            return array_filter($users, function($user) use ($status) {
                return $user['status'] === $status;
            });
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function searchUsers($searchTerm) {
        try {
            $users = $this->getAllUsers();
            return array_filter($users, function($user) use ($searchTerm) {
                return (
                    stripos($user['name'], $searchTerm) !== false ||
                    stripos($user['email'], $searchTerm) !== false
                );
            });
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
