<?php
require_once '../../src/json.php';
if($_POST){
    $users = getJSON('users');
    // Simple check to see if user exists could go here
    $users[] = ['u'=>$_POST['u'], 'p'=>$_POST['p']];
    saveJSON('users', $users);
    // Create User Directory
    if(!is_dir(STORAGE_PATH.'/users/'.$_POST['u'])){
        mkdir(STORAGE_PATH.'/users/'.$_POST['u'], 0777, true);
    }
    header('Location: login.php'); exit;
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:50px;">
    <div class="card">
        <h2>Sign Up</h2>
        <form method="POST">
            <input type="text" name="u" placeholder="New Username" required>
            <input type="password" name="p" placeholder="New Password" required>
            <button class="btn btn-primary" style="width:100%">Register</button>
        </form>
    </div>
</div></body></html>
