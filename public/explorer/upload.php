<?php
require_once '../../src/auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], STORAGE_PATH . "/users/" . currentUser() . "/" . basename($_FILES['file']['name']));
    header('Location: dashboard.php'); exit;
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container"><h2>Upload File</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required><br><br>
        <button class="btn">Upload</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div></body></html>
