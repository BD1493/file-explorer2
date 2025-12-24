<?php
require_once 'json.php';
session_start();

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function requireLogin() {
    if(!currentUser()) {
        header('Location: /auth/login.php');
        exit;
    }
}

function login($username, $password) {
    $users = loadJson('users.json');
    foreach($users as $u) {
        if($u['username']===$username && $u['password']===$password) { // simple hash can be added
            $_SESSION['user'] = $username;
            return true;
        }
    }
    return false;
}

function logout() {
    session_destroy();
}
