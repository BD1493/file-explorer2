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
    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Login</title></head><body>
<div class="modal-overlay">
    <div class="modal">
        <h2>Mega Drive</h2>
        <?php if(isset($error)) echo "<div style='color:red; margin-bottom:10px;'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="u" placeholder="Username" required>
            <input type="password" name="p" placeholder="Password" required>
            <button class="btn btn-primary" style="width:100%">Sign In</button>
        </form>
        <div style="margin-top:15px; font-size:13px;">
            <a href="signup.php" style="color:var(--blue)">Create Account</a>
        </div>
    </div>
</div></body></html>
