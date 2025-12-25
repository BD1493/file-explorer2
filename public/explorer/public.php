<?php require_once '../../src/json.php'; $shares = getJSON('shares'); ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav"><strong>Public Gallery</strong> <a href="dashboard.php" class="btn">Back</a></div>
<div class="container"><div class="grid">
    <?php foreach($shares as $s): if($s['public']): ?>
        <div class="file-card" onclick="location.href='editor.php?file=<?php echo $s['file']; ?>&path=<?php echo $s['owner']; ?>'">
            <span class="file-icon">í³„</span>
            <b><?php echo $s['file']; ?></b><br><small>by <?php echo $s['owner']; ?></small>
        </div>
    <?php endif; endforeach; ?>
</div></div></body></html>
