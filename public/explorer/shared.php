<?php
require_once '../../src/auth.php';
requireLogin();
$me = currentUser();
$shares = getShares();

// Logic: Users see files shared directly with them OR public files they have credentials for
$myShares = [];

// 1. Direct Shares
foreach ($shares as $s) {
    if ($s['shared_with'] === $me) {
        $myShares[] = $s;
    }
}

// 2. Handle "Get Shared File" prompt input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetUser = $_POST['target_user'];
    $targetPass = $_POST['target_pass'];
    
    // Find share matching this
    $found = false;
    foreach ($shares as $s) {
        if ($s['owner'] === $targetUser && $s['password'] === $targetPass) {
            // It matches a share. Is it public or for me?
            // For this simple version, if they know the password and owner, we let them see it
            // We temporarily add it to a session list of "unlocked files"
            $_SESSION['unlocked_shares'][] = $s;
            $found = true;
        }
    }
    
    if (!$found) {
        $error = "Incorrect username or password.";
        $showRequestBtn = true; // Show request access button
    }
}

// Merge unlocked shares
if (isset($_SESSION['unlocked_shares'])) {
    $myShares = array_merge($myShares, $_SESSION['unlocked_shares']);
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Shared With Me</h2>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    <hr>
    
    <div style="background:#eee; padding:15px; border-radius:5px; margin-bottom:20px;">
        <h3>Get Shared File</h3>
        <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="target_user" placeholder="Owner Username" required>
            <input type="text" name="target_pass" placeholder="File Password" required>
            <button type="submit" class="btn">Unlock File</button>
        </form>
        
        <?php if(isset($showRequestBtn)): ?>
            <br>
            <form action="request.php" method="GET">
                <input type="hidden" name="owner" value="<?php echo htmlspecialchars($_POST['target_user']); ?>">
                <button type="submit" class="btn" style="background:orange;">Request Access</button>
            </form>
        <?php endif; ?>
    </div>

    <h3>Files Available</h3>
    <ul class="file-list">
        <?php foreach ($myShares as $share): ?>
            <li class="file-item">
                <span>
                    <b><?php echo htmlspecialchars($share['filename']); ?></b> 
                    (from <?php echo htmlspecialchars($share['owner']); ?>)
                    - <i><?php echo $share['permission']; ?></i>
                </span>
                <a href="edit.php?file=<?php echo urlencode($share['filename']); ?>&owner=<?php echo urlencode($share['owner']); ?>" class="btn">Open</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
