<?php
require_once '../../src/json.php'; session_start();
$error = "";
if($_POST) {
    $shares = getJSON('shares');
    $found = false;
    foreach($shares as $s) {
        if($s['alias'] === $_POST['alias'] && $s['pass'] === $_POST['pass']) {
            // Success: Unlock access in session
            if(!isset($_SESSION['access_list'])) $_SESSION['access_list'] = [];
            $_SESSION['access_list'][$s['alias']] = true;
            
            // Redirect to editor
            header("Location: editor.php?file=" . urlencode($s['file']) . "&owner=" . urlencode($s['owner']) . "&path=" . urlencode($s['path']));
            exit;
        }
    }
    $error = "Invalid Alias or Password";
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:50px;">
    <div class="card">
        <h3>Access Shared File</h3>
        <?php if($error) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="alias" placeholder="Share Alias" required>
            <input type="password" name="pass" placeholder="Password" required>
            <button class="btn btn-primary" style="width:100%">Unlock & Open</button>
        </form>
        <br><a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</div></body></html>
