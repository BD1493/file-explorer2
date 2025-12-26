<?php require_once '../../src/json.php'; $shares = getJSON('shares'); ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav"><b>í¼Ž Public Files</b> <a href="dashboard.php" class="btn">Back</a></div>
<div class="container"><div class="grid">
    <?php foreach($shares as $s): if(isset($s['is_public']) && $s['is_public']): ?>
        <div class="file-card" onclick="location.href='editor.php?file=<?=$s['file']?>&owner=<?=$s['owner']?>&path=<?=$s['path']?>'">
            <div class="icon">í³„</div><div><?=$s['file']?></div><small>by <?=$s['owner']?></small>
        </div>
    <?php endif; endforeach; ?>
</div></div></body></html>
