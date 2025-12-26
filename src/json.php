<?php
define('DATA_PATH', __DIR__ . '/../public/data');
define('STORAGE_PATH', __DIR__ . '/../public/storage');

// --- SELF REPAIR SYSTEM ---
// If the folder is empty or files are missing, recreate them immediately.
if (!file_exists(DATA_PATH)) { mkdir(DATA_PATH, 0777, true); }

if (!file_exists(DATA_PATH.'/users.json')) {
    file_put_contents(DATA_PATH.'/users.json', '[]');
}
if (!file_exists(DATA_PATH.'/shares.json')) {
    file_put_contents(DATA_PATH.'/shares.json', '[]');
}
if (!file_exists(DATA_PATH.'/chats.json')) {
    file_put_contents(DATA_PATH.'/chats.json', '{}');
}

function getJSON($file) {
    $path = DATA_PATH . "/$file.json";
    if (!file_exists($path)) return []; // Should not happen due to self-repair above
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function saveJSON($file, $data) {
    $path = DATA_PATH . "/$file.json";
    // LOCK_EX prevents data corruption
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}
?>
