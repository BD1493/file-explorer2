# 1. Setup the folder structure
rm -rf file-explorer
mkdir -p file-explorer/public/assets/{css,js}
mkdir -p file-explorer/public/{auth,explorer,data,storage/users}
mkdir -p file-explorer/src

# 2. Dockerfile for Render
cat << 'EOF' > file-explorer/Dockerfile
FROM php:8.2-apache
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html/
RUN echo "upload_max_filesize=50M\npost_max_size=50M" > /usr/local/etc/php/conf.d/uploads.ini
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage && chmod -R 777 /var/www/html/public/data /var/www/html/public/storage
EXPOSE 80
EOF

# 3. Logic: JSON and Permissions
cat << 'EOF' > file-explorer/src/json.php
<?php
define('BASE_PATH', dirname(__DIR__)); 
define('DATA_PATH', BASE_PATH . '/public/data');
define('STORAGE_PATH', BASE_PATH . '/public/storage');
function getJSON($f) { $p=DATA_PATH."/$f"; return file_exists($p)?json_decode(file_get_contents($p),true)??[]:[]; }
function saveJSON($f,$d) { file_put_contents(DATA_PATH."/$f", json_encode($d, JSON_PRETTY_PRINT)); }
?>
EOF

cat << 'EOF' > file-explorer/src/auth.php
<?php
session_start();
require_once __DIR__ . '/json.php';
function isLoggedIn() { return isset($_SESSION['user']); }
function requireLogin() { if (!isLoggedIn()) { header('Location: /public/auth/login.php'); exit; } }
function currentUser() { return $_SESSION['user'] ?? null; }
?>
EOF

cat << 'EOF' > file-explorer/src/permissions.php
<?php
require_once __DIR__ . '/json.php';
function canAccess($username, $filename, $owner) {
    if ($username === $owner) return 'edit';
    $shares = getJSON('shares.json');
    if (isset($_SESSION['unlocked_shares'])) {
        foreach ($_SESSION['unlocked_shares'] as $s) {
            if ($s['filename'] === $filename && $s['owner'] === $owner) return $s['permission'];
        }
    }
    foreach ($shares as $s) {
        if ($s['filename'] === $filename && $s['owner'] === $owner && ($s['is_public'] ?? false)) return $s['permission'];
    }
    return false;
}
?>
EOF

