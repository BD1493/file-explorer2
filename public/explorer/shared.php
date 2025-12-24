<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
requireLogin();

$user = currentUser();
$shares = loadJson('../../data/shares.json');
$files = loadJson('../../data/files.json');

$success = '';
$error = '';
$accessFile = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUser = trim($_POST['username']);
    $inputPass = trim($_POST['password']);

    foreach ($shares as $s) {
        if ($s['shared_with'] === $inputUser && $s['password'] === $inputPass) {
            foreach ($files as $f) {
                if ($f['id'] === $s['file_id']) {
                    $accessFile = $f;
                    break 2;
                }
            }
        }
    }
    if ($accessFile) $success = "Access granted to {$accessFile['filename']}";
    else $error = "Invalid username or password";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Get Shared File</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h2>Access Shared File</h2>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <input name="username" placeholder="Username" required><br>
    <input name="password" placeholder="Password" required><br>
    <button>Get File</button>
</form>

<?php if ($accessFile): ?>
    <a href="edit.php?id=<?= $accessFile['id'] ?>" class="btn">
        <?= htmlspecialchars($accessFile['filename']) ?> (<?= $accessFile['type'] ?>)
    </a>
<?php endif; ?>

<a href="dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
