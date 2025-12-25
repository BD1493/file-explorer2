<?php require_once '../../src/json.php'; session_start();
if(!isset($_SESSION['user'])) header('Location: ../auth/login.php');
$u = $_SESSION['user']; $path = $_GET['path'] ?? $u;
$full = STORAGE_PATH . "/users/" . $path;

if($_FILES) {
    foreach($_FILES['f']['name'] as $k => $name) move_uploaded_file($_FILES['f']['tmp_name'][$k], $full."/".$name);
}
if(isset($_POST['n'])) mkdir($full."/".$_POST['n'], 0777, true);
$items = array_diff(scandir($full), ['.', '..']);
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav">
    <strong>Mega Drive</strong>
    <div>
        <a href="public.php" class="btn">Gallery</a>
        <a href="shared_access.php" class="btn">Find Shared</a>
        <a href="../auth/logout.php" class="btn">Logout</a>
    </div>
</div>
<div class="container">
    <div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
        <form method="POST" enctype="multipart/form-data"><input type="file" name="f[]" multiple webkitdirectory onchange="this.form.submit()" id="up" style="display:none"><button type="button" onclick="document.getElementById('up').click()" class="btn btn-primary">Upload</button></form>
        <form method="POST" style="display:flex; gap:5px;"><input type="text" name="n" placeholder="Folder Name" style="margin:0; width:150px;"><button class="btn">New Folder</button></form>
    </div>
    <div class="grid">
        <?php foreach($items as $i): $isDir = is_dir($full."/".$i); $ext = pathinfo($i, PATHINFO_EXTENSION); ?>
        <div class="file-card" onclick="location.href='<?php echo $isDir ? "?path=$path/$i" : ($ext=='chat'?'chat.php?id='.$i:'editor.php?file='.$i.'&path='.$path); ?>'">
            <span class="file-icon"><?php echo $isDir ? 'í³' : ($ext=='chat'?'í²¬':'í³„'); ?></span>
            <div style="font-size:12px; font-weight:bold;"><?php echo $i; ?></div>
            <div style="margin-top:10px;">
                <a href="share_logic.php?file=<?php echo $i; ?>&path=<?php echo $path; ?>" style="font-size:10px; color:var(--google-blue); text-decoration:none;">Share</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div></body></html>
