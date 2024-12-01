<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'abc_id_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Database Connection Function
function getDatabaseConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Function to generate unique ABC ID
function generateUniqueAbcId($conn) {
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(abc_id, 5) AS UNSIGNED)) as max_id FROM users");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nextId = $result['max_id'] ? $result['max_id'] + 1 : 1;
    return sprintf("ABC-%04d", $nextId);
}
?>
