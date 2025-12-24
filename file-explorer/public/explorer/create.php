<?php
require_once '../../src/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = $_POST['filename'];
    $content = $_POST['content']; // Optional initial content
    $user = currentUser();
    
    // Validate filename (basic)
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
    if (pathinfo($filename, PATHINFO_EXTENSION) == '') {
        $filename .= '.txt'; // Default to txt
    }

    $path = STORAGE_PATH . "/users/$user/$filename";
    
    file_put_contents($path, $content);
    header("Location: edit.php?file=$filename");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Create New File</h2>
    <form method="POST">
        <label>Filename:</label>
        <input type="text" name="filename" placeholder="example.txt" required>
        <label>Content (Optional):</label>
        <textarea name="content" rows="10"></textarea>
        <button type="submit" class="btn">Create</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
