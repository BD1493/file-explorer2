<?php
require_once '../../src/auth.php';
require_once '../../src/permissions.php';
requireLogin();

$user = currentUser();
$file = $_GET['file'];
$owner = $_GET['owner'] ?? $user;
$filePath = STORAGE_PATH . "/users/$owner/$file";

// Permissions Check
$access = canAccess($user, $file, $owner);

if (!$access) {
    die("<h1>Access Denied</h1><a href='dashboard.php'>Back</a>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $access === 'edit') {
    file_put_contents($filePath, $_POST['content']);
    $success = "Saved!";
}

$content = file_exists($filePath) ? file_get_contents($filePath) : "";
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <h2>Edit: <?php echo htmlspecialchars($file); ?></h2>
    <?php if(isset($success)) echo "<div class='alert' style='background:#d4edda;color:#155724'>$success</div>"; ?>
    <form method="POST">
        <textarea name="content" rows="15" <?php if($access!=='edit') echo 'readonly'; ?>><?php echo htmlspecialchars($content); ?></textarea>
        <?php if($access==='edit'): ?>
            <button class="btn">Save</button>
        <?php else: ?>
            <p><i>View Only Mode</i></p>
        <?php endif; ?>
        <a href="<?php echo ($owner===$user)?'dashboard.php':'shared.php'; ?>" class="btn btn-secondary">Back</a>
    </form>
</div></body></html>
