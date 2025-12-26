<?php require_once '../../src/json.php'; $shares = getJSON('shares'); ?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Public Gallery</title></head><body>
<div class="nav">
    <div class="logo">Public Gallery</div>
    <a href="dashboard.php" class="btn">Back to Drive</a>
</div>
<div class="container">
    <div class="grid">
        <?php foreach($shares as $s): if(isset($s['is_public']) && $s['is_public']): 
             $link = "editor.php?file=" . urlencode($s['file']) . "&owner=" . urlencode($s['owner']) . "&path=" . urlencode($s['path']);
        ?>
        <div class="file-card" onclick="location.href='<?php echo $link; ?>'">
            <span class="icon"></span>
            <div class="filename"><?php echo $s['file']; ?></div>
            <div class="meta">Owner: <?php echo $s['owner']; ?></div>
            <div class="meta" style="color:green">Public Access</div>
        </div>
        <?php endif; endforeach; ?>
    </div>
</div></body></html>
