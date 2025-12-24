<?php
require_once __DIR__ . '/json.php';
function canAccess($username, $filename, $owner) {
    if ($username === $owner) return 'edit';
    $shares = getJSON('shares.json');
    if (isset($_SESSION['unlocked_shares'])) {
        foreach ($_SESSION['unlocked_shares'] as $s) {
            if ($s['filename'] === $filename && $s['owner'] === $owner) return $s['permission'];
        }
    }
    foreach ($shares as $s) {
        if ($s['filename'] === $filename && $s['owner'] === $owner && ($s['is_public'] ?? false)) return $s['permission'];
    }
    return false;
}
?>
