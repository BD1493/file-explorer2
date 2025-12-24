<?php require_once '../../src/auth.php'; requireLogin();
$s = getJSON('shares.json');
if(isset($_GET['del'])){ array_splice($s, $_GET['del'], 1); saveJSON('shares.json', $s); header("Location: manage_shares.php"); exit; } ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <div class="nav"><h2>My Active Shares</h2><a href="dashboard.php" class="btn btn-secondary">Back</a></div>
    <?php foreach($s as $i=>$sh): if($sh['owner']===currentUser()): ?>
        <div class="file-item">
            <span><b><?php echo $sh['filename']; ?></b> (Alias: <?php echo $sh['share_name']?:'Public'; ?>)</span>
            <a href="?del=<?php echo $i; ?>" class="btn btn-danger">Unshare</a>
        </div>
    <?php endif; endforeach; ?>
</div></body></html>
