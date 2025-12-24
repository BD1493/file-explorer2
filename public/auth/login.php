<?php
require_once '../../src/json.php';
session_start();

if (isset($_SESSION['user'])) {
    header("Location: /explorer/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $users = loadJson('../public/data/users.json');

    foreach ($users as $u) {
        if ($u['username'] === $username &&
            password_verify($password, $u['password'])) {

            $_SESSION['user'] = $username;
            header("Location: /explorer/dashboard.php");
            exit;
        }
    }

    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h2>Login</h2>

<?php if ($error): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
  <input name="username" placeholder="Username" required>
  <input name="password" type="password" required>
  <button>Login</button>
</form>

<p>No account? <a href="/auth/signup.php">Sign up</a></p>
</body>
</html>
