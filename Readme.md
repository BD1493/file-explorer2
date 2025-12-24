# 1. Create Directory Structure
rm -rf file-explorer # clear old if exists
mkdir -p file-explorer/public/assets/css
mkdir -p file-explorer/public/assets/js
mkdir -p file-explorer/public/auth
mkdir -p file-explorer/public/explorer
mkdir -p file-explorer/public/data
mkdir -p file-explorer/public/storage/users
mkdir -p file-explorer/src

# 2. Create Dockerfile
cat << 'EOF' > file-explorer/Dockerfile
FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory to root
WORKDIR /var/www/html

# Copy all files to the container
COPY . /var/www/html/

# Configure Upload Limits
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini

# PERMISSIONS:
# Publicly accessible data and storage, full 777 permissions
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage \
    && chmod -R 777 /var/www/html/public/data /var/www/html/public/storage

# Expose port 80
EXPOSE 80
EOF

# 3. Create Root index.php
cat << 'EOF' > file-explorer/index.php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>
    <div class="container" style="text-align:center; margin-top:50px;">
        <h1>File Explorer</h1>
        <br>
        <a href="/public/auth/login.php" class="btn">File Explorer</a>
    </div>
</body>
</html>
EOF

# 4. Create src/json.php
cat << 'EOF' > file-explorer/src/json.php
<?php
define('BASE_PATH', dirname(__DIR__)); 
define('DATA_PATH', BASE_PATH . '/public/data');
define('STORAGE_PATH', BASE_PATH . '/public/storage');

function getJSON($filename) {
    $path = DATA_PATH . '/' . $filename;
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    return json_decode($json, true) ?? [];
}

function saveJSON($filename, $data) {
    $path = DATA_PATH . '/' . $filename;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
}

function getUsers() { return getJSON('users.json'); }
function saveUsers($users) { saveJSON('users.json', $users); }

function getShares() { return getJSON('shares.json'); }
function saveShares($shares) { saveJSON('shares.json', $shares); }
?>
EOF

# 5. Create src/auth.php
cat << 'EOF' > file-explorer/src/auth.php
<?php
session_start();
require_once __DIR__ . '/json.php';

function isLoggedIn() { return isset($_SESSION['user']); }

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /public/auth/login.php');
        exit;
    }
}

function currentUser() { return $_SESSION['user'] ?? null; }
?>
EOF

# 6. Create src/permissions.php
cat << 'EOF' > file-explorer/src/permissions.php
<?php
require_once __DIR__ . '/json.php';

function canAccess($username, $filename, $owner) {
    // 1. Owner always access
    if ($username === $owner) return 'edit';
    
    // 2. Check Unlocked Shares (Session based)
    if (isset($_SESSION['unlocked_shares'])) {
        foreach ($_SESSION['unlocked_shares'] as $s) {
            if ($s['filename'] === $filename && $s['owner'] === $owner) {
                return $s['permission']; // 'edit' or 'view'
            }
        }
    }

    // 3. Check Public Shares
    $shares = getShares();
    foreach ($shares as $s) {
        if ($s['filename'] === $filename && $s['owner'] === $owner && $s['is_public']) {
            return $s['permission'];
        }
    }
    
    return false;
}
?>
EOF

