<?php require_once '../../src/json.php';
if($_POST){
    $users = getJSON('users'); $users[] = ['u'=>$_POST['u'], 'p'=>$_POST['p']];
    saveJSON('users', $users); mkdir(STORAGE_PATH.'/users/'.$_POST['u'], 0777, true);
    header('Location: login.php'); exit;
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:10vh;">
    <div style="background:white; padding:30px; border-radius:8px; border:1px solid #dadce0;">
        <h2>Create Account</h2>
        <form method="POST"><input type="text" name="u" placeholder="Username" required><input type="password" name="p" placeholder="Password" required><button class="btn btn-primary" style="width:100%">Sign Up</button></form>
    </div>
</div></body></html>
