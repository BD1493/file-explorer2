<?php
require_once '../../src/json.php'; session_start();
if(!isset($_SESSION['user'])) { header('Location: ../auth/login.php'); exit; }

$user = $_SESSION['user'];
$path = $_GET['path'] ?? $user;
// Security: Prevent going up directories
if(strpos($path, '..') !== false) $path = $user;
$fullPath = STORAGE_PATH . "/users/" . $path;

// 1. Handle File Upload
if(isset($_FILES['files'])) {
    foreach($_FILES['files']['name'] as $i => $name){
        move_uploaded_file($_FILES['files']['tmp_name'][$i], $fullPath . "/" . $name);
    }
}
// 2. Handle New Folder
if(isset($_POST['new_folder'])) {
    mkdir($fullPath . "/" . $_POST['new_folder'], 0777, true);
}
// 3. Handle NEW FILE (Fixing your issue)
if(isset($_POST['new_file'])) {
    file_put_contents($fullPath . "/" . $_POST['new_file'], ""); // Create empty file
}

$items = is_dir($fullPath) ? array_diff(scandir($fullPath), ['.', '..']) : [];
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav">
    <strong>Mega Drive</strong>
    <div>
        <a href="shared.php" class="btn">Find Shared</a>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>
<div class="container">
    <div class="card" style="display:flex; flex-wrap:wrap; gap:10px;">
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="files[]" multiple id="upl" style="display:none" onchange="this.form.submit()">
            <button type="button" onclick="document.getElementById('upl').click()" class="btn btn-primary">â¬† Upload</button>
        </form>
        <form method="POST" style="display:flex;">
            <input type="text" name="new_folder" placeholder="Folder Name" style="margin:0; width:120px; border-right:0; border-radius:4px 0 0 4px;" required>
            <button class="btn" style="border-radius:0 4px 4px 0;">+ Folder</button>
        </form>
        <form method="POST" style="display:flex;">
            <input type="text" name="new_file" placeholder="File.txt / .html" style="margin:0; width:120px; border-right:0; border-radius:4px 0 0 4px;" required>
            <button class="btn" style="border-radius:0 4px 4px 0;">+ File</button>
        </form>
    </div>

    <div class="grid">
        <?php foreach($items as $i): 
            $isDir = is_dir($fullPath."/".$i);
            $ext = pathinfo($i, PATHINFO_EXTENSION);
            $link = $isDir ? "?path=$path/$i" : ($ext=='chat' ? "chat.php?id=$i" : "editor.php?file=$i&owner=$user&path=$path"); 
        ?>
        <div class="file-item" onclick="location.href='<?php echo $link; ?>'">
            <span class="icon"><?php echo $isDir ? 'í³' : ($ext=='chat'?'í²¬':'í³„'); ?></span>
            <div><b><?php echo $i; ?></b></div>
            <?php if(!$isDir): ?>
                <div style="margin-top:5px; font-size:11px; color:#1a73e8;">
                    <a href="share_manage.php?file=<?php echo urlencode($i); ?>&path=<?php echo urlencode($path); ?>" onclick="event.stopPropagation()">Share</a>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div></body></html>
