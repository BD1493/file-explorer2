<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
requireLogin();

$user = currentUser();
$files = loadJson('../data/files.json');

$publicFiles = array_filter($files, fn($f)=>$f['visibility']==='public');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Public Files</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h2>Public Files</h2>
<ul>
<?php foreach($publicFiles as $f): ?>
    <li>
        <?= htmlspecialchars($f['filename']) ?> (<?= $f['type'] ?>)
        <?php if(checkPermission($f['id'],$user,'edit')): ?>
            <a href="edit.php?id=<?= $f['id'] ?>" class="btn">Edit</a>
        <?php else: ?>
            <a href="edit.php?id=<?= $f['id'] ?>" class="btn">View</a>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
