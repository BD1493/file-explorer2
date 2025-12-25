<?php require_once '../../src/auth.php'; require_once '../../src/permissions.php'; requireLogin();
if($_POST){
    $access = canAccess(currentUser(), $_POST['file'], $_POST['owner']);
    if($access === 'edit'){
        file_put_contents(STORAGE_PATH."/users/{$_POST['owner']}/{$_POST['file']}", $_POST['content']);
        echo json_encode(['status'=>'success', 'time'=>date('H:i:s')]); exit;
    }
} echo json_encode(['status'=>'error']);
