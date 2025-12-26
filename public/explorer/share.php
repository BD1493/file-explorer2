<?php
require_once '../../src/json.php'; session_start();
$file = $_GET['file']; $path = $_GET['path']; $shares = getJSON('shares');
if($_POST){
    $shares[] = ['owner'=>$_SESSION['user'], 'path'=>$path, 'file'=>$file, 'alias'=>$_POST['a'], 'is_public'=>isset($_POST['p'])];
    saveJSON('shares', $shares);
    header("Location: dashboard.php"); exit;
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px;"><div class="card">
<h3>Share: <?=$file?></h3>
<form method="POST">
    <input name="a" placeholder="Share Alias Name" required>
    <label><input type="checkbox" name="p"> List in Public Gallery</label><br><br>
    <button class="btn btn-primary" style="width:100%;">Create Share</button>
</form>
</div></div></body></html>
