<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
require_once '../../src/permissions.php';
requireLogin();

$user = currentUser();
$files = loadJson('../public/data/files.json');

function displayFile($file) {
    echo "<li data-file-id='{$file['id']}'>";
    echo ($file['type']==='studio' ? "📁 " : "📄 ") . htmlspecialchars($file['filename']);
    if($file['type']==='studio' && !empty($file['children'])) {
        echo "<ul>";
        foreach($file['children'] as $child) displayFile($child);
        echo "</ul>";
    }
    if($file['owner'] === currentUser()) {
        echo " <button class='edit-btn'>Edit</button>";
        echo " <button class='share-btn'>Share</button>";
    }
    echo "</li>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <script src="/assets/js/app.js"></script>
</head>
<body>
<h2>Welcome, <?= htmlspecialchars($user) ?></h2>

<ul id="file-list">
<?php
foreach($files as $f) {
    if($f['owner'] === $user) displayFile($f);
}
?>
</ul>

<!-- Buttons -->
<a href="/explorer/create.php" class="btn">Create File/Studio</a>
<a href="/explorer/upload.php" class="btn">Upload File</a>
<a href="/explorer/shared.php" class="btn">Get Shared File</a> <!-- NEW THIRD BUTTON -->
<a href="/auth/logout.php" class="btn">Logout</a>

<!-- Modal for sharing -->
<div id="modal" class="hidden">
  <div id="modal-content">
    <span id="modal-close">&times;</span>
    <div id="modal-body"></div>
  </div>
</div>
</body>
</html>
