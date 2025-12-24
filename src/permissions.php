<?php
require_once 'json.php';

function checkPermission($fileId, $username, $required='view') {
    $shares = loadJson('shares.json');
    foreach($shares as $s) {
        if($s['file_id'] === $fileId && $s['shared_with'] === $username) {
            if($required === 'view') return true;
            if($required === 'edit' && $s['permission'] === 'edit') return true;
        }
    }
    return false;
}
