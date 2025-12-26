<?php
require_once '../../src/json.php'; session_start();
$user = $_SESSION['user']; $file = $_GET['file']; $path = $_GET['path'];
$shares = getJSON('shares');

if($_POST) {
    if(isset($_POST['delete'])) {
        foreach($shares as $k => $s) {
            if($s['alias'] === $_POST['del_alias'] && $s['owner'] === $user) unset($shares[$k]);
        }
    } else {
        $shares[] = [
            'owner' => $user, 'path' => $path, 'file' => $file,
            'alias' => $_POST['alias'], 'pass' => $_POST['pass'],
            'perm' => $_POST['perm'], 'is_public' => isset($_POST['is_public'])
        ];
    }
    saveJSON('shares', array_values($shares));
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:500px;">
    <div class="card">
        <h3>Share Settings: <?php echo $file; ?></h3>
        <form method="POST">
            <div style="background:#e8f0fe; padding:10px; border-radius:4px; margin-bottom:10px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="is_public"> <b>List in Public Gallery</b>
                </label>
            </div>
            <label>Share Name (Alias)</label>
            <input type="text" name="alias" placeholder="e.g. ProjectX" required>
            <label>Password (Optional)</label>
            <input type="text" name="pass" placeholder="Leave empty for none">
            <label>Permissions</label>
            <select name="perm"><option value="view">View Only</option><option value="edit">Allow Editing</option></select>
            <button class="btn btn-primary" style="width:100%">Create Link</button>
        </form>
        <hr>
        <h4>Active Links</h4>
        <?php foreach($shares as $s): if($s['file'] === $file && $s['owner'] === $user): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding:10px 0;">
                <div>
                    <b><?php echo $s['alias']; ?></b> 
                    <?php if(isset($s['is_public']) && $s['is_public']) echo "<span style='background:green; color:white; padding:2px 5px; font-size:10px; border-radius:4px;'>PUBLIC</span>"; ?>
                </div>
                <form method="POST" style="margin:0"><input type="hidden" name="delete" value="1"><input type="hidden" name="del_alias" value="<?php echo $s['alias']; ?>"><button class="btn btn-danger" style="font-size:12px; padding:4px 8px;">Unshare</button></form>
            </div>
        <?php endif; endforeach; ?>
        <br><a href="dashboard.php" class="btn">Back</a>
    </div>
</div></body></html>
