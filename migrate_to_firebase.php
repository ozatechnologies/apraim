<?php
require_once 'config/database.php';
require_once 'config/firebase.php';

function migrateToFirebase() {
    try {
        // Get MySQL connection
        $mysqlConn = getDatabaseConnection();
        
        // Get Firebase connection
        $firebaseDb = getDatabase();
        
        // 1. Migrate Users
        $stmt = $mysqlConn->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $user) {
            $userData = [
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'status' => $user['status'],
                'created_at' => convertToTimestamp($user['created_at']),
                // Add any other fields you have in your users table
            ];
            
            // Store in Firebase using abc_id as the key
            $firebaseDb->getReference('users/' . $user['abc_id'])->set($userData);
        }
        
        // 2. Migrate Transactions
        $stmt = $mysqlConn->query("SELECT * FROM transactions");
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($transactions as $transaction) {
            $transactionData = [
                'user_id' => $transaction['user_id'],
                'type' => $transaction['type'],
                'amount' => $transaction['amount'],
                'date' => convertToTimestamp($transaction['date']),
                'description' => $transaction['description']
                // Add any other transaction fields
            ];
            
            // Store in Firebase using transaction ID as key
            $firebaseDb->getReference('transactions/' . $transaction['id'])->set($transactionData);
        }
        
        // 3. Migrate Admin Settings
        $stmt = $mysqlConn->query("SELECT * FROM admin_settings");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($settings as $setting) {
            $settingData = [
                'setting_name' => $setting['setting_name'],
                'setting_value' => $setting['setting_value'],
                'updated_at' => convertToTimestamp($setting['updated_at'])
            ];
            
            $firebaseDb->getReference('admin/settings/' . $setting['id'])->set($settingData);
        }
        
        echo "Migration completed successfully!\n";
        
    } catch (Exception $e) {
        die("Migration failed: " . $e->getMessage());
    }
}

// Run the migration
migrateToFirebase();
?>
