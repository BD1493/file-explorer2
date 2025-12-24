<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
require_once '../../src/permissions.php';
requireLogin();

$user = currentUser();
$shares = loadJson('../data/shares.json');
$files = loadJson('../data/files.json');

if (!isset($_GET['id'])) die("File ID missing");
$fileId = $_GET['id'];

$file = null;
foreach ($files as $f) if ($f['id'] === $fileId) { $file = $f; break; }
if (!$file) die("File not found");
if ($file['owner'] !== $user) die("You do not own this file");

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shareUser = trim($_POST['username']);
    $perm = $_POST['permission'] ?? 'view';
    $visibility = $_POST['visibility'] ?? 'private'; // NEW
    $password = trim($_POST['password']) ?: substr(bin2hex(random_bytes(2)),0,4);

    $shares[] = [
        'file_id'=>$fileId,
        'shared_with'=>$shareUser,
        'permission'=>$perm,
        'password'=>$password,
        'shared_by'=>$user,
        'visibility'=>$visibility, // NEW
        'created_at'=>date('c')
    ];
    saveJson('../data/shares.json',$shares);
    $success = "File shared with $shareUser (password: $password, visibility: $visibility)";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Share <?= htmlspecialchars($file['filename']) ?></title>
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>
<h2>Share <?= htmlspecialchars($file['filename']) ?></h2>
<?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post">
    <input name="username" placeholder="Username to share with"><br>
    <select name="permission">
        <option value="view">View only</option>
        <option value="edit">Edit</option>
    </select><br>
    <select name="visibility"> <!-- NEW -->
        <option value="private">Private</option>
        <option value="public">Public</option>
    </select><br>
    <input name="password" placeholder="Optional password"><br>
    <button>Share</button>
</form>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
