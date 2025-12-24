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

    if ($username === '' || $password === '') {
        $error = "All fields are required.";
    } else {
        $users = loadJson('../public/data/users.json');

        foreach ($users as $u) {
            if ($u['username'] === $username) {
                $error = "Username already exists.";
                break;
            }
        }

        if ($error === '') {
            $users[] = [
                "username"   => $username,
                "password"   => password_hash($password, PASSWORD_DEFAULT),
                "created_at" => date('c')
            ];

            saveJson('../public/data/users.json', $users);

            $userDir = "../public/storage/users/$username";
            if (!is_dir($userDir)) {
                mkdir($userDir, 0755, true);
            }

            $_SESSION['user'] = $username;
            header("Location: /explorer/dashboard.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Sign Up</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h2>Create Account</h2>

<?php if ($error): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
  <input name="username" placeholder="Username" required>
  <input name="password" type="password" placeholder="Password" required>
  <button>Create Account</button>
</form>

<p>Already have an account? <a href="/auth/login.php">Login</a></p>
</body>
</html>
