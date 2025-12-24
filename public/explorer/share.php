<?php
require_once '../../src/auth.php';
requireLogin();

$file = $_GET['file'];
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shareUser = $_POST['username'];
    $permission = $_POST['permission']; // 'view' or 'edit'
    $password = $_POST['password'];
    
    if (empty($password)) {
        $password = generateRandomString(4);
    }

    $shares = getShares();
    $shares[] = [
        'owner' => $user,
        'filename' => $file,
        'shared_with' => $shareUser,
        'permission' => $permission, // public/private logic simplified to user target
        'password' => $password,
        'is_public' => isset($_POST['is_public'])
    ];
    saveShares($shares);
    
    $success = "Shared with $shareUser! Password: $password";
}

$users = getUsers();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Share File: <?php echo htmlspecialchars($file); ?></h2>
    <?php if(isset($success)) echo "<div class='alert' style='background:#d4edda;color:#155724'>$success</div>"; ?>
    
    <form method="POST">
        <label>Share with Username:</label>
        <select name="username">
            <?php foreach($users as $u): ?>
                <?php if($u['username'] !== $user): ?>
                    <option value="<?php echo $u['username']; ?>"><?php echo $u['username']; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        
        <label>Permission:</label>
        <select name="permission">
            <option value="view">View Only</option>
            <option value="edit">Can Edit</option>
        </select>

        <label>Password (Optional - default random 4 chars):</label>
        <input type="text" name="password" placeholder="Leave empty for random">
        
        <label>
            <input type="checkbox" name="is_public" style="width:auto;"> Make Public (Anyone with link/pass)
        </label>
        <br><br>
        <button type="submit" class="btn">Share</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
