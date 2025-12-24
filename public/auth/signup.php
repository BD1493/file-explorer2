<?php
require_once '../../src/json.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = getUsers();
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if exists
    foreach ($users as $u) {
        if ($u['username'] === $username) {
            $error = "Username taken.";
            break;
        }
    }

    if (!isset($error)) {
        // Add user
        $users[] = ['username' => $username, 'password' => $password]; // Plaintext as requested
        saveUsers($users);

        // Create User Directory in public/storage
        $userDir = STORAGE_PATH . '/users/' . $username;
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true);
        }

        $_SESSION['user'] = $username;
        header('Location: ../explorer/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Sign Up</h2>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
