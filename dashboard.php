<?php
session_start();
if(!isset($_SESSION['user'])) header("Location: index.php");
$user = $_SESSION['user'];

$usersFile = 'data/users.json';
$users = json_decode(file_get_contents($usersFile), true);

// Create new file
if(isset($_POST['create_file'])){
    $filename = $_POST['filename'];
    $type = $_POST['filetype']; // txt, chat, link
    $path = "storage/users/$user/$filename";
    file_put_contents($path, "");
    $users[$user]['files'][] = $filename;
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

$userFiles = $users[$user]['files'] ?? [];
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard - MyDrive</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<h1>Dashboard - <?php echo $user; ?></h1>
<a href="about.php">About</a> | <a href="help.php">Help</a> | <a href="index.php?logout=1">Logout</a>

<?php
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
}
?>

<h2>Create File</h2>
<form method="POST">
<input type="text" name="filename" placeholder="Filename" required>
<select name="filetype">
<option value="txt">Text</option>
<option value="chat">Chat</option>
<option value="link">Link</option>
</select>
<button type="submit" name="create_file">Create</button>
</form>

<h2>Your Files</h2>
<ul>
<?php
foreach($userFiles as $f){
    echo "<li><a href='storage/users/$user/$f' target='_blank'>$f</a> | <a href='chat.php?file=$f'>Open</a></li>";
}
?>
</ul>
</body>
</html>
