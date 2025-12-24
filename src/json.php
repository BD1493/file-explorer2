<?php
define('BASE_PATH', dirname(__DIR__)); 
define('DATA_PATH', BASE_PATH . '/public/data');
define('STORAGE_PATH', BASE_PATH . '/public/storage');
function getJSON($f) { $p=DATA_PATH."/$f"; return file_exists($p)?json_decode(file_get_contents($p),true)??[]:[]; }
function saveJSON($f,$d) { file_put_contents(DATA_PATH."/$f", json_encode($d, JSON_PRETTY_PRINT)); }
?>
