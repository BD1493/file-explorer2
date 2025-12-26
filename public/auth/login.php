<?php
require_once '../../src/json.php'; session_start();
if($_POST){
    $users = getJSON('users');
    foreach($users as $u) if($u['u']==$_POST['u'] && $u['p']==$_POST['p']){ $_SESSION['user']=$u['u']; header('Location: ../explorer/dashboard.php'); exit; }
    $err = "Invalid!";
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:320px; padding-top:100px;"><div class="card">
<h2>Mega Drive</h2><form method="POST"><input name="u" placeholder="User"><input name="p" type="password"><button class="btn btn-primary" style="width:100%;">Login</button></form>
<br><a href="signup.php">Create Account</a></div></div></body></html>
