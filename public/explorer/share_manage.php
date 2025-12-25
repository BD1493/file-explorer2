<?php
require_once '../../src/json.php'; session_start();
$user = $_SESSION['user'];
$file = $_GET['file'];
$path = $_GET['path']; // Folder context
$shares = getJSON('shares');

// 1. Create Share
if(isset($_POST['create_share'])) {
    $newShare = [
        'id' => uniqid(),
        'owner' => $user,
        'path' => $path,
        'file' => $file,
        'alias' => $_POST['alias'],
        'pass' => $_POST['pass'],
        'perm' => $_POST['perm']
    ];
    $shares[] = $newShare;
    saveJSON('shares', $shares);
}
// 2. Unshare
if(isset($_POST['delete_share'])) {
    foreach($shares as $k => $s) {
        if($s['alias'] === $_POST['del_alias'] && $s['owner'] === $user) {
            unset($shares[$k]);
        }
    }
    saveJSON('shares', array_values($shares));
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:500px;">
    <div class="card">
        <h3>Share "<?php echo $file; ?>"</h3>
        <form method="POST">
            <input type="hidden" name="create_share" value="1">
            <label>Share Alias (Unique Name)</label>
            <input type="text" name="alias" required placeholder="e.g. SecretDoc1">
            <label>Password</label>
            <input type="text" name="pass" required placeholder="Secure Password">
            <label>Permission</label>
            <select name="perm"><option value="view">View Only</option><option value="edit">Edit</option></select>
            <button class="btn btn-primary" style="width:100%">Create Share Link</button>
        </form>
        <hr>
        <h4>Active Shares</h4>
        <?php foreach($shares as $s): if($s['file'] === $file && $s['owner'] === $user): ?>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; border-bottom:1px solid #eee; padding-bottom:5px;">
                <span><b><?php echo $s['alias']; ?></b> (Pass: <?php echo $s['pass']; ?>)</span>
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="delete_share" value="1">
                    <input type="hidden" name="del_alias" value="<?php echo $s['alias']; ?>">
                    <button class="btn btn-danger" style="padding:2px 8px; font-size:12px;">Unshare</button>
                </form>
            </div>
        <?php endif; endforeach; ?>
        <br><a href="dashboard.php" class="btn">Back</a>
    </div>
</div></body></html>
