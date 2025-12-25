<?php
session_start();
if(isset($_SESSION['consent'])) { header('Location: /public/auth/login.php'); exit; }
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="/public/assets/css/style.css"></head><body>
<div class="modal-overlay">
    <div class="modal">
        <h2>⚠️ Security Check</h2>
        <p>This system records IP: <b><?php echo $_SERVER['REMOTE_ADDR']; ?></b>.</p>
        <p>By proceeding, you acknowledge file storage is for dev purposes.</p>
        <button onclick="location.href='https://google.com'" class="btn">Exit</button>
        <button onclick="location.href='accept.php'" class="btn btn-primary">Accept & Enter</button>
    </div>
</div></body></html>
