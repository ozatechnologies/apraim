<?php
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

function getFirebaseDatabase() {
    $factory = (new Factory)
        ->withDatabaseUri('https://oza-contents-default-rtdb.firebaseio.com');
    
    return $factory->createDatabase();
}

// Helper function to generate unique ABC ID
function generateUniqueAbcId($database) {
    $users = $database->getReference('users')->getValue();
    $maxId = 0;
    
    if ($users) {
        foreach ($users as $userId => $user) {
            $currentId = intval(substr($userId, 4)); // Extract number from ABC-XXXX
            $maxId = max($maxId, $currentId);
        }
    }
    
    return sprintf("ABC-%04d", $maxId + 1);
}
?>
