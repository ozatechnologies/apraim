<?php
require_once 'classes/CSRFProtection.php';

// Start session
session_start();

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        CSRFProtection::validateToken($_POST['csrf_token']);
    } catch (Exception $e) {
        // Log error or handle invalid CSRF token
        die("Invalid logout request");
    }
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
