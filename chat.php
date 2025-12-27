<?php
session_start();
if(!isset($_SESSION['user'])) header("Location: index.php");
$user = $_SESSION['user'];
$file = $_GET['file'] ?? null;

if($file){
    $path = "storage/users/$user/$file";
    if($_SERVER['REQUEST_METHOD']==='POST'){
        file_put_contents($path, $_POST['content']);
    }
    $content = file_exists($path) ? file_get_contents($path) : '';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Chat - <?php echo $file; ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<h1>Chat File: <?php echo $file; ?></h1>
<form method="POST">
<textarea name="content" rows="20" cols="50"><?php echo $content; ?></textarea><br>
<button type="submit">Save</button>
</form>
<a href="dashboard.php">Back</a>
</body>
</html>