# 7. Create CSS
cat << 'EOF' > file-explorer/public/assets/css/style.css
body { font-family: sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
.container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
h1, h2 { color: #333; }
input, select, textarea { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
.btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border: none; cursor: pointer; border-radius: 4px; font-size:14px; }
.btn:hover { background: #0056b3; }
.btn-secondary { background: #6c757d; }
.file-list { list-style: none; padding: 0; }
.file-item { background: #eee; padding: 10px; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center; }
.alert { padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 10px; border-radius: 4px;}
.nav { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
.nav a { margin-right: 15px; text-decoration: none; color: #333; font-weight: bold; }
.hidden { display: none; }
EOF

# 8. Create JS
cat << 'EOF' > file-explorer/public/assets/js/app.js
function toggleShareFields() {
    var isPublic = document.getElementById('is_public').checked;
    var privateFields = document.getElementById('private_fields');
    if (isPublic) {
        privateFields.style.display = 'none';
        document.getElementById('share_name').required = false;
        document.getElementById('share_pass').required = false;
    } else {
        privateFields.style.display = 'block';
        document.getElementById('share_name').required = true;
    }
}
EOF

# 9. JSON Data Files
echo "[]" > file-explorer/public/data/users.json
echo "[]" > file-explorer/public/data/shares.json
echo "[]" > file-explorer/public/data/requests.json

# 10. Auth Files
cat << 'EOF' > file-explorer/public/auth/signup.php
<?php
require_once '../../src/json.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = getUsers();
    $username = $_POST['username'];
    $password = $_POST['password'];
    foreach ($users as $u) {
        if ($u['username'] === $username) { $error = "Username taken."; break; }
    }
    if (!isset($error)) {
        $users[] = ['username' => $username, 'password' => $password];
        saveUsers($users);
        mkdir(STORAGE_PATH . '/users/' . $username, 0777, true);
        $_SESSION['user'] = $username;
        header('Location: ../explorer/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container"><h2>Sign Up</h2>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button class="btn">Sign Up</button></form>
</div></body></html>
EOF

cat << 'EOF' > file-explorer/public/auth/login.php
<?php
require_once '../../src/json.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = getUsers();
    foreach ($users as $u) {
        if ($u['username'] === $_POST['username'] && $u['password'] === $_POST['password']) {
            $_SESSION['user'] = $_POST['username'];
            header('Location: ../explorer/dashboard.php');
            exit;
        }
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container"><h2>Login</h2>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button class="btn">Login</button></form>
    <p><a href="signup.php">Sign Up</a></p>
</div></body></html>
EOF

cat << 'EOF' > file-explorer/public/auth/logout.php
<?php session_start(); session_destroy(); header('Location: /index.php'); ?>
EOF

# 11. Explorer Files

# Dashboard (Removed links to JSON)
cat << 'EOF' > file-explorer/public/explorer/dashboard.php
<?php
require_once '../../src/auth.php';
requireLogin();
$user = currentUser();
$userDir = STORAGE_PATH . '/users/' . $user;
if (!file_exists($userDir)) mkdir($userDir, 0777, true);
$files = array_diff(scandir($userDir), array('.', '..'));
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <div class="nav">
        <span>Hi, <b><?php echo htmlspecialchars($user); ?></b></span>
        <a href="shared.php" style="margin-left:20px;">Shared Files</a>
        <a href="../auth/logout.php" style="float:right; color:red;">Logout</a>
    </div>
    <div style="margin-bottom: 20px;">
        <a href="create.php" class="btn">Make File</a>
        <a href="upload.php" class="btn btn-secondary">Upload File</a>
    </div>
    <h3>My Files</h3>
    <ul class="file-list">
        <?php foreach ($files as $file): ?>
            <li class="file-item">
                <span><?php echo htmlspecialchars($file); ?></span>
                <div>
                    <a href="edit.php?file=<?php echo urlencode($file); ?>" class="btn">Edit</a>
                    <a href="share.php?file=<?php echo urlencode($file); ?>" class="btn btn-secondary">Share</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
EOF

# Create
cat << 'EOF' > file-explorer/public/explorer/create.php
<?php
require_once '../../src/auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $_POST['filename']);
    if (pathinfo($filename, PATHINFO_EXTENSION) == '') $filename .= '.txt';
    file_put_contents(STORAGE_PATH . "/users/" . currentUser() . "/$filename", $_POST['content']);
    header("Location: dashboard.php"); exit;
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container"><h2>Make File</h2>
    <form method="POST">
        <input type="text" name="filename" placeholder="File Name" required>
        <textarea name="content" rows="10" placeholder="Content..."></textarea>
        <button class="btn">Save</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div></body></html>
EOF

# Upload
cat << 'EOF' > file-explorer/public/explorer/upload.php
<?php
require_once '../../src/auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], STORAGE_PATH . "/users/" . currentUser() . "/" . basename($_FILES['file']['name']));
    header('Location: dashboard.php'); exit;
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container"><h2>Upload File</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required><br><br>
        <button class="btn">Upload</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div></body></html>
EOF

# Share (UPDATED LOGIC: Share Name + Toggle Public)
cat << 'EOF' > file-explorer/public/explorer/share.php
<?php
require_once '../../src/auth.php';
requireLogin();
$file = $_GET['file'];
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isPublic = isset($_POST['is_public']);
    $shareName = $_POST['share_name'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // If public, we generate a random share name to identify it easily or use filename
    if ($isPublic) {
        $shareName = "public_" . uniqid(); 
        $password = "";
    }

    $shares = getShares();
    $shares[] = [
        'owner' => $user,
        'filename' => $file,
        'share_name' => $shareName, // The "username/alias" you give to a friend
        'password' => $password,
        'permission' => $_POST['permission'], // view or edit
        'is_public' => $isPublic
    ];
    saveShares($shares);
    
    if($isPublic) {
        $success = "File is Public! Share Name ID: $shareName";
    } else {
        $success = "Shared! Give your friend this Name: '$shareName' and Password: '$password'";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/app.js"></script>
</head>
<body>
<div class="container">
    <h2>Share: <?php echo htmlspecialchars($file); ?></h2>
    <?php if(isset($success)) echo "<div class='alert' style='background:#d4edda;color:#155724'>$success</div>"; ?>
    
    <form method="POST">
        <label>Permission:</label>
        <select name="permission">
            <option value="view">View Only</option>
            <option value="edit">Edit</option>
        </select>
        <br><br>
        
        <label>
            <input type="checkbox" id="is_public" name="is_public" onclick="toggleShareFields()"> 
            Make Public (No password needed)
        </label>
        <br><br>

        <div id="private_fields">
            <label>Share Name (Give this name to your friend):</label>
            <input type="text" id="share_name" name="share_name" placeholder="e.g. myproject">
            
            <label>Password (Optional):</label>
            <input type="text" id="share_pass" name="password" placeholder="Secret123">
        </div>
        
        <button type="submit" class="btn">Share File</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
EOF

# Shared (UPDATED LOGIC: Enter Share Name + Password)
cat << 'EOF' > file-explorer/public/explorer/shared.php
<?php
require_once '../../src/auth.php';
requireLogin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputName = $_POST['share_name'];
    $inputPass = $_POST['share_pass'];
    
    $shares = getShares();
    $found = false;
    
    foreach ($shares as $s) {
        // Match Share Name
        if ($s['share_name'] === $inputName) {
            // Check Password (if not public)
            if ($s['is_public'] || $s['password'] === $inputPass) {
                // Success! Unlock it for this session
                if (!isset($_SESSION['unlocked_shares'])) {
                    $_SESSION['unlocked_shares'] = [];
                }
                // Avoid duplicates
                $_SESSION['unlocked_shares'] = array_filter($_SESSION['unlocked_shares'], function($item) use ($s) {
                    return $item['share_name'] !== $s['share_name'];
                });
                $_SESSION['unlocked_shares'][] = $s;
                $found = true;
            }
        }
    }
    
    if (!$found) {
        $error = "Invalid Share Name or Password.";
        $showRequest = true;
    }
}

$unlocked = $_SESSION['unlocked_shares'] ?? [];
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Access Shared Files</h2>
    <a href="dashboard.php" class="btn btn-secondary">Back</a>
    <hr>
    
    <div style="background:#f9f9f9; padding:15px; border-radius:5px; margin-bottom:20px;">
        <h3>Find a File</h3>
        <?php if($error) echo "<div class='alert'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="share_name" placeholder="Enter Share Name (e.g. myproject)" required>
            <input type="text" name="share_pass" placeholder="Enter Password (if private)">
            <button class="btn">Get File</button>
        </form>
        
        <?php if(isset($showRequest)): ?>
            <br>
            <p>Access Denied. <a href="request.php" class="btn" style="background:orange;">Request Access</a></p>
        <?php endif; ?>
    </div>

    <h3>Unlocked Files</h3>
    <ul class="file-list">
        <?php foreach ($unlocked as $share): ?>
            <li class="file-item">
                <span><b><?php echo htmlspecialchars($share['filename']); ?></b> (<?php echo $share['permission']; ?>)</span>
                <a href="edit.php?file=<?php echo urlencode($share['filename']); ?>&owner=<?php echo urlencode($share['owner']); ?>" class="btn">Open</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
EOF

# Request
cat << 'EOF' > file-explorer/public/explorer/request.php
<?php
require_once '../../src/auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reqs = getJSON('requests.json');
    $reqs[] = ['from'=>currentUser(),'note'=>$_POST['note']];
    saveJSON('requests.json', $reqs);
    $success = "Request Sent.";
}
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <h2>Request Access</h2>
    <?php if(isset($success)) echo "<div class='alert'>$success</div>"; ?>
    <form method="POST">
        <textarea name="note" placeholder="Why do you want access?" required></textarea>
        <button class="btn">Send</button>
        <a href="shared.php" class="btn btn-secondary">Back</a>
    </form>
</div></body></html>
EOF

# Edit
cat << 'EOF' > file-explorer/public/explorer/edit.php
<?php
require_once '../../src/auth.php';
require_once '../../src/permissions.php';
requireLogin();

$user = currentUser();
$file = $_GET['file'];
$owner = $_GET['owner'] ?? $user;
$filePath = STORAGE_PATH . "/users/$owner/$file";

// Permissions Check
$access = canAccess($user, $file, $owner);

if (!$access) {
    die("<h1>Access Denied</h1><a href='dashboard.php'>Back</a>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $access === 'edit') {
    file_put_contents($filePath, $_POST['content']);
    $success = "Saved!";
}

$content = file_exists($filePath) ? file_get_contents($filePath) : "";
?>
<!DOCTYPE html>
<html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="container">
    <h2>Edit: <?php echo htmlspecialchars($file); ?></h2>
    <?php if(isset($success)) echo "<div class='alert' style='background:#d4edda;color:#155724'>$success</div>"; ?>
    <form method="POST">
        <textarea name="content" rows="15" <?php if($access!=='edit') echo 'readonly'; ?>><?php echo htmlspecialchars($content); ?></textarea>
        <?php if($access==='edit'): ?>
            <button class="btn">Save</button>
        <?php else: ?>
            <p><i>View Only Mode</i></p>
        <?php endif; ?>
        <a href="<?php echo ($owner===$user)?'dashboard.php':'shared.php'; ?>" class="btn btn-secondary">Back</a>
    </form>
</div></body></html>
EOF

echo "Project updated! Push to GitHub and Deploy."


copy the code above for ceating your own file explorer. Make sure to give credits!