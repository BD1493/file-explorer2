<?php require_once '../../src/auth.php'; $s = getJSON('shares.json'); ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <div class="nav"><h2>Public Gallery</h2><a href="dashboard.php" class="btn btn-secondary">Back</a></div>
    <?php foreach($s as $sh): if($sh['is_public']): ?>
        <div class="file-item">
            <span><?php echo $sh['filename']; ?> (by <?php echo $sh['owner']; ?>)</span>
            <a href="edit.php?file=<?php echo urlencode($sh['filename']); ?>&owner=<?php echo urlencode($sh['owner']); ?>" class="btn btn-primary">Open</a>
        </div>
    <?php endif; endforeach; ?>
</div></body></html>
