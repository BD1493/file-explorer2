<?php
require_once '../../src/auth.php';
require_once '../../src/permissions.php';
requireLogin();

$currentUser = currentUser();
$file = $_GET['file'] ?? '';
$owner = $_GET['owner'] ?? $currentUser; // Defaults to current user if not specified

$filePath = STORAGE_PATH . "/users/$owner/$file";

// Security Check
if (!file_exists($filePath)) {
    die("File not found at $filePath");
}

// Check permissions
if ($owner !== $currentUser) {
    if (!canEdit($currentUser, $file, $owner)) {
        // If they can't edit, maybe they can view? 
        // For now, let's just make it Read Only if they don't have edit rights
        $isReadOnly = true; 
    } else {
        $isReadOnly = false;
    }
} else {
    $isReadOnly = false;
}

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isReadOnly) {
    $newContent = $_POST['content'];
    file_put_contents($filePath, $newContent);
    
    // Handle Rename (Only if owner)
    if ($owner === $currentUser && !empty($_POST['new_name']) && $_POST['new_name'] !== $file) {
        $newName = basename($_POST['new_name']);
        rename($filePath, STORAGE_PATH . "/users/$owner/$newName");
        header("Location: dashboard.php");
        exit;
    }
    
    $success = "Saved!";
}

$content = file_get_contents($filePath);
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Editing: <?php echo htmlspecialchars($file); ?></h2>
    <?php if(isset($success)) echo "<div class='alert' style='background:#d4edda;color:#155724'>$success</div>"; ?>
    
    <form method="POST">
        <?php if($owner === $currentUser): ?>
            <label>Rename File:</label>
            <input type="text" name="new_name" value="<?php echo htmlspecialchars($file); ?>">
        <?php endif; ?>

        <textarea name="content" rows="15" <?php if($isReadOnly) echo 'readonly'; ?>><?php echo htmlspecialchars($content); ?></textarea>
        
        <?php if(!$isReadOnly): ?>
            <button type="submit" class="btn">Save Changes</button>
        <?php else: ?>
            <p><i>Read Only Mode</i></p>
        <?php endif; ?>
        
        <a href="<?php echo ($owner === $currentUser) ? 'dashboard.php' : 'shared.php'; ?>" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
