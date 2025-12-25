<?php session_start(); ?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="/public/assets/css/style.css"></head><body>
<?php if(!isset($_SESSION['consent'])): ?>
<div class="modal-overlay">
    <div class="modal">
        <h2>Security Notice</h2>
        <p>Your IP: <b><?php echo $_SERVER['REMOTE_ADDR']; ?></b> will be logged. Files are stored in a development environment. Proceed?</p>
        <button onclick="location.href='https://google.com'" class="btn">Exit</button>
        <button onclick="location.href='accept.php'" class="btn btn-primary">Accept</button>
    </div>
</div>
<?php else: header('Location: /public/auth/login.php'); exit; endif; ?>
</body></html>
