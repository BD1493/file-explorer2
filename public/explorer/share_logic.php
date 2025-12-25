<?php require_once '../../src/json.php'; session_start();
$file = $_GET['file']; $path = $_GET['path'];
$shares = getJSON('shares');

if($_POST) {
    if(isset($_POST['del'])) {
        foreach($shares as $k=>$s) if($s['alias']===$_POST['del']) unset($shares[$k]);
    } else {
        $shares[] = ['owner'=>$_SESSION['user'], 'file'=>$file, 'alias'=>$_POST['a'], 'pass'=>$_POST['p'], 'perm'=>$_POST['m'], 'public'=>isset($_POST['pub'])];
    }
    saveJSON('shares', array_values($shares)); $msg = "Updated!";
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:500px;">
    <div class="card">
        <h3>Share: <?php echo $file; ?></h3>
        <form method="POST">
            <input type="text" name="a" placeholder="Share Alias Name" required>
            <input type="text" name="p" placeholder="Password (Optional)">
            <select name="m"><option value="view">View Only</option><option value="edit">Can Edit</option></select>
            <label><input type="checkbox" name="pub"> Make Public Gallery</label>
            <button class="btn btn-primary" style="width:100%">Create Share Link</button>
        </form>
        <hr>
        <h4>Active Shares</h4>
        <?php foreach($shares as $s): if($s['file']==$file): ?>
            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                <span>Alias: <?php echo $s['alias']; ?></span>
                <form method="POST"><input type="hidden" name="del" value="<?php echo $s['alias']; ?>"><button class="btn" style="color:red">Unshare</button></form>
            </div>
        <?php endif; endforeach; ?>
        <br><a href="dashboard.php?path=<?php echo $path; ?>" class="btn">Back</a>
    </div>
</div></body></html>
