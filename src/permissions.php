<?php
require_once 'json.php';
function checkPermission(string $fileId, string $user, string $requiredPermission = 'view'): bool {
    $files = loadJson(__DIR__ . '/../data/files.json');
    $shares = loadJson(__DIR__ . '/../data/shares.json');
    $file = null;
    foreach ($files as $f) if ($f['id'] === $fileId) { $file = $f; break; }
    if (!$file) return false;
    if ($file['owner'] === $user) return true;
    if ($file['visibility'] === 'public' && $requiredPermission === 'view') return true;
    foreach ($shares as $s) {
        if ($s['file_id'] === $fileId && $s['shared_with'] === $user) {
            if ($requiredPermission === 'view') return true;
            if ($requiredPermission === 'edit' && $s['permission'] === 'edit') return true;
        }
    }
    return false;
}
