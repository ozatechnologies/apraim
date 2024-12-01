<?php
require_once 'config/database.php';

try {
    $conn = getDatabaseConnection();
    
    // Check if admins table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'admins'");
    if ($stmt->rowCount() == 0) {
        echo "Admins table does not exist!\n";
        
        // Create admins table
        $conn->exec("
            CREATE TABLE IF NOT EXISTS admins (
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "Created admins table.\n";
    } else {
        echo "Admins table exists.\n";
    }
    
    // Check if admin user exists
    $stmt = $conn->query("SELECT * FROM admins WHERE username = 'admin'");
    if ($stmt->rowCount() == 0) {
        echo "Admin user does not exist!\n";
        
        // Create admin user
        $hashedPassword = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hashedPassword]);
        echo "Created admin user with username 'admin' and password 'password'\n";
    } else {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Admin user exists.\n";
        
        // Update admin password
        $hashedPassword = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
        $stmt->execute([$hashedPassword]);
        echo "Updated admin password to 'password'\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
