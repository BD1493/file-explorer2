<?php require_once '../../src/json.php'; session_start();
if($_POST){
    foreach(getJSON('users') as $u) {
        if($u['u']===$_POST['u'] && $u['p']===$_POST['p']){
            $_SESSION['user']=$_POST['u']; header('Location: ../explorer/dashboard.php'); exit;
        }
    }
} ?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:10vh;">
    <div style="background:white; padding:30px; border-radius:8px; border:1px solid #dadce0;">
        <h2>Login</h2>
        <form method="POST"><input type="text" name="u" placeholder="Username" required><input type="password" name="p" placeholder="Password" required><button class="btn btn-primary" style="width:100%">Sign In</button></form>
        <p>No account? <a href="signup.php">Register</a></p>
    </div>
</div></body></html>
