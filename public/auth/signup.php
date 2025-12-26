<?php
require_once '../../src/json.php';
if($_POST){
    $users = getJSON('users');
    $userDir = STORAGE_PATH . '/users/' . $_POST['u'];
    
    // Check if user already exists
    $exists = false;
    foreach($users as $u) { if($u['u'] === $_POST['u']) $exists = true; }
    
    if(!$exists) {
        $users[] = ['u'=>$_POST['u'], 'p'=>$_POST['p']];
        saveJSON('users', $users);
        if(!is_dir($userDir)) mkdir($userDir, 0777, true);
        header('Location: login.php'); exit;
    } else {
        $err = "Username taken.";
    }
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="display:flex; justify-content:center; align-items:center; height:90vh;">
    <div class="card" style="width:100%; max-width:400px;">
        <h2>Create Account</h2>
        <?php if(isset($err)) echo "<p style='color:red'>$err</p>"; ?>
        <form method="POST">
            <input type="text" name="u" placeholder="Username" required>
            <input type="password" name="p" placeholder="Password" required>
            <button class="btn btn-primary" style="width:100%; margin-top:10px;">Register</button>
        </form>
        <p style="text-align:center;"><a href="login.php">Back to Login</a></p>
    </div>
</div></body></html>
