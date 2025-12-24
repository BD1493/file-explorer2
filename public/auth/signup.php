<?php require_once '../../src/json.php';
if($_POST){
    $users = getJSON('users.json');
    $users[] = ['username'=>$_POST['u'], 'password'=>$_POST['p']];
    saveJSON('users.json', $users);
    mkdir(STORAGE_PATH.'/users/'.$_POST['u'], 0777, true);
    header('Location: login.php'); exit;
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; margin-top:100px;">
    <h2>Sign Up</h2>
    <form method="POST"><input type="text" name="u" placeholder="Username" required><input type="password" name="p" placeholder="Password" required><button class="btn btn-primary">Register</button></form>
</div></body></html>
