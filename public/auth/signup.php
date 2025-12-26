<?php
require_once '../../src/json.php';
if($_POST){
    $users = getJSON('users'); $users[] = ['u'=>$_POST['u'], 'p'=>$_POST['p']]; saveJSON('users', $users);
    mkdir(STORAGE_PATH . '/users/' . $_POST['u'], 0777, true); header('Location: login.php'); exit;
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:320px; padding-top:100px;"><div class="card">
<h2>Sign Up</h2><form method="POST"><input name="u" placeholder="User"><input name="p" type="password"><button class="btn btn-primary" style="width:100%;">Register</button></form></div></div></body></html>
