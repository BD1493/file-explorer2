<?php require_once '../../src/auth.php'; requireLogin();
if($_POST){
    file_put_contents(STORAGE_PATH."/users/".currentUser()."/{$_POST['n']}.txt", "");
    header("Location: dashboard.php"); exit;
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px;">
    <h2>New File</h2>
    <form method="POST"><input type="text" name="n" placeholder="Filename" required><button class="btn btn-primary">Create</button></form>
</div></body></html>