# 4. Global CSS (Google Drive Aesthetic)
cat << 'EOF' > file-explorer/public/assets/css/style.css
body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
.container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.nav { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
.btn { padding: 10px 18px; border-radius: 5px; border: none; cursor: pointer; text-decoration: none; font-size: 14px; display: inline-block; transition: 0.2s; }
.btn-primary { background: #1a73e8; color: white; }
.btn-secondary { background: #f1f3f4; color: #3c4043; border: 1px solid #dadce0; }
.btn-danger { background: #ea4335; color: white; }
.editor-container { height: 75vh; display: flex; flex-direction: column; }
.editor-textarea { flex-grow: 1; border: 1px solid #dadce0; padding: 60px; font-size: 16px; resize: none; outline: none; line-height: 1.6; font-family: 'Courier New', monospace; }
.file-item { border-bottom: 1px solid #eee; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
input, select { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #dadce0; border-radius: 4px; box-sizing: border-box; }
.alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; background: #e6f4ea; color: #137333; }
EOF

# 5. Auth Pages
cat << 'EOF' > file-explorer/public/auth/login.php
<?php require_once '../../src/json.php'; session_start();
if($_POST){
    foreach(getJSON('users.json') as $u) {
        if($u['username']===$_POST['u'] && $u['password']===$_POST['p']){
            $_SESSION['user']=$_POST['u']; header('Location: ../explorer/dashboard.php'); exit;
        }
    }
    $err = "Invalid login.";
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; margin-top:100px;">
    <h2>Login</h2>
    <?php if(isset($err)) echo "<div class='alert' style='background:#fce8e6; color:#c5221f;'>$err</div>"; ?>
    <form method="POST"><input type="text" name="u" placeholder="Username" required><input type="password" name="p" placeholder="Password" required><button class="btn btn-primary">Login</button></form>
    <p>New? <a href="signup.php">Create Account</a></p>
</div></body></html>
EOF

cat << 'EOF' > file-explorer/public/auth/signup.php
<?php require_once '../../src/json.php';
if($_POST){
    $users = getJSON('users.json');
    $users[] = ['username'=>$_POST['u'], 'password'=>$_POST['p']];
    saveJSON('users.json', $users);
    mkdir(STORAGE_PATH.'/users/'.$_POST['u'], 0777, true);
    header('Location: login.php'); exit;
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px; margin-top:100px;">
    <h2>Sign Up</h2>
    <form method="POST"><input type="text" name="u" placeholder="Username" required><input type="password" name="p" placeholder="Password" required><button class="btn btn-primary">Register</button></form>
</div></body></html>
EOF

# 6. Dashboard
cat << 'EOF' > file-explorer/public/explorer/dashboard.php
<?php require_once '../../src/auth.php'; requireLogin();
$user = currentUser();
$userDir = STORAGE_PATH . '/users/' . $user;
if (!file_exists($userDir)) mkdir($userDir, 0777, true);
$files = array_diff(scandir($userDir), array('.', '..')); ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <div class="nav">
        <div><strong>My Drive</strong> (<?php echo $user; ?>)</div>
        <div>
            <a href="public_files.php" class="btn btn-secondary">Gallery</a>
            <a href="shared.php" class="btn btn-secondary">Access Shared</a>
            <a href="manage_shares.php" class="btn btn-secondary">Shares</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
    <a href="create.php" class="btn btn-primary" style="margin-bottom:20px;">+ New File</a>
    <div class="file-list">
        <?php foreach($files as $f): ?>
            <div class="file-item">
                <span>📄 <?php echo htmlspecialchars($f); ?></span>
                <div>
                    <a href="edit.php?file=<?php echo urlencode($f); ?>" class="btn btn-secondary">Open</a>
                    <a href="share.php?file=<?php echo urlencode($f); ?>" class="btn btn-primary">Share</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div></body></html>
EOF

# 7. Create and Manage Shares
cat << 'EOF' > file-explorer/public/explorer/create.php
<?php require_once '../../src/auth.php'; requireLogin();
if($_POST){
    file_put_contents(STORAGE_PATH."/users/".currentUser()."/{$_POST['n']}.txt", "");
    header("Location: dashboard.php"); exit;
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container" style="max-width:400px;">
    <h2>New File</h2>
    <form method="POST"><input type="text" name="n" placeholder="Filename" required><button class="btn btn-primary">Create</button></form>
</div></body></html>
EOF

cat << 'EOF' > file-explorer/public/explorer/share.php
<?php require_once '../../src/auth.php'; requireLogin();
if($_POST){
    $s = getJSON('shares.json');
    $s[] = ['owner'=>currentUser(), 'filename'=>$_GET['file'], 'share_name'=>$_POST['sn'], 'password'=>$_POST['sp'], 'permission'=>$_POST['p'], 'is_public'=>isset($_POST['pub'])];
    saveJSON('shares.json', $s);
    $msg = "File Shared!";
} ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css">
<script>function upd(){ let p=document.getElementById('pub').checked; document.getElementById('priv').style.display=p?'none':'block'; }</script></head><body>
<div class="container" style="max-width:500px;">
    <h2>Share Settings</h2>
    <?php if(isset($msg)) echo "<div class='alert'>$msg</div>"; ?>
    <form method="POST">
        <label><input type="checkbox" name="pub" id="pub" onchange="upd()"> Make Public (Everyone can see)</label>
        <div id="priv">
            <input type="text" name="sn" placeholder="Share Alias (Friend uses this)">
            <input type="text" name="sp" placeholder="Password (Optional)">
        </div>
        <select name="p"><option value="view">View Only</option><option value="edit">Can Edit</option></select>
        <button class="btn btn-primary">Confirm Share</button> <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div></body></html>
EOF

cat << 'EOF' > file-explorer/public/explorer/manage_shares.php
<?php require_once '../../src/auth.php'; requireLogin();
$s = getJSON('shares.json');
if(isset($_GET['del'])){ array_splice($s, $_GET['del'], 1); saveJSON('shares.json', $s); header("Location: manage_shares.php"); exit; } ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <div class="nav"><h2>My Active Shares</h2><a href="dashboard.php" class="btn btn-secondary">Back</a></div>
    <?php foreach($s as $i=>$sh): if($sh['owner']===currentUser()): ?>
        <div class="file-item">
            <span><b><?php echo $sh['filename']; ?></b> (Alias: <?php echo $sh['share_name']?:'Public'; ?>)</span>
            <a href="?del=<?php echo $i; ?>" class="btn btn-danger">Unshare</a>
        </div>
    <?php endif; endforeach; ?>
</div></body></html>
EOF

# 8. Editor (Auto-Save + PDF)
cat << 'EOF' > file-explorer/public/explorer/edit.php
<?php require_once '../../src/auth.php'; require_once '../../src/permissions.php'; requireLogin();
$user = currentUser(); $file = $_GET['file']; $owner = $_GET['owner'] ?? $user;
$access = canAccess($user, $file, $owner);
if (!$access) die("Denied.");
$path = STORAGE_PATH . "/users/$owner/$file";
$content = file_exists($path) ? file_get_contents($path) : ""; ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script></head>
<body><div class="container" style="max-width: 1100px;">
    <div class="nav">
        <h2><?php echo htmlspecialchars($file); ?> <small id="status" style="font-weight:normal; font-size:12px; color:gray;"><?php echo ($access==='view')?'(View Only)':''; ?></small></h2>
        <div><button onclick="dlPDF()" class="btn btn-secondary">Download PDF</button> <a href="dashboard.php" class="btn btn-secondary">Close</a></div>
    </div>
    <div id="pdf-area" style="display:none; padding:50px; white-space:pre-wrap; font-family: serif;"></div>
    <div class="editor-container"><textarea id="editor" class="editor-textarea" <?php echo ($access==='view')?'readonly':''; ?>><?php echo htmlspecialchars($content); ?></textarea></div>
</div>
<script>
function dlPDF() { const e = document.getElementById('pdf-area'); e.innerText = document.getElementById('editor').value; e.style.display='block'; html2pdf().from(e).save().then(()=>e.style.display='none'); }
<?php if($access === 'edit'): ?>
let t; document.getElementById('editor').addEventListener('input', () => {
    document.getElementById('status').innerText = "Typing...";
    clearTimeout(t);
    t = setTimeout(() => {
        document.getElementById('status').innerText = "Saving...";
        let fd = new FormData(); fd.append('file','<?php echo $file; ?>'); fd.append('owner','<?php echo $owner; ?>'); fd.append('content', document.getElementById('editor').value);
        fetch('api_save.php', {method:'POST', body:fd}).then(r=>r.json()).then(d=>{ document.getElementById('status').innerText = "Saved at " + d.time; });
    }, 1500);
});
<?php endif; ?>
</script></body></html>
EOF

# 9. Background Save API
cat << 'EOF' > file-explorer/public/explorer/api_save.php
<?php require_once '../../src/auth.php'; require_once '../../src/permissions.php'; requireLogin();
if($_POST){
    $access = canAccess(currentUser(), $_POST['file'], $_POST['owner']);
    if($access === 'edit'){
        file_put_contents(STORAGE_PATH."/users/{$_POST['owner']}/{$_POST['file']}", $_POST['content']);
        echo json_encode(['status'=>'success', 'time'=>date('H:i:s')]); exit;
    }
} echo json_encode(['status'=>'error']);
EOF

# 10. Gallery and Finder
cat << 'EOF' > file-explorer/public/explorer/public_files.php
<?php require_once '../../src/auth.php'; $s = getJSON('shares.json'); ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <div class="nav"><h2>Public Gallery</h2><a href="dashboard.php" class="btn btn-secondary">Back</a></div>
    <?php foreach($s as $sh): if($sh['is_public']): ?>
        <div class="file-item">
            <span><?php echo $sh['filename']; ?> (by <?php echo $sh['owner']; ?>)</span>
            <a href="edit.php?file=<?php echo urlencode($sh['filename']); ?>&owner=<?php echo urlencode($sh['owner']); ?>" class="btn btn-primary">Open</a>
        </div>
    <?php endif; endforeach; ?>
</div></body></html>
EOF

cat << 'EOF' > file-explorer/public/explorer/shared.php
<?php require_once '../../src/auth.php'; requireLogin();
if($_POST){
    $shares = getJSON('shares.json'); $found = false;
    foreach($shares as $s){
        if($s['share_name']===$_POST['sn'] && ($s['is_public'] || $s['password']===$_POST['sp'])){
            $_SESSION['unlocked_shares'][] = $s; $found = true;
        }
    }
    if(!$found) $err = "Not found.";
} $unl = $_SESSION['unlocked_shares'] ?? []; ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <div class="nav"><h2>Find Private Share</h2><a href="dashboard.php" class="btn btn-secondary">Back</a></div>
    <form method="POST"><input type="text" name="sn" placeholder="Share Alias"><input type="text" name="sp" placeholder="Password"><button class="btn btn-primary">Unlock</button></form>
    <h3>Unlocked:</h3>
    <?php foreach($unl as $u): ?>
        <div class="file-item"><span><?php echo $u['filename']; ?></span> <a href="edit.php?file=<?php echo urlencode($u['filename']); ?>&owner=<?php echo urlencode($u['owner']); ?>" class="btn btn-secondary">Open</a></div>
    <?php endforeach; ?>
</div></body></html>
EOF

# 11. Core Files
echo "[]" > file-explorer/public/data/users.json
echo "[]" > file-explorer/public/data/shares.json
cat << 'EOF' > file-explorer/index.php
<?php header('Location: public/auth/login.php'); ?>
EOF
cat << 'EOF' > file-explorer/public/auth/logout.php
<?php session_start(); session_destroy(); header('Location: login.php'); ?>
EOF

echo "Done! Run: cd file-explorer && git init && git add . && git commit -m 'Launch'"