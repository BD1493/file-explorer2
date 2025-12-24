<?php
require_once '../../src/auth.php';
requireLogin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputName = $_POST['share_name'];
    $inputPass = $_POST['share_pass'];
    
    $shares = getShares();
    $found = false;
    
    foreach ($shares as $s) {
        // Match Share Name
        if ($s['share_name'] === $inputName) {
            // Check Password (if not public)
            if ($s['is_public'] || $s['password'] === $inputPass) {
                // Success! Unlock it for this session
                if (!isset($_SESSION['unlocked_shares'])) {
                    $_SESSION['unlocked_shares'] = [];
                }
                // Avoid duplicates
                $_SESSION['unlocked_shares'] = array_filter($_SESSION['unlocked_shares'], function($item) use ($s) {
                    return $item['share_name'] !== $s['share_name'];
                });
                $_SESSION['unlocked_shares'][] = $s;
                $found = true;
            }
        }
    }
    
    if (!$found) {
        $error = "Invalid Share Name or Password.";
        $showRequest = true;
    }
}

$unlocked = $_SESSION['unlocked_shares'] ?? [];
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Access Shared Files</h2>
    <a href="dashboard.php" class="btn btn-secondary">Back</a>
    <hr>
    
    <div style="background:#f9f9f9; padding:15px; border-radius:5px; margin-bottom:20px;">
        <h3>Find a File</h3>
        <?php if($error) echo "<div class='alert'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="share_name" placeholder="Enter Share Name (e.g. myproject)" required>
            <input type="text" name="share_pass" placeholder="Enter Password (if private)">
            <button class="btn">Get File</button>
        </form>
        
        <?php if(isset($showRequest)): ?>
            <br>
            <p>Access Denied. <a href="request.php" class="btn" style="background:orange;">Request Access</a></p>
        <?php endif; ?>
    </div>

    <h3>Unlocked Files</h3>
    <ul class="file-list">
        <?php foreach ($unlocked as $share): ?>
            <li class="file-item">
                <span><b><?php echo htmlspecialchars($share['filename']); ?></b> (<?php echo $share['permission']; ?>)</span>
                <a href="edit.php?file=<?php echo urlencode($share['filename']); ?>&owner=<?php echo urlencode($share['owner']); ?>" class="btn">Open</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
