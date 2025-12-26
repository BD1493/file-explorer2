<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    # 1. Wipe and Recreate Directory Structure
rm -rf mega-drive
mkdir -p mega-drive/public/{auth,explorer,data,storage/users}
mkdir -p mega-drive/src
mkdir -p mega-drive/public/assets/{css,js}

# --- CRITICAL FIX: PRE-CREATE DATABASE FILES ---
# We create these NOW so they exist before Docker even builds.
echo "[]" > mega-drive/public/data/users.json
echo "[]" > mega-drive/public/data/shares.json
echo "{}" > mega-drive/public/data/chats.json
chmod 777 mega-drive/public/data/*.json

# 2. Dockerfile (Permissions Guaranteed)
cat << 'EOF' > mega-drive/Dockerfile
FROM php:8.2-apache
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html/

# CRITICAL: Grant full read/write to the web server for data and storage
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage
RUN chmod -R 777 /var/www/html/public/data /var/www/html/public/storage

# PHP Limits
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/uploads.ini
EXPOSE 80
EOF

# 3. CSS (Responsive & Clean)
cat << 'EOF' > mega-drive/public/assets/css/style.css
:root { --blue: #1a73e8; --bg: #f8f9fa; --border: #dadce0; --text: #3c4043; }
* { box-sizing: border-box; }
body { font-family: 'Segoe UI', Roboto, Helvetica, sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; flex-direction: column; height: 100vh; }
a { text-decoration: none; color: inherit; }
.nav { background: white; border-bottom: 1px solid var(--border); padding: 0 20px; height: 60px; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
.logo { font-size: 20px; font-weight: 500; color: #5f6368; display: flex; align-items: center; gap: 8px; }
.container { flex: 1; overflow-y: auto; padding: 20px; max-width: 1200px; margin: 0 auto; width: 100%; }
.btn { padding: 8px 16px; border-radius: 4px; border: 1px solid var(--border); background: white; cursor: pointer; font-size: 14px; font-weight: 500; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px; }
.btn-primary { background: var(--blue); color: white; border: none; }
.btn-primary:hover { background: #1557b0; }
.btn-danger { color: #d93025; border-color: #f28b82; }
.card { background: white; border: 1px solid var(--border); border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.toolbar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
.file-card { background: white; border: 1px solid var(--border); border-radius: 8px; padding: 15px; text-align: center; cursor: pointer; transition: 0.2s; position: relative; height: 160px; display: flex; flex-direction: column; justify-content: center; align-items: center; }
.file-card:hover { background: #e8f0fe; border-color: var(--blue); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.icon { font-size: 48px; margin-bottom: 10px; display: block; }
.filename { font-size: 13px; font-weight: 500; word-break: break-word; max-width: 100%; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
.meta { font-size: 11px; color: #5f6368; margin-top: 5px; }
input[type="text"], input[type="password"], select { width: 100%; padding: 10px; margin: 8px 0 15px; border: 1px solid var(--border); border-radius: 4px; }
#editor { width: 100%; height: calc(100vh - 60px); }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal { background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
EOF

# 4. JSON Logic (Self-Repairing)
cat << 'EOF' > mega-drive/src/json.php
<?php
define('DATA_PATH', __DIR__ . '/../public/data');
define('STORAGE_PATH', __DIR__ . '/../public/storage');

// --- SELF REPAIR SYSTEM ---
// If the folder is empty or files are missing, recreate them immediately.
if (!file_exists(DATA_PATH)) { mkdir(DATA_PATH, 0777, true); }

if (!file_exists(DATA_PATH.'/users.json')) {
    file_put_contents(DATA_PATH.'/users.json', '[]');
}
if (!file_exists(DATA_PATH.'/shares.json')) {
    file_put_contents(DATA_PATH.'/shares.json', '[]');
}
if (!file_exists(DATA_PATH.'/chats.json')) {
    file_put_contents(DATA_PATH.'/chats.json', '{}');
}

function getJSON($file) {
    $path = DATA_PATH . "/$file.json";
    if (!file_exists($path)) return []; // Should not happen due to self-repair above
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function saveJSON($file, $data) {
    $path = DATA_PATH . "/$file.json";
    // LOCK_EX prevents data corruption
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}
?>
EOF

# 5. Index Redirect
cat << 'EOF' > mega-drive/index.php
<?php
session_start();
if(isset($_SESSION['user'])) { header('Location: /public/explorer/dashboard.php'); exit; }
header('Location: /public/auth/login.php');
?>
EOF

# 6. Auth (Login & Signup)
cat << 'EOF' > mega-drive/public/auth/login.php
<?php
require_once '../../src/json.php'; session_start();
if($_POST){
    $users = getJSON('users');
    foreach($users as $u){
        if($u['u'] === $_POST['u'] && $u['p'] === $_POST['p']){
            $_SESSION['user'] = $_POST['u'];
            header('Location: ../explorer/dashboard.php'); exit;
        }
    }
    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Login</title></head><body>
<div class="modal-overlay">
    <div class="modal">
        <h2>Mega Drive</h2>
        <?php if(isset($error)) echo "<div style='color:red; margin-bottom:10px;'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="u" placeholder="Username" required>
            <input type="password" name="p" placeholder="Password" required>
            <button class="btn btn-primary" style="width:100%">Sign In</button>
        </form>
        <div style="margin-top:15px; font-size:13px;">
            <a href="signup.php" style="color:var(--blue)">Create Account</a>
        </div>
    </div>
</div></body></html>
EOF

cat << 'EOF' > mega-drive/public/auth/signup.php
<?php
require_once '../../src/json.php';
if($_POST){
    $users = getJSON('users');
    foreach($users as $u) { if($u['u'] === $_POST['u']) $error = "Username taken."; }
    
    if(!isset($error)) {
        $users[] = ['u'=>$_POST['u'], 'p'=>$_POST['p']];
        saveJSON('users', $users);
        mkdir(STORAGE_PATH . '/users/' . $_POST['u'], 0777, true);
        header('Location: login.php'); exit;
    }
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Sign Up</title></head><body>
<div class="modal-overlay">
    <div class="modal">
        <h2>Sign Up</h2>
        <?php if(isset($error)) echo "<div style='color:red; margin-bottom:10px;'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="u" placeholder="New Username" required>
            <input type="password" name="p" placeholder="New Password" required>
            <button class="btn btn-primary" style="width:100%">Register</button>
        </form>
        <div style="margin-top:15px; font-size:13px;">
            <a href="login.php" style="color:var(--blue)">Back to Login</a>
        </div>
    </div>
</div></body></html>
EOF

# 7. Dashboard (Main Logic)
cat << 'EOF' > mega-drive/public/explorer/dashboard.php
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
        <a href="public.php" class="btn">🌎 Public</a>
        <a href="shared_find.php" class="btn">🔓 Find Shared</a>
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
            <span class="icon"><?php echo $isDir ? '📁' : ($ext=='chat' ? '💬' : '📄'); ?></span>
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
EOF

# 8. Editor
cat << 'EOF' > mega-drive/public/explorer/editor.php
<?php
require_once '../../src/json.php'; session_start();
if(!isset($_SESSION['user'])) die("Login required");

$file = $_GET['file'];
$owner = $_GET['owner'];
$path = $_GET['path'] ?? $owner; 
$realPath = STORAGE_PATH . "/users/" . $path . "/" . $file;

// Permissions
$canEdit = false;
if($_SESSION['user'] === $owner) {
    $canEdit = true;
} else {
    $shares = getJSON('shares');
    foreach($shares as $s) {
        if($s['file'] === $file && $s['owner'] === $owner) {
            if(isset($s['is_public']) && $s['is_public'] && $s['perm'] === 'edit') $canEdit = true;
            if(isset($_SESSION['unlocked'][$s['alias']]) && $s['perm'] === 'edit') $canEdit = true;
        }
    }
}

$content = file_exists($realPath) ? file_get_contents($realPath) : "";
$ext = pathinfo($file, PATHINFO_EXTENSION);
$langMap = ['html'=>'html', 'php'=>'php', 'js'=>'javascript', 'css'=>'css', 'json'=>'json'];
$lang = $langMap[$ext] ?? 'plaintext';
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script><title>Editor</title></head><body>
<div class="nav">
    <div style="font-weight:bold;"><?php echo $file; ?> <span style="font-size:12px; font-weight:normal"><?php echo $canEdit ? '(Editing)' : '(Read Only)'; ?></span></div>
    <div>
        <span id="status" style="margin-right:15px; font-size:12px; color:green;"></span>
        <?php if($canEdit): ?><button onclick="save()" class="btn btn-primary">Save Changes</button><?php endif; ?>
        <button onclick="history.back()" class="btn">Close</button>
    </div>
</div>
<div id="editor"></div>
<script>
    var editor;
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        editor = monaco.editor.create(document.getElementById('editor'), {
            value: <?php echo json_encode($content); ?>, language: '<?php echo $lang; ?>', readOnly: <?php echo $canEdit ? 'false' : 'true'; ?>, theme: 'vs-light', automaticLayout: true
        });
        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyK, function() {
            let url = prompt("Enter Link URL:");
            if(url) {
                let sel = editor.getSelection();
                let text = editor.getModel().getValueInRange(sel);
                editor.executeEdits("", [{ range: sel, text: `<a href="${url}">${text}</a>` }]);
            }
        });
    });
    function save() {
        document.getElementById('status').innerText = "Saving...";
        let fd = new FormData();
        fd.append('path', '<?php echo $realPath; ?>');
        fd.append('content', editor.getValue());
        fetch('api_save.php', { method: 'POST', body: fd }).then(() => {
            document.getElementById('status').innerText = "Saved!";
            setTimeout(() => document.getElementById('status').innerText = "", 2000);
        });
    }
</script></body></html>
EOF

# 9. Share Setup
cat << 'EOF' > mega-drive/public/explorer/share_setup.php
<?php
require_once '../../src/json.php'; session_start();
$user = $_SESSION['user']; $file = $_GET['file']; $path = $_GET['path'];
$shares = getJSON('shares');

if($_POST) {
    if(isset($_POST['delete'])) {
        foreach($shares as $k => $s) {
            if($s['alias'] === $_POST['del_alias'] && $s['owner'] === $user) unset($shares[$k]);
        }
    } else {
        $shares[] = [
            'owner' => $user, 'path' => $path, 'file' => $file,
            'alias' => $_POST['alias'], 'pass' => $_POST['pass'],
            'perm' => $_POST['perm'], 'is_public' => isset($_POST['is_public'])
        ];
    }
    saveJSON('shares', array_values($shares));
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:500px;">
    <div class="card">
        <h3>Share Settings: <?php echo $file; ?></h3>
        <form method="POST">
            <div style="background:#e8f0fe; padding:10px; border-radius:4px; margin-bottom:10px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="is_public"> <b>List in Public Gallery</b>
                </label>
            </div>
            <label>Share Name (Alias)</label>
            <input type="text" name="alias" placeholder="e.g. ProjectX" required>
            <label>Password (Optional)</label>
            <input type="text" name="pass" placeholder="Leave empty for none">
            <label>Permissions</label>
            <select name="perm"><option value="view">View Only</option><option value="edit">Allow Editing</option></select>
            <button class="btn btn-primary" style="width:100%">Create Link</button>
        </form>
        <hr>
        <h4>Active Links</h4>
        <?php foreach($shares as $s): if($s['file'] === $file && $s['owner'] === $user): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding:10px 0;">
                <div>
                    <b><?php echo $s['alias']; ?></b> 
                    <?php if(isset($s['is_public']) && $s['is_public']) echo "<span style='background:green; color:white; padding:2px 5px; font-size:10px; border-radius:4px;'>PUBLIC</span>"; ?>
                </div>
                <form method="POST" style="margin:0"><input type="hidden" name="delete" value="1"><input type="hidden" name="del_alias" value="<?php echo $s['alias']; ?>"><button class="btn btn-danger" style="font-size:12px; padding:4px 8px;">Unshare</button></form>
            </div>
        <?php endif; endforeach; ?>
        <br><a href="dashboard.php" class="btn">Back</a>
    </div>
</div></body></html>
EOF

# 10. Public Gallery
cat << 'EOF' > mega-drive/public/explorer/public.php
<?php require_once '../../src/json.php'; $shares = getJSON('shares'); ?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Public Gallery</title></head><body>
<div class="nav">
    <div class="logo">🌎 Public Gallery</div>
    <a href="dashboard.php" class="btn">Back to Drive</a>
</div>
<div class="container">
    <div class="grid">
        <?php foreach($shares as $s): if(isset($s['is_public']) && $s['is_public']): 
             $link = "editor.php?file=" . urlencode($s['file']) . "&owner=" . urlencode($s['owner']) . "&path=" . urlencode($s['path']);
        ?>
        <div class="file-card" onclick="location.href='<?php echo $link; ?>'">
            <span class="icon">📄</span>
            <div class="filename"><?php echo $s['file']; ?></div>
            <div class="meta">Owner: <?php echo $s['owner']; ?></div>
            <div class="meta" style="color:green">Public Access</div>
        </div>
        <?php endif; endforeach; ?>
    </div>
</div></body></html>
EOF

# 11. Find Shared
cat << 'EOF' > mega-drive/public/explorer/shared_find.php
<?php
require_once '../../src/json.php'; session_start();
if($_POST) {
    $shares = getJSON('shares');
    foreach($shares as $s) {
        if($s['alias'] === $_POST['alias'] && $s['pass'] === $_POST['pass']) {
            $_SESSION['unlocked'][$s['alias']] = true;
            header("Location: editor.php?file=" . urlencode($s['file']) . "&owner=" . urlencode($s['owner']) . "&path=" . urlencode($s['path']));
            exit;
        }
    }
    $err = "Invalid Alias or Password";
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; padding-top:50px;">
    <div class="card">
        <h3>Access Shared File</h3>
        <?php if(isset($err)) echo "<p style='color:red'>$err</p>"; ?>
        <form method="POST">
            <input type="text" name="alias" placeholder="Share Name (Alias)" required>
            <input type="password" name="pass" placeholder="Password (if any)">
            <button class="btn btn-primary" style="width:100%">Unlock</button>
        </form>
        <br><a href="dashboard.php" class="btn">Back</a>
    </div>
</div></body></html>
EOF

# 12. Chat (Fix: Initialize array correctly)
cat << 'EOF' > mega-drive/public/explorer/chat.php
<?php
require_once '../../src/json.php'; session_start();
$id = $_GET['id'];
$allChats = getJSON('chats');

if(isset($_POST['msg']) && !empty($_POST['msg'])) {
    if(!isset($allChats[$id])) $allChats[$id] = [];
    $allChats[$id][] = [
        'u' => $_SESSION['user'],
        'm' => $_POST['msg'],
        't' => date('H:i')
    ];
    saveJSON('chats', $allChats);
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav">
    <div class="logo">💬 Chat: <?php echo $id; ?></div>
    <a href="dashboard.php" class="btn">Exit</a>
</div>
<div class="container">
    <div id="chatbox" class="card" style="height: calc(100vh - 160px); overflow-y: auto; background:#f5f5f5; display:flex; flex-direction:column; gap:10px;">
        <?php 
        if(isset($allChats[$id])) {
            foreach($allChats[$id] as $msg) {
                $isMe = $msg['u'] === $_SESSION['user'];
                echo "<div style='align-self:" . ($isMe ? 'flex-end' : 'flex-start') . "; background:" . ($isMe ? '#d2e3fc' : 'white') . "; padding:8px 12px; border-radius:12px; max-width:70%; box-shadow:0 1px 2px rgba(0,0,0,0.1);'>";
                echo "<div style='font-size:10px; color:#555; margin-bottom:2px;'><b>" . $msg['u'] . "</b> " . $msg['t'] . "</div>";
                echo "<div>" . htmlspecialchars($msg['m']) . "</div>";
                echo "</div>";
            }
        }
        ?>
    </div>
    <form method="POST" style="display:flex; gap:10px;">
        <input type="text" name="msg" placeholder="Type a message..." autocomplete="off" style="margin:0;" autofocus required>
        <button class="btn btn-primary">Send</button>
    </form>
</div>
<script>
    var box = document.getElementById('chatbox');
    box.scrollTop = box.scrollHeight;
    setTimeout(() => location.reload(), 3000);
</script></body></html>
EOF

# 13. API Save
cat << 'EOF' > mega-drive/public/explorer/api_save.php
<?php
if(isset($_POST['path']) && isset($_POST['content'])) {
    file_put_contents($_POST['path'], $_POST['content']);
    echo "OK";
}
?>
EOF

# 14. Logout
cat << 'EOF' > mega-drive/public/auth/logout.php
<?php session_start(); session_destroy(); header('Location: /'); ?>
EOF

echo "Initialization Complete. All Data files created. Run 'cd mega-drive' and deploy."
</body>
</html>