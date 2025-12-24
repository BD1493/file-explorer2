<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
require_once '../../src/permissions.php';
requireLogin();

$user = currentUser();
$files = loadJson('../../data/files.json');
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = trim($_POST['filename']);
    $isStudio = isset($_POST['is_studio']);
    if ($filename === '') die("Filename required");

    $path = "../../storage/users/$user/$filename";
    if ($isStudio) mkdir($path, 0755, true);
    else file_put_contents($path, '');

    $files[] = [
        "id" => 'f_' . bin2hex(random_bytes(4)),
        "owner" => $user,
        "filename" => $filename,
        "path" => "storage/users/$user/$filename",
        "type" => $isStudio ? 'studio' : 'file',
        "children" => $isStudio ? [] : null,
        "visibility" => "private",
        "created_at" => date('c')
    ];
    saveJson('../../data/files.json', $files);
    $success = "Created $filename successfully";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create File/Studio</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h2>Create File / Studio</h2>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post">
    <input name="filename" placeholder="File or Studio name" required>
    <label><input type="checkbox" name="is_studio"> Create as Studio</label>
    <button>Create</button>
</form>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
