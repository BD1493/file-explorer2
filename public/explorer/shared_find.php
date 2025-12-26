<?php
require_once '../../src/json.php'; session_start();
if($_POST) {
    $shares = getJSON('shares');
    foreach($shares as $s) {
        if($s['alias'] === $_POST['alias'] && $s['pass'] === $_POST['pass']) {
            $_SESSION['unlocked'][$s['alias']] = true;
            header("Location: editor.php?file=" . urlencode($s['file']) . "&owner=" . urlencode($s['owner']) . "&path=" . urlencode($s['path']));
            exit;
        }
    }
    $err = "Invalid Alias or Password";
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:50px;">
    <div class="card">
        <h3>Access Shared File</h3>
        <?php if(isset($err)) echo "<p style='color:red'>$err</p>"; ?>
        <form method="POST">
            <input type="text" name="alias" placeholder="Share Name (Alias)" required>
            <input type="password" name="pass" placeholder="Password (if any)">
            <button class="btn btn-primary" style="width:100%">Unlock</button>
        </form>
        <br><a href="dashboard.php" class="btn">Back</a>
    </div>
</div></body></html>
