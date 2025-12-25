<?php require_once '../../src/auth.php'; requireLogin();
if($_POST){
    $shares = getJSON('shares.json'); $found = false;
    foreach($shares as $s){
        if($s['share_name']===$_POST['sn'] && ($s['is_public'] || $s['password']===$_POST['sp'])){
            $_SESSION['unlocked_shares'][] = $s; $found = true;
        }
    }
    if(!$found) $err = "Not found.";
} $unl = $_SESSION['unlocked_shares'] ?? []; ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <div class="nav"><h2>Find Private Share</h2><a href="dashboard.php" class="btn btn-secondary">Back</a></div>
    <form method="POST"><input type="text" name="sn" placeholder="Share Alias"><input type="text" name="sp" placeholder="Password"><button class="btn btn-primary">Unlock</button></form>
    <h3>Unlocked:</h3>
    <?php foreach($unl as $u): ?>
        <div class="file-item"><span><?php echo $u['filename']; ?></span> <a href="edit.php?file=<?php echo urlencode($u['filename']); ?>&owner=<?php echo urlencode($u['owner']); ?>" class="btn btn-secondary">Open</a></div>
    <?php endforeach; ?>
</div></body></html>
