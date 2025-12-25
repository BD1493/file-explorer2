<?php
define('DATA_PATH', __DIR__ . '/../public/data');
define('STORAGE_PATH', __DIR__ . '/../public/storage');

function getJSON($file) {
    $path = DATA_PATH . "/$file.json";
    if (!file_exists($path)) return [];
    $content = file_get_contents($path);
    return json_decode($content, true) ?? [];
}

function saveJSON($file, $data) {
    $path = DATA_PATH . "/$file.json";
    // Lock file to prevent write conflicts
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}
?>
