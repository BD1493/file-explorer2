<?php
require_once '../../src/json.php';
if($_POST){
    $users = getJSON('users');
    foreach($users as $u) { if($u['u'] === $_POST['u']) $error = "Username taken."; }
    
    if(!isset($error)) {
        $users[] = ['u'=>$_POST['u'], 'p'=>$_POST['p']];
        saveJSON('users', $users);
        mkdir(STORAGE_PATH . '/users/' . $_POST['u'], 0777, true);
        header('Location: login.php'); exit;
    }
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Sign Up</title></head><body>
<div class="modal-overlay">
    <div class="modal">
        <h2>Sign Up</h2>
        <?php if(isset($error)) echo "<div style='color:red; margin-bottom:10px;'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="u" placeholder="New Username" required>
            <input type="password" name="p" placeholder="New Password" required>
            <button class="btn btn-primary" style="width:100%">Register</button>
        </form>
        <div style="margin-top:15px; font-size:13px;">
            <a href="login.php" style="color:var(--blue)">Back to Login</a>
        </div>
    </div>
</div></body></html>
