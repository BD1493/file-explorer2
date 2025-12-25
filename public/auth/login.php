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
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:50px;">
    <div class="card">
        <h2>Login</h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="u" placeholder="Username" required>
            <input type="password" name="p" placeholder="Password" required>
            <button class="btn btn-primary" style="width:100%">Sign In</button>
        </form>
        <p><a href="signup.php">Create Account</a></p>
    </div>
</div></body></html>
