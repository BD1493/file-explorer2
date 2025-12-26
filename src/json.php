<?php
define('DATA_PATH', __DIR__ . '/../public/data');
define('STORAGE_PATH', __DIR__ . '/../public/storage');

function getJSON($f) {
    $p = DATA_PATH . "/$f.json";
    if(!file_exists($p)) return [];
    return json_decode(file_get_contents($p), true) ?: [];
}

function saveJSON($f, $d) {
    file_put_contents(DATA_PATH . "/$f.json", json_encode($d, JSON_PRETTY_PRINT), LOCK_EX);
}
?>
