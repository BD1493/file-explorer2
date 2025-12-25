<?php
define('DATA_PATH', __DIR__ . '/../public/data');
define('STORAGE_PATH', __DIR__ . '/../public/storage');
function getJSON($f) { $p=DATA_PATH."/$f.json"; return file_exists($p)?json_decode(file_get_contents($p),true)??[]:[]; }
function saveJSON($f,$d) { file_put_contents(DATA_PATH."/$f.json", json_encode($d, JSON_PRETTY_PRINT)); }
function canAccess($u, $file, $owner) {
    if ($u === $owner) return 'edit';
    $shares = getJSON('shares');
    foreach($shares as $s) {
        if($s['file']===$file && $s['owner']===$owner) {
            if($s['public'] || (isset($_SESSION['unlocked'][$s['alias']]) )) return $s['perm'];
        }
    }
    return false;
}
?>
