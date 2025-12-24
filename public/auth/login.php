<?php
require_once '../../src/json.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = getUsers();
    foreach ($users as $u) {
        if ($u['username'] === $_POST['username'] && $u['password'] === $_POST['password']) {
            $_SESSION['user'] = $_POST['username'];
            header('Location: ../explorer/dashboard.php');
            exit;
        }
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container"><h2>Login</h2>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button class="btn">Login</button></form>
    <p><a href="signup.php">Sign Up</a></p>
</div></body></html>
