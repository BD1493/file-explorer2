<?php
require_once '../../src/json.php'; session_start();
if($_POST){
    $users = getJSON('users');
    foreach($users as $u){
        if($u['u'] === $_POST['u'] && $u['p'] === $_POST['p']){
            $_SESSION['user'] = $_POST['u'];
            header('Location: ../explorer/dashboard.php'); exit;
        }
    }
    $err = "Incorrect username or password.";
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="display:flex; justify-content:center; align-items:center; height:90vh;">
    <div class="card" style="width:100%; max-width:400px;">
        <h2>Sign In</h2>
        <?php if(isset($err)) echo "<p style='color:red'>$err</p>"; ?>
        <form method="POST">
            <input type="text" name="u" placeholder="Username" required>
            <input type="password" name="p" placeholder="Password" required>
            <button class="btn btn-primary" style="width:100%; margin-top:10px;">Login</button>
        </form>
        <p style="text-align:center; margin-top:15px;"><a href="signup.php">Create an Account</a></p>
    </div>
</div></body></html>
