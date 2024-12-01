<?php
require_once 'config/database.php';

try {
    $conn = getDatabaseConnection();
    echo "Database connection successful!";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
