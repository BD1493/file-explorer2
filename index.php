<?php
session_start();
$usersFile = 'data/users.json';
$users = json_decode(file_get_contents($usersFile), true);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($action === 'signup'){
        if(isset($users[$username])){
            $error = "User already exists!";
        } else {
            $users[$username] = ['password'=>$password, 'files'=>[]];
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            mkdir("storage/users/$username", 0777, true);
            $_SESSION['user'] = $username;
            header("Location: dashboard.php");
            exit;
        }
    } elseif($action === 'login'){
        if(isset($users[$username]) && $users[$username]['password']===$password){
            $_SESSION['user']=$username;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>MyDrive</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<h1>MyDrive</h1>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<div>
<h2>Login</h2>
<form method="POST">
<input type="hidden" name="action" value="login">
<input name="username" placeholder="Username" required>
<input name="password" type="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
</div>
<div>
<h2>Sign Up</h2>
<form method="POST">
<input type="hidden" name="action" value="signup">
<input name="username" placeholder="Username" required>
<input name="password" type="password" placeholder="Password" required>
<button type="submit">Sign Up</button>
</form>
</div>
</body>
</html>
