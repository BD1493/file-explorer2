<?php
require_once '../../src/auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reqs = getJSON('requests.json');
    $reqs[] = ['from'=>currentUser(),'note'=>$_POST['note']];
    saveJSON('requests.json', $reqs);
    $success = "Request Sent.";
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <h2>Request Access</h2>
    <?php if(isset($success)) echo "<div class='alert'>$success</div>"; ?>
    <form method="POST">
        <textarea name="note" placeholder="Why do you want access?" required></textarea>
        <button class="btn">Send</button>
        <a href="shared.php" class="btn btn-secondary">Back</a>
    </form>
</div></body></html>
