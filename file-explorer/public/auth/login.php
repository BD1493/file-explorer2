<?php
require_once '../../src/json.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = getUsers();
    $username = $_POST['username'];
    $password = $_POST['password'];

    foreach ($users as $u) {
        if ($u['username'] === $username && $u['password'] === $password) {
            $_SESSION['user'] = $username;
            header('Location: ../explorer/dashboard.php');
            exit;
        }
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
    </form>
    <p>No account? <a href="signup.php">Sign Up</a></p>
</div>
</body>
</html>
