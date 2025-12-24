<?php
function loadJson($filePath) {
    $fullPath = __DIR__ . '/../public/data/' . basename($filePath);
    if(!file_exists($fullPath)) return [];
    $content = file_get_contents($fullPath);
    return json_decode($content, true) ?? [];
}

function saveJson($filePath, $data) {
    $fullPath = __DIR__ . '/../public/data/' . basename($filePath);
    file_put_contents($fullPath, json_encode($data, JSON_PRETTY_PRINT));
}
