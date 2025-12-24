<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: /explorer/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>File Explorer</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <h1>📁 File Explorer</h1>
  <p>Secure file creation, sharing, and collaboration.</p>

  <a href="/auth/signup.php" class="btn">Enter File Explorer</a>
</body>
</html>
