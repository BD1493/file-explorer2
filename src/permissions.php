<?php
require_once __DIR__ . '/json.php';

function canEdit($username, $filename, $owner) {
    // Owner can always edit
    if ($username === $owner) return true;
    
    // Check shares
    $shares = getShares();
    foreach ($shares as $share) {
        if ($share['filename'] === $filename && 
            $share['owner'] === $owner && 
            $share['shared_with'] === $username && 
            $share['permission'] === 'edit') {
            return true;
        }
    }
    return false;
}
?>
