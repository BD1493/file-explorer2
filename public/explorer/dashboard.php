<?php
require_once '../../src/auth.php';
requireLogin();
$user = currentUser();
$userDir = STORAGE_PATH . '/users/' . $user;
if (!file_exists($userDir)) mkdir($userDir, 0777, true);
$files = array_diff(scandir($userDir), array('.', '..'));
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <div class="nav">
        <span>Hi, <b><?php echo htmlspecialchars($user); ?></b></span>
        <a href="shared.php" style="margin-left:20px;">Shared Files</a>
        <a href="../auth/logout.php" style="float:right; color:red;">Logout</a>
    </div>
    <div style="margin-bottom: 20px;">
        <a href="create.php" class="btn">Make File</a>
        <a href="upload.php" class="btn btn-secondary">Upload File</a>
    </div>
    <h3>My Files</h3>
    <ul class="file-list">
        <?php foreach ($files as $file): ?>
            <li class="file-item">
                <span><?php echo htmlspecialchars($file); ?></span>
                <div>
                    <a href="edit.php?file=<?php echo urlencode($file); ?>" class="btn">Edit</a>
                    <a href="share.php?file=<?php echo urlencode($file); ?>" class="btn btn-secondary">Share</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
