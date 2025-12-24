<?php
session_start();
function requireLogin(): void { if (!isset($_SESSION['user'])) header("Location: /auth/login.php"); }
function currentUser(): ?string { return $_SESSION['user'] ?? null; }
