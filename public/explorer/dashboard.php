<?php
require_once '../../src/json.php'; session_start();
if(!isset($_SESSION['user'])) { header('Location: ../auth/login.php'); exit; }

$user = $_SESSION['user'];
$path = $_GET['path'] ?? $user;
if(strpos($path, '..') !== false || strpos($path, $user) !== 0) $path = $user;
$fullPath = STORAGE_PATH . "/users/" . $path;
if(!is_dir($fullPath)) mkdir($fullPath, 0777, true);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_FILES['up'])){
        foreach($_FILES['up']['name'] as $i => $n) move_uploaded_file($_FILES['up']['tmp_name'][$i], $fullPath."/".$n);
    }
    if(!empty($_POST['nf'])) mkdir($fullPath."/".$_POST['nf'], 0777, true);
    if(!empty($_POST['nfile'])) file_put_contents($fullPath."/".$_POST['nfile'], "");
    header("Location: dashboard.php?path=" . urlencode($path));
    exit;
}
$items = array_diff(scandir($fullPath), ['.', '..']);
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"><title>Drive</title></head><body>
<div class="nav">
    <div style="font-weight:bold;">‚òÅÔ∏è Mega Drive: /<?=$path?></div>
    <div>
        <a href="public.php" class="btn">Ìºé Public</a>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>
<div class="container">
    <div class="card" style="display:flex; gap:10px;">
        <form method="POST" enctype="multipart/form-data"><input type="file" name="up[]" multiple onchange="this.form.submit()" id="u" style="display:none;"><button type="button" onclick="document.getElementById('u').click()" class="btn btn-primary">Upload</button></form>
        <form method="POST" style="display:flex; gap:5px;"><input name="nf" placeholder="Folder Name" style="width:120px;"><button class="btn">New Folder</button></form>
        <form method="POST" style="display:flex; gap:5px;"><input name="nfile" placeholder="File.txt" style="width:120px;"><button class="btn">New File</button></form>
    </div>
    <div class="grid">
        <?php foreach($items as $i): 
            $isDir = is_dir($fullPath."/".$i);
            $ext = pathinfo($i, PATHINFO_EXTENSION);
            $url = $isDir ? "?path=$path/$i" : ($ext=="chat" ? "chat.php?id=$i" : "editor.php?file=$i&owner=$user&path=$path");
        ?>
        <div class="file-card" onclick="location.href='<?=$url?>'">
            <div class="icon"><?=$isDir?'Ì≥Å':($ext=='chat'?'Ì≤¨':'Ì≥Ñ')?></div>
            <div style="font-weight:500; font-size:13px;"><?=$i?></div>
            <?php if(!$isDir): ?>
                <a href="share.php?file=<?=urlencode($i)?>&path=<?=urlencode($path)?>" style="margin-top:10px; color:var(--blue); font-size:11px;" onclick="event.stopPropagation()">Share Settings</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div></body></html>
