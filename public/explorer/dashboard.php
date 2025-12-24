<?php require_once '../../src/auth.php'; requireLogin();
$user = currentUser();
$userDir = STORAGE_PATH . '/users/' . $user;
if (!file_exists($userDir)) mkdir($userDir, 0777, true);
$files = array_diff(scandir($userDir), array('.', '..')); ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <div class="nav">
        <div><strong>My Drive</strong> (<?php echo $user; ?>)</div>
        <div>
            <a href="public_files.php" class="btn btn-secondary">Gallery</a>
            <a href="shared.php" class="btn btn-secondary">Access Shared</a>
            <a href="manage_shares.php" class="btn btn-secondary">Shares</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
    <a href="create.php" class="btn btn-primary" style="margin-bottom:20px;">+ New File</a>
    <div class="file-list">
        <?php foreach($files as $f): ?>
            <div class="file-item">
                <span>í³„ <?php echo htmlspecialchars($f); ?></span>
                <div>
                    <a href="edit.php?file=<?php echo urlencode($f); ?>" class="btn btn-secondary">Open</a>
                    <a href="share.php?file=<?php echo urlencode($f); ?>" class="btn btn-primary">Share</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div></body></html>
