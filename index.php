<?php
session_start();
if(isset($_SESSION['user'])) { header('Location: /public/explorer/dashboard.php'); exit; }
header('Location: /public/auth/login.php');
?>
