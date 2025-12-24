<?php
require_once '../../src/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $user = currentUser();
    $targetDir = STORAGE_PATH . "/users/$user/";
    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Upload failed.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Upload File</h2>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit" class="btn">Upload</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
