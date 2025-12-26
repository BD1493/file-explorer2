<?php
require_once '../../src/json.php'; session_start();
$user = $_SESSION['user']; $file = $_GET['file']; $path = $_GET['path'];
$shares = getJSON('shares');

if($_POST) {
    if(isset($_POST['del'])) {
        foreach($shares as $k=>$s) if($s['alias']===$_POST['del']) unset($shares[$k]);
    } else {
        $shares[] = [
            'owner'=>$user, 'path'=>$path, 'file'=>$file, 
            'alias'=>$_POST['alias'], 'pass'=>$_POST['pass'], 
            'perm'=>$_POST['perm'], 'is_public'=>isset($_POST['is_public'])
        ];
    }
    saveJSON('shares', array_values($shares));
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:500px;">
    <div class="card">
        <h3>Share Settings for "<?php echo $file; ?>"</h3>
        <form method="POST">
            <label><b>Public Gallery?</b> <input type="checkbox" name="is_public"></label>
            <input type="text" name="alias" placeholder="Unique Share Name (ID)" required>
            <input type="text" name="pass" placeholder="Password (Optional)">
            <select name="perm"><option value="view">View Only</option><option value="edit">Allow Editing</option></select>
            <button class="btn btn-primary" style="width:100%">Create Share</button>
        </form>
        <hr>
        <h4>Active Shares</h4>
        <?php foreach($shares as $s): if($s['file']===$file && $s['owner']===$user): ?>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <span>ID: <b><?php echo $s['alias']; ?></b> <?php if($s['is_public']) echo "(Public)"; ?></span>
                <form method="POST" style="margin:0"><input type="hidden" name="del" value="<?php echo $s['alias']; ?>"><button class="btn btn-danger">Delete</button></form>
            </div>
        <?php endif; endforeach; ?>
        <br><a href="dashboard.php" class="btn">Back</a>
    </div>
</div></body></html>
