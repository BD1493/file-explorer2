<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
require_once '../../src/permissions.php';
requireLogin();

$user = currentUser();
$files = loadJson('../data/files.json');

if (!isset($_GET['id'])) die("File ID missing");
$fileId = $_GET['id'];

$file = null;
foreach ($files as $f) if ($f['id'] === $fileId) { $file = $f; break; }
if (!$file) die("File not found");

if (!checkPermission($fileId, $user, 'edit')) die("No permission to edit");

$filePath = "../../" . $file['path'];
$content = file_exists($filePath) ? file_get_contents($filePath) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    file_put_contents($filePath, $_POST['content']);
    $content = $_POST['content'];
    $success = "Saved changes!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit <?= htmlspecialchars($file['filename']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h2>Edit <?= htmlspecialchars($file['filename']) ?></h2>
<?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post">
    <textarea name="content" rows="20" cols="80"><?= htmlspecialchars($content) ?></textarea><br>
    <button>Save</button>
</form>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
