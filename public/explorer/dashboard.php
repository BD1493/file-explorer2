<?php
require_once '../../src/json.php'; session_start();
if(!isset($_SESSION['user'])) { header('Location: ../auth/login.php'); exit; }

$user = $_SESSION['user'];
$path = $_GET['path'] ?? $user;
if(strpos($path, '..') !== false) $path = $user; // Security fix
$fullPath = STORAGE_PATH . "/users/" . $path;

// Create user folder if missing (fix for ephemeral loss)
if(!is_dir($fullPath)) mkdir($fullPath, 0777, true);

// Uploads
if(isset($_FILES['files'])) {
    foreach($_FILES['files']['name'] as $i => $name){
        move_uploaded_file($_FILES['files']['tmp_name'][$i], $fullPath . "/" . $name);
    }
}
// New Folder
if(isset($_POST['new_folder'])) mkdir($fullPath . "/" . $_POST['new_folder'], 0777, true);
// New File
if(isset($_POST['new_file'])) file_put_contents($fullPath . "/" . $_POST['new_file'], "");

$items = array_diff(scandir($fullPath), ['.', '..']);
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav">
    <div class="logo"><span>Ì≥Å</span> Mega Drive</div>
    <div>
        <a href="public.php" class="btn">Ìºé Public</a>
        <a href="shared.php" class="btn">Ì¥ì Shared</a>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>
<div class="container">
    <div class="card" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="files[]" multiple id="up" style="display:none" onchange="this.form.submit()">
            <button type="button" onclick="document.getElementById('up').click()" class="btn btn-primary">‚¨Ü Upload</button>
        </form>
        <form method="POST" style="display:flex;"><input type="text" name="new_folder" placeholder="Folder Name" style="margin:0; width:120px;" required><button class="btn">‚ûï Folder</button></form>
        <form method="POST" style="display:flex;"><input type="text" name="new_file" placeholder="File.txt" style="margin:0; width:120px;" required><button class="btn">‚ûï File</button></form>
    </div>
    <div class="grid">
        <?php foreach($items as $i): 
            $isDir = is_dir($fullPath."/".$i);
            $ext = pathinfo($i, PATHINFO_EXTENSION);
            $link = $isDir ? "?path=$path/$i" : ($ext=='chat' ? "chat.php?id=$i" : "editor.php?file=$i&owner=$user&path=$path");
        ?>
        <div class="file-item" onclick="location.href='<?php echo $link; ?>'">
            <span class="icon"><?php echo $isDir ? 'Ì≥Å' : ($ext=='chat'?'Ì≤¨':'Ì≥Ñ'); ?></span>
            <div style="font-weight:500; font-size:14px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:100%;"><?php echo $i; ?></div>
            <?php if(!$isDir): ?>
                <a href="share_manage.php?file=<?php echo urlencode($i); ?>&path=<?php echo urlencode($path); ?>" style="font-size:11px; margin-top:5px; color:#1a73e8;" onclick="event.stopPropagation()">Share Settings</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div></body></html>
