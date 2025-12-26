<?php
require_once '../../src/json.php'; session_start();
if(!isset($_SESSION['user'])) { header('Location: ../auth/login.php'); exit; }

$user = $_SESSION['user'];
$path = $_GET['path'] ?? $user;
// Security: lock to user directory
if(strpos($path, '..') !== false || strpos($path, $user) !== 0) $path = $user;

$fullPath = STORAGE_PATH . "/users/" . $path;
if(!is_dir($fullPath)) mkdir($fullPath, 0777, true);

// Handle Uploads
if(isset($_FILES['up_file'])) {
    foreach($_FILES['up_file']['name'] as $i => $name) move_uploaded_file($_FILES['up_file']['tmp_name'][$i], $fullPath."/".$name);
}
// Handle Creation
if(isset($_POST['new_folder'])) mkdir($fullPath."/".$_POST['new_folder'], 0777, true);
if(isset($_POST['new_file'])) file_put_contents($fullPath."/".$_POST['new_file'], "");

$items = array_diff(scandir($fullPath), ['.', '..']);
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Drive</title></head><body>
<div class="nav">
    <div class="logo"><span>☁️</span> Mega Drive</div>
    <div style="display:flex; gap:10px;">
        <a href="public.php" class="btn">Public</a>
        <a href="shared_find.php" class="btn">Find Shared</a>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>
<div class="container">
    <div class="card toolbar">
        <form method="POST" enctype="multipart/form-data" style="margin:0;">
            <input type="file" name="up_file[]" multiple id="up" style="display:none" onchange="this.form.submit()">
            <button type="button" onclick="document.getElementById('up').click()" class="btn btn-primary">⬆ Upload</button>
        </form>
        <form method="POST" style="margin:0; display:flex;">
            <input type="text" name="new_folder" placeholder="Folder Name" style="margin:0; width:120px; border-right:0; border-radius:4px 0 0 4px;" required>
            <button class="btn" style="border-radius:0 4px 4px 0;">+ Folder</button>
        </form>
        <form method="POST" style="margin:0; display:flex;">
            <input type="text" name="new_file" placeholder="File.txt" style="margin:0; width:120px; border-right:0; border-radius:4px 0 0 4px;" required>
            <button class="btn" style="border-radius:0 4px 4px 0;">+ File</button>
        </form>
    </div>
    
    <div style="margin-bottom:15px; color:#5f6368;">Current: <b>/<?php echo $path; ?></b> <?php if($path !== $user): ?><a href="?path=<?php echo dirname($path); ?>" style="color:var(--blue); margin-left:10px;">⬆ Up</a><?php endif; ?></div>

    <div class="grid">
        <?php foreach($items as $i): 
            $isDir = is_dir($fullPath."/".$i);
            $ext = pathinfo($i, PATHINFO_EXTENSION);
            if($isDir) $link = "?path=$path/$i";
            elseif($ext == 'chat') $link = "chat.php?id=$i";
            else $link = "editor.php?file=$i&owner=$user&path=$path";
        ?>
        <div class="file-card" onclick="location.href='<?php echo $link; ?>'">
            <span class="icon"><?php echo $isDir ? '' : ($ext=='chat' ? '' : ''); ?></span>
            <div class="filename"><?php echo $i; ?></div>
            <?php if(!$isDir): ?>
                <div class="meta" onclick="event.stopPropagation()">
                    <a href="share_setup.php?file=<?php echo urlencode($i); ?>&path=<?php echo urlencode($path); ?>" style="color:var(--blue)">Share / Public</a>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div></body></html>
