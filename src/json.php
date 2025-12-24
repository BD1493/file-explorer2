<?php
function loadJson(string $file): array {
    if (!file_exists($file)) file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function saveJson(string $file, array $data): void {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}
