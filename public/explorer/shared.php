<?php
require_once '../../src/json.php'; session_start();
if($_POST) {
    $shares = getJSON('shares');
    foreach($shares as $s) {
        if($s['alias'] === $_POST['alias'] && $s['pass'] === $_POST['pass']) {
            $_SESSION['unlocked_shares'][] = $s['alias'];
            header("Location: editor.php?file=".$s['file']."&owner=".$s['owner']."&path=".$s['path']); exit;
        }
    }
    $err = "Invalid credentials";
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:50px;">
    <div class="card">
        <h3>Unlock Private File</h3>
        <?php if(isset($err)) echo "<p style='color:red'>$err</p>"; ?>
        <form method="POST">
            <input type="text" name="alias" placeholder="Share ID / Alias" required>
            <input type="password" name="pass" placeholder="Password">
            <button class="btn btn-primary" style="width:100%">Open</button>
        </form>
        <br><a href="dashboard.php" class="btn">Back</a>
    </div>
</div></body></html>
