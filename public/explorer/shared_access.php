<?php require_once '../../src/json.php'; session_start();
if($_POST) {
    foreach(getJSON('shares') as $s) {
        if($s['alias']===$_POST['a'] && ($s['pass']==='' || $s['pass']===$_POST['p'])) {
            $_SESSION['unlocked'][$s['alias']] = true;
            header("Location: editor.php?file=".$s['file']."&path=".$s['owner']); exit;
        }
    }
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:10vh;">
    <div class="card">
        <h3>Unlock Private Share</h3>
        <form method="POST"><input type="text" name="a" placeholder="Share Alias"><input type="password" name="p" placeholder="Password"><button class="btn btn-primary" style="width:100%">Access</button></form>
    </div>
</div></body></html>
