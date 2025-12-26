<?php
define('DATA_PATH', __DIR__ . '/../public/data');
define('STORAGE_PATH', __DIR__ . '/../public/storage');

function getJSON($filename) {
    $path = DATA_PATH . "/$filename.json";
    if (!file_exists($path)) return [];
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function saveJSON($filename, $data) {
    $path = DATA_PATH . "/$filename.json";
    // LOCK_EX prevents two people from writing at the same time
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}
?>
