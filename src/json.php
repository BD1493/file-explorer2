<?php
// Base paths
$dataFile = __DIR__ . '/../data/users.json'; // correct path
$storageDir = __DIR__ . '/../storage/users';

// Ensure data file exists
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

// Function to read JSON
function readData() {
    global $dataFile;
    return json_decode(file_get_contents($dataFile), true);
}

// Function to write JSON
function writeData($data) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}
?>
