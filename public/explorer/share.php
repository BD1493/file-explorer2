<?php require_once '../../src/auth.php'; requireLogin();
if($_POST){
    $s = getJSON('shares.json');
    $s[] = ['owner'=>currentUser(), 'filename'=>$_GET['file'], 'share_name'=>$_POST['sn'], 'password'=>$_POST['sp'], 'permission'=>$_POST['p'], 'is_public'=>isset($_POST['pub'])];
    saveJSON('shares.json', $s);
    $msg = "File Shared!";
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css">
<script>function upd(){ let p=document.getElementById('pub').checked; document.getElementById('priv').style.display=p?'none':'block'; }</script></head><body>
<div class="container" style="max-width:500px;">
    <h2>Share Settings</h2>
    <?php if(isset($msg)) echo "<div class='alert'>$msg</div>"; ?>
    <form method="POST">
        <label><input type="checkbox" name="pub" id="pub" onchange="upd()"> Make Public (Everyone can see)</label>
        <div id="priv">
            <input type="text" name="sn" placeholder="Share Alias (Friend uses this)">
            <input type="text" name="sp" placeholder="Password (Optional)">
        </div>
        <select name="p"><option value="view">View Only</option><option value="edit">Can Edit</option></select>
        <button class="btn btn-primary">Confirm Share</button> <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div></body></html>
