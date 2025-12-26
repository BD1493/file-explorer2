<?php require_once '../../src/json.php'; $shares = getJSON('shares'); ?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav"><strong>í¼Ž Public Gallery</strong> <a href="dashboard.php" class="btn">Back</a></div>
<div class="container"><div class="grid">
    <?php foreach($shares as $s): if(isset($s['is_public']) && $s['is_public']): ?>
        <div class="file-item" onclick="location.href='editor.php?file=<?php echo $s['file']; ?>&owner=<?php echo $s['owner']; ?>&path=<?php echo $s['path']; ?>'">
            <span class="icon">í³„</span>
            <div><b><?php echo $s['file']; ?></b></div>
            <div style="font-size:12px; color:gray">by <?php echo $s['owner']; ?></div>
        </div>
    <?php endif; endforeach; ?>
</div></div></body></html>
