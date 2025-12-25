<?php
session_start();
require_once __DIR__ . '/json.php';
function isLoggedIn() { return isset($_SESSION['user']); }
function requireLogin() { if (!isLoggedIn()) { header('Location: /public/auth/login.php'); exit; } }
function currentUser() { return $_SESSION['user'] ?? null; }
?>
