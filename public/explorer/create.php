<?php
require_once '../../src/auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $_POST['filename']);
    if (pathinfo($filename, PATHINFO_EXTENSION) == '') $filename .= '.txt';
    file_put_contents(STORAGE_PATH . "/users/" . currentUser() . "/$filename", $_POST['content']);
    header("Location: dashboard.php"); exit;
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container"><h2>Make File</h2>
    <form method="POST">
        <input type="text" name="filename" placeholder="File Name" required>
        <textarea name="content" rows="10" placeholder="Content..."></textarea>
        <button class="btn">Save</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div></body></html>
