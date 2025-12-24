<?php
require_once '../../src/auth.php';
requireLogin();
$user = currentUser();
$userDir = STORAGE_PATH . '/users/' . $user;

// Scan directory for files
$files = array_diff(scandir($userDir), array('.', '..'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/app.js"></script>
</head>
<body>
<div class="container">
    <div class="nav">
        <span>Hello, <b><?php echo htmlspecialchars($user); ?></b></span>
        <a href="shared.php">Shared With Me</a>
        <a href="../auth/logout.php" style="float:right; color:red;">Logout</a>
    </div>

    <div style="margin-bottom: 20px;">
        <a href="create.php" class="btn">Make a File</a>
        <a href="upload.php" class="btn btn-secondary">Upload File</a>
    </div>

    <h3>My Files</h3>
    <ul class="file-list">
        <?php foreach ($files as $file): ?>
            <li class="file-item">
                <span><?php echo htmlspecialchars($file); ?></span>
                <div>
                    <a href="edit.php?file=<?php echo urlencode($file); ?>" class="btn" style="padding: 5px 10px;">Edit</a>
                    <a href="share.php?file=<?php echo urlencode($file); ?>" class="btn btn-secondary" style="padding: 5px 10px;">Share</a>
                </div>
            </li>
        <?php endforeach; ?>
        <?php if(empty($files)): ?>
            <p>No files found.</p>
        <?php endif; ?>
    </ul>
    
    <div style="margin-top:30px; border-top:1px solid #ccc; padding-top:10px;">
        <h4>Public Data Links (Insecure as requested)</h4>
        <a href="/public/data/users.json" target="_blank">View Users JSON</a> | 
        <a href="/public/data/shares.json" target="_blank">View Shares JSON</a>
    </div>
</div>
</body>
</html>
