<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
requireLogin();

$user = currentUser();
$files = loadJson('../../data/files.json');
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $upload = $_FILES['file'];
    $filename = basename($upload['name']);
    $targetPath = "../../storage/users/$user/$filename";
    move_uploaded_file($upload['tmp_name'], $targetPath);

    $files[] = [
        "id" => 'f_' . bin2hex(random_bytes(4)),
        "owner" => $user,
        "filename" => $filename,
        "path" => "storage/users/$user/$filename",
        "type" => 'file',
        "children" => null,
        "visibility" => "private",
        "created_at" => date('c')
    ];
    saveJson('../../data/files.json', $files);
    $success = "Uploaded $filename successfully";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h2>Upload File</h2>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button>Upload</button>
</form>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
