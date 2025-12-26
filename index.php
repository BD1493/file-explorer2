<?php
session_start();
// If already consented, go to login
if(isset($_SESSION['consent'])) { header('Location: /public/auth/login.php'); exit; }
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="/public/assets/css/style.css"></head><body>
<div class="modal-overlay">
    <div class="modal">
        <h2>í´’ Security Gateway</h2>
        <p>This system utilizes ephemeral storage for development.</p>
        <p>Your IP <b><?php echo $_SERVER['REMOTE_ADDR']; ?></b> is being logged.</p>
        <div style="margin-top:20px;">
            <button onclick="location.href='https://google.com'" class="btn">Decline</button>
            <button onclick="location.href='accept.php'" class="btn btn-primary">Accept & Continue</button>
        </div>
    </div>
</div></body></html>
