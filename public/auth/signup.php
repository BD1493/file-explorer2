<?php
require_once '../../src/json.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = getUsers();
    $username = $_POST['username'];
    $password = $_POST['password'];
    foreach ($users as $u) {
        if ($u['username'] === $username) { $error = "Username taken."; break; }
    }
    if (!isset($error)) {
        $users[] = ['username' => $username, 'password' => $password];
        saveUsers($users);
        mkdir(STORAGE_PATH . '/users/' . $username, 0777, true);
        $_SESSION['user'] = $username;
        header('Location: ../explorer/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container"><h2>Sign Up</h2>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button class="btn">Sign Up</button></form>
</div></body></html>
