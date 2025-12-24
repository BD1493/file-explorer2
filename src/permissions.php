<?php
require_once __DIR__ . '/json.php';

function canAccess($username, $filename, $owner) {
    // 1. Owner always access
    if ($username === $owner) return 'edit';
    
    // 2. Check Unlocked Shares (Session based)
    if (isset($_SESSION['unlocked_shares'])) {
        foreach ($_SESSION['unlocked_shares'] as $s) {
            if ($s['filename'] === $filename && $s['owner'] === $owner) {
                return $s['permission']; // 'edit' or 'view'
            }
        }
    }

    // 3. Check Public Shares
    $shares = getShares();
    foreach ($shares as $s) {
        if ($s['filename'] === $filename && $s['owner'] === $owner && $s['is_public']) {
            return $s['permission'];
        }
    }
    
    return false;
}
?>
