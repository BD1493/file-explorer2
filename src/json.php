<?php
function loadJson($fileName) {
    $fullPath = __DIR__ . '/../public/data/' . $fileName;
    if(!file_exists($fullPath)) return [];
    $content = file_get_contents($fullPath);
    return json_decode($content, true) ?? [];
}

function saveJson($fileName, $data) {
    $fullPath = __DIR__ . '/../public/data/' . $fileName;
    file_put_contents($fullPath, json_encode($data, JSON_PRETTY_PRINT));
}
