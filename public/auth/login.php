<?php require_once '../../src/json.php'; session_start();
if($_POST){
    foreach(getJSON('users.json') as $u) {
        if($u['username']===$_POST['u'] && $u['password']===$_POST['p']){
            $_SESSION['user']=$_POST['u']; header('Location: ../explorer/dashboard.php'); exit;
        }
    }
    $err = "Invalid login.";
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; margin-top:100px;">
    <h2>Login</h2>
    <?php if(isset($err)) echo "<div class='alert' style='background:#fce8e6; color:#c5221f;'>$err</div>"; ?>
    <form method="POST"><input type="text" name="u" placeholder="Username" required><input type="password" name="p" placeholder="Password" required><button class="btn btn-primary">Login</button></form>
    <p>New? <a href="signup.php">Create Account</a></p>
</div></body></html>
