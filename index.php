<?php
session_start();
// This MUST be the very first thing in the file. No spaces or lines above <?php

if (isset($_SESSION['consent'])) {
    header('Location: /public/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>
<div class="modal-overlay">
    <div class="modal">
        <h2>🔒 Access Agreement</h2>
        <p>This system records IP Address: <b><?php echo $_SERVER['REMOTE_ADDR']; ?></b></p>
        <p>Warning: Highly sensitive files may be insecure. Do you accept?</p>
        <div style="margin-top:20px;">
            <button onclick="location.href='https://google.com'" class="btn">Deny</button>
            <button onclick="location.href='accept.php'" class="btn btn-primary">Accept & Continue</button>
        </div>
    </div>
</div>
</body>
</html>