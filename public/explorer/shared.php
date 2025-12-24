<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
requireLogin();

$user = currentUser();
$shares = loadJson('../public/data/shares.json');
$files = loadJson('../public/data/files.json');

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
                    $accessPerm = $s['permission'];
                    break 2;
                }
            }
        }
    }

    if ($accessFile) $success = "Access granted to {$accessFile['filename']}";
    else $error = "Invalid username or password. You can request access.";
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

<?php if (!$accessFile && $error): ?>
    <a href="request.php" class="btn">Request Access</a>
<?php endif; ?>

<?php if ($accessFile): ?>
    <?php if ($accessPerm === 'edit'): ?>
        <a href="edit.php?id=<?= $accessFile['id'] ?>" class="btn">Edit <?= htmlspecialchars($accessFile['filename']) ?></a>
    <?php else: ?>
        <a href="edit.php?id=<?= $accessFile['id'] ?>" class="btn">View <?= htmlspecialchars($accessFile['filename']) ?></a>
    <?php endif; ?>
<?php endif; ?>

<a href="dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
