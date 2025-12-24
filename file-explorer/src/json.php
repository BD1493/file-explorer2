<?php
// Define Absolute Paths to prevent "../" errors
define('BASE_PATH', dirname(__DIR__)); // /var/www/html
define('DATA_PATH', BASE_PATH . '/public/data');
define('STORAGE_PATH', BASE_PATH . '/public/storage');

function getJSON($filename) {
    $path = DATA_PATH . '/' . $filename;
    if (!file_exists($path)) {
        return [];
    }
    $json = file_get_contents($path);
    return json_decode($json, true) ?? [];
}

function saveJSON($filename, $data) {
    $path = DATA_PATH . '/' . $filename;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
}

function getUsers() { return getJSON('users.json'); }
function saveUsers($users) { saveJSON('users.json', $users); }

function getShares() { return getJSON('shares.json'); }
function saveShares($shares) { saveJSON('shares.json', $shares); }

// Helper to generate random password
function generateRandomString($length = 4) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
?>
